<template>
    <div>
        <h4>Durchgeführte Operationen</h4>
        <div class="chart-container">
            <div class="chart" :id="elementId"></div>
            <ul class="legend mt-3">
                <li v-if="isModerator" v-for="operation, key of operations" :key="key">
                    <i class="fa fa-square" :style="{ color: operationsColors(key) }"></i>
                    {{ operation }}
                </li>
                <li v-if="!isModerator">
                    <i class="fa fa-square" style="color: #d3d3d3"></i>
                    Durchschnitt
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import * as d3 from "d3";
import Communication from "../../../classes/communication";
import store from "../../../store";

export default {
    name: "operations_bar",
    props: {
        id: String,
        isMock: Boolean,
    },
    data() {
        return {
            authorsToOperations: [],
            colors: [
                "#2ECC71", // edit
                "#3498DB", // write
                "#8E44AD", // paste
                "#E74C3C", // delete
            ],
            operations: [
                "edit",
                "write",
                "paste",
                "delete"
            ],
            isModerator: false,
        };
    },
    computed: {
        elementId() {
            return `operations-bar-student-${this.id}`;
        },
        operationsColors() {
            if (!this.colors || !this.colors.length) {
                return null;
            }
            return d3.scaleOrdinal(this.colors);
        },
        users() {
            return this.$store.state.users.users;
        },
    },
    methods: {
        /**
         * Get data from EVA and sum up edits, writes, pastes and deletes for each author.
         */
        async getData() {
            return Communication.getFromEVA(`activity/operations/${store.state.base.padName}`,
                { padName: store.state.base.padName })
                .then(res => {
                    for (let entry of res) {
                        // check format {EDIT: number, WRITE: number, PASTE: number, DELETE: number}
                        for (let key in entry.authorToOperations) {
                            if (entry.authorToOperations[key].EDIT === undefined) {
                                entry.authorToOperations[key].EDIT = 0;
                            }
                            if (entry.authorToOperations[key].WRITE === undefined) {
                                entry.authorToOperations[key].WRITE = 0;
                            }
                            if (entry.authorToOperations[key].PASTE === undefined) {
                                entry.authorToOperations[key].PASTE = 0;
                            }
                            if (entry.authorToOperations[key].DELETE === undefined) {
                                entry.authorToOperations[key].DELETE = 0;
                            }
                        }
                        let keys = Object.keys(entry.authorToOperations);
                        for (let key of keys) {
                            // add author if not already in authorsToOperations
                            if (!this.authorsToOperations.some(obj => {
                                return Object.keys(obj).toString() === key;
                            })) {
                                this.authorsToOperations.push(
                                    {
                                        [key]: {
                                            edit: 0,
                                            write: 0,
                                            paste: 0,
                                            delete: 0,
                                        }
                                    }
                                );
                            }
                            // sum up authors operations
                            let author = this.authorsToOperations.find(obj => {
                                return Object.keys(obj).toString() === key;
                            });
                            author[key].edit += entry.authorToOperations[key].EDIT;
                            author[key].write += entry.authorToOperations[key].WRITE;
                            author[key].paste += entry.authorToOperations[key].PASTE;
                            author[key].delete += entry.authorToOperations[key].DELETE;
                        }
                    }
                })
                .catch(() => {
                    store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
                });
        },
        /**
         * Load the chart.
         */
        async loadBar() {
            let data = [];
            let dataValues = [];
            // get authors info
            let authorsInfo = await Communication.getFromEVA("minimap/authorInfo")
                .then(res => {
                    return res;
                })
                .catch(error => { throw new Error(error) });
            // aggregate x- and y-axes data
            for (let author of this.authorsToOperations) {
                let group = "";
                for (let [key, value] of Object.entries(authorsInfo)) {
                    if (!this.isMock) {
                        if (Object.keys(author)[0] === key) {
                            group = value.epalias;
                        }
                    }
                    if (this.isMock) {
                        group = Object.keys(author)[0];
                    }
                    if (Object.keys(author)[0] === "others") {
                        group = "Average";
                    }
                }
                // operations of student(s)
                if (Object.keys(author)[0] !== "others") {
                    data.push({
                        group: group,
                        edit: Object.values(author)[0].edit,
                        write: Object.values(author)[0].write,
                        paste: Object.values(author)[0].paste,
                        delete: Object.values(author)[0].delete,
                    });
                }
                // average of other students
                if (Object.keys(author)[0] === "others") {
                    data.push({
                        group: group,
                        edit: Object.values(author)[0].edit / this.users.length,
                        write: Object.values(author)[0].write / this.users.length,
                        paste: Object.values(author)[0].paste / this.users.length,
                        delete: Object.values(author)[0].delete / this.users.length,
                    });
                }
                // data values for calculation of y-axis range
                dataValues.push(Object.values(author)[0].edit);
                dataValues.push(Object.values(author)[0].write);
                dataValues.push(Object.values(author)[0].paste);
                dataValues.push(Object.values(author)[0].delete);
            }
            // set y-axis range boundary
            let yAxisRangeTop = Math.ceil(Math.max(...dataValues) / 100) * 100;
            // margin
            let margin = { top: 20, right: 30, bottom: 30, left: 30 };
            let width = 460 - margin.left - margin.right;
            let height = 230 - margin.top - margin.bottom;
            // svg object 
            let svg = d3.select(`#${this.elementId}`)
                .append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform",
                    "translate(" + margin.left + "," + margin.top + ")");
            /*
             * teacher chart
             */
            if (this.isModerator) {
                // subgroups
                let subgroups = this.operations;
                // groups
                let groups = [];
                for (let entry of data) {
                    groups.push(entry.group);
                }
                // x-axis
                let xAxis = d3.scaleBand()
                    .domain(groups)
                    .range([0, width])
                    .padding([0.2]);
                svg.append("g")
                    .attr("transform", "translate(0," + height + ")")
                    .call(d3.axisBottom(xAxis).tickSize(0));
                // y-axis
                let yAxis = d3.scaleLinear()
                    .domain([0, yAxisRangeTop])
                    .range([height, 0]);
                svg.append("g")
                    .call(d3.axisLeft(yAxis));
                // subgroup position
                let xSubgroup = d3.scaleBand()
                    .domain(subgroups)
                    .range([0, xAxis.bandwidth()])
                    .padding([0.05]);
                // bars
                svg.append("g")
                    .selectAll("g")
                    .data(data)
                    .enter()
                    .append("g")
                    .attr("transform", function (d) { return "translate(" + xAxis(d.group) + ",0)"; })
                    .selectAll("rect")
                    .data(function (d) { return subgroups.map(function (key) { return { key: key, value: d[key] }; }); })
                    .enter().append("rect")
                    .attr("x", function (d) { return xSubgroup(d.key); })
                    .attr("y", function (d) { return yAxis(d.value); })
                    .attr("width", xSubgroup.bandwidth())
                    .attr("height", function (d) { return height - yAxis(d.value); })
                    .attr("fill", (d, i) => { return this.operationsColors(i) });
            }
            /*
             * student chart
             */
            if (!this.isModerator) {
                // x-axis
                let xAxis = d3.scaleBand()
                    .range([0, width])
                    .domain(this.operations.map(function (o) { return o; }))
                    .padding(0.2);
                svg.append("g")
                    .attr("transform", "translate(0," + height + ")")
                    .call(d3.axisBottom(xAxis))
                    .selectAll("text")
                    .style("text-anchor", "center");
                // y-axis
                let yAxis = d3.scaleLinear()
                    .domain([0, yAxisRangeTop])
                    .range([height, 0]);
                svg.append("g")
                    .call(d3.axisLeft(yAxis));
                // seperate data for multiple bars
                let userData = []
                for (let [key, value] of Object.entries(data[0])) {
                    if (key !== "group") {
                        userData.push({ [key]: value });
                    }
                }
                let averageData = [];
                for (let [key, value] of Object.entries(data[1])) {
                    if (key !== "group") {
                        averageData.push({ [key]: value });
                    }
                }
                // user bar
                svg.selectAll("mybar-01")
                    .data(userData)
                    .enter()
                    .append("rect")
                    .attr("x", function (d) { return xAxis(Object.keys(d)[0]) + xAxis.bandwidth() / 2; })
                    .attr("y", function (d) { return yAxis(Object.values(d)[0]); })
                    .attr("width", xAxis.bandwidth() / 2)
                    .attr("height", function (d) { return height - yAxis(Object.values(d)[0]); })
                    .attr("fill", "#d3d3d3");
                // average bar
                svg.selectAll("mybar-02")
                    .data(averageData)
                    .enter()
                    .append("rect")
                    .attr("x", function (d) { return xAxis(Object.keys(d)[0]); })
                    .attr("y", function (d) { return yAxis(Object.values(d)[0]); })
                    .attr("width", xAxis.bandwidth() / 2)
                    .attr("height", function (d) { return height - yAxis(Object.values(d)[0]); })
                    .attr("fill", (d, i) => { return this.operationsColors(i) });
            }
        },
    },
    mounted() {
        this.isModerator = store._state.data.base.isModerator;
        /*
         * use mock data
         */
        if (this.isMock && this.isModerator) {
            this.authorsToOperations = [
                {
                    Mueller: {
                        edit: 40,
                        write: 90,
                        paste: 10,
                        delete: 20,
                    }
                },
                {
                    Fuchs: {
                        edit: 20,
                        write: 110,
                        paste: 30,
                        delete: 40,
                    }
                },
                {
                    Gamma: {
                        edit: 10,
                        write: 80,
                        paste: 20,
                        delete: 50,
                    }
                }
            ];
            this.loadBar();
        }
        if (this.isMock && !this.isModerator) {
            this.authorsToOperations = [
                {
                    Mueller: {
                        edit: 400,
                        write: 900,
                        paste: 100,
                        delete: 200,
                    }
                },
                {
                    others: {
                        edit: 200,
                        write: 900,
                        paste: 300,
                        delete: 400,
                    },
                },
            ];
            this.loadBar();
        }
        /*
         * use eva data
         */
        if (!this.isMock) {
            this.getData().then(() =>
                this.loadBar());
        }
    },
};
</script>

<style scoped lang="css">
.chart-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    gap: 20px;
}

.chart {
    width: 100%;
    height: 100%;
}

.legend {
    position: absolute;
    right: 5px;
}
</style>
