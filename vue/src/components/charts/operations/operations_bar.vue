<template>
    <div class="chart-outer-container">
        <h2>{{ getStrings["operationswidgettitle"] }}</h2>
        <div class="chart-container">
            <div class="chart" :id="elementId"></div>
            <ul class="legend mt-3">
                <li v-for="(str, key) of operationsStrings" :key="key">
                    <i class="fa fa-square" :style="{ color: operationsColors(key) }"></i>
                    {{ str }}
                </li>
                <li v-if="!isModerator">
                    <i class="fa fa-square" style="color: #d3d3d3"></i>
                    {{ getStrings["operationswidgetaverage"] }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="js">
import * as d3 from "d3";
import Communication from "../../../classes/communication";
import store from "../../../store";

export default {
    name: "operations_bar",
    props: {
        id: String,
        isMock: Boolean,
        padName: String,
        w: Number,
        h: Number,
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
                "delete",
            ],
            operationsStrings: [],
            isModerator: false,
            widthOfSvg: 560,
            heightOfSvg: 500,
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
        getStrings() {
            return this.$store.getters.getStrings;
        },
    },
    watch: {
        padName() {
            if (!this.isMock) {
                this.getData().then(() =>
                    this.loadChart());
            }
        },
        w(val) {
            this.widthOfSvg = val;
            this.loadChart();
        },
        h(val) {
            this.heightOfSvg = val;
            this.loadChart();
        },
    },
    methods: {
        /**
         * Get data from EVA and sum up edits, writes, pastes and deletes for each author.
         */
        getDashboardDimensions() {
            return { w: 10, h: 14 };
        },
        async getData() {
            this.authorsToOperations = [];
            return Communication.getFromEVA(`activity/operations/${this.padName}`)
                .then(res => {
                    for (const entry of res) {
                        // check format {EDIT: number, WRITE: number, PASTE: number, DELETE: number}
                        for (const key in entry.authorToOperations) {
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
                    }
                    const filtered = res.filter(el => Object.keys(el.authorToOperations).length !== 0);
                    for (const entry of filtered) {
                        const keys = Object.keys(entry.authorToOperations);
                        for (const key of keys) {
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
                            const author = this.authorsToOperations.find(obj => {
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
        async loadChart() {
            document.getElementById(this.elementId).childNodes.forEach(c => c.remove());
            const data = [];
            const dataValues = [];
            // get authors info
            const authorsInfo = store.getters["users/usersByEpId"];
            // aggregate x- and y-axes data
            for (const author of this.authorsToOperations) {
                let group = "";
                for (const [key, value] of Object.entries(authorsInfo)) {
                    if (!this.isMock) {
                        if (Object.keys(author)[0] === key) {
                            group = value.fullName;
                        }
                    }
                    if (this.isMock) {
                        group = Object.keys(author)[0];
                    }
                    if (Object.keys(author)[0] === "others") {
                        group = "others";
                    }
                }
                // operations of student(s) or average of others 
                const multiplier = Object.keys(author)[0] !== "others" ? 1 : 1 / Object.keys(authorsInfo).length;
                data.push({
                    group: group,
                    [this.operationsStrings[0]]: Object.values(author)[0].edit * multiplier,
                    [this.operationsStrings[1]]: Object.values(author)[0].write * multiplier,
                    [this.operationsStrings[2]]: Object.values(author)[0].paste * multiplier,
                    [this.operationsStrings[3]]: Object.values(author)[0].delete * multiplier,
                });
                // data values for calculation of y-axis range
                dataValues.push(Object.values(author)[0].edit * multiplier);
                dataValues.push(Object.values(author)[0].write * multiplier);
                dataValues.push(Object.values(author)[0].paste * multiplier);
                dataValues.push(Object.values(author)[0].delete * multiplier);
            }
            // set y-axis range boundary
            const yAxisRangeTop = Math.ceil(Math.max(...dataValues) / 10) * 10;
            // margin, width, height
            const margin = { top: 40, right: 150, bottom: 30, left: 75 };
            const width = this.widthOfSvg - margin.right - margin.left; 
            const height = this.heightOfSvg - margin.bottom - margin.top;
            // svg object
            const svg = d3.select(`#${this.elementId}`)
                .append("svg")
                .attr("width", width + 250)
                .attr("height", height + 150)
                .append("g")
                .attr("transform",
                    "translate(" + margin.left + "," + margin.top + ")");
            // labels x- and y-axis
            const offsetXWidth = this.isMock ? 0 : 40;
            svg.append("text")
                .attr("class", "x label")
                .attr("text-anchor", "center")
                .attr("x", width - offsetXWidth)
                .attr("y", height + 30)
                .attr("font-size", "1.25em")
                .text(this.getStrings[`operationswidgetxaxis${this.isModerator ? "teacher" : "student"}`]);
            svg.append("text")
                .attr("class", "y label")
                .attr("text-anchor", "center")
                .attr("x", 0)
                .attr("y", -20)
                .attr("dy", ".75em")
                .attr("font-size", "1.25em")
                .text(this.getStrings["operationswidgetyaxis"]);
            /*
             * teacher chart
             */
            if (this.isModerator && data.length !== 0) {
                // subgroups
                const subgroups = this.operationsStrings;
                // groups
                const groups = [];
                for (const entry of data) {
                    groups.push(entry.group);
                }
                // x-axis
                const offsetXAxisLetterings = this.isMock && this.isModerator ? 0 : 70;
                const xAxis = d3.scaleBand()
                    .domain(groups)
                    .range([0, width])
                    .padding([0.2]);
                svg.append("g")
                    .call(d3.axisBottom(xAxis).tickSize(0))
                    .attr("transform", `translate(0, ${height})`)
                    .attr("font-size", "1.1em")
                    .selectAll("text")
                    .attr("transform", `translate(${offsetXAxisLetterings}, 0)rotate(-15)`)
                    .style("text-anchor", "end");

                // y-axis
                const yAxis = d3.scaleLinear()
                    .domain([0, yAxisRangeTop])
                    .range([height, 0]);
                svg.append("g")
                    .call(d3.axisLeft(yAxis))
                    .attr("font-size", "1em");
                // subgroup position
                const xSubgroup = d3.scaleBand()
                    .domain(subgroups)
                    .range([0, xAxis.bandwidth()])
                    .padding([0.05]);
                // bars
                svg.append("g")
                    .selectAll("g")
                    .data(data)
                    .enter()
                    .append("g")
                    .attr("transform", function (d) { return `translate(${xAxis(d.group)}, 0)`; })
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
            if (!this.isModerator && data.length !== 0) {
                // x-axis
                const xAxis = d3.scaleBand()
                    .range([0, width])
                    .domain(this.operationsStrings.map(function (o) { return o; }))
                    .padding(0.2);
                svg.append("g")
                    .call(d3.axisBottom(xAxis))
                    .attr("transform", `translate(0, ${height})`)
                    .attr("font-size", "1.1em")
                    .selectAll("text")
                    .style("text-anchor", "center");
                // y-axis
                const yAxis = d3.scaleLinear()
                    .domain([0, yAxisRangeTop])
                    .range([height, 0])
                svg.append("g")
                    .call(d3.axisLeft(yAxis))
                    .style("text-anchor", "center")
                    .attr("font-size", "1.1em");
                // seperate data for multiple bars
                const userData = [];
                for (const [key, value] of Object.entries(data[1])) {
                    if (key !== "group") {
                        userData.push({ [key]: value });
                    }
                }
                const averageData = [];
                for (const [key, value] of Object.entries(data[0])) {
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

        for (const operation of this.operations) {
            this.operationsStrings.push(this.getStrings[`operationswidget${operation}`]);
        }
        /*
         * use mock data
         */
        if (this.isMock && this.isModerator) {
            this.authorsToOperations = [
                {
                    Mueller: {
                        edit: 400,
                        write: 900,
                        paste: 100,
                        delete: 20,
                    }
                },
                {
                    Fuchs: {
                        edit: 200,
                        write: 1100,
                        paste: 300,
                        delete: 400,
                    }
                },
                {
                    Gamma: {
                        edit: 100,
                        write: 800,
                        paste: 200,
                        delete: 200,
                    }
                }
            ];
            this.loadChart();
        }
        if (this.isMock && !this.isModerator) {
            this.authorsToOperations = [
                {
                    Mueller: {
                        edit: 750,
                        write: 4000,
                        paste: 500,
                        delete: 2000,
                    }
                },
                {
                    others: {
                        edit: 2000,
                        write: 9000,
                        paste: 3000,
                        delete: 4000,
                    },
                },
            ];
            this.loadChart();
        }
        /*
         * use eva data
         */
        if (!this.isMock) {
            this.getData().then(() =>
                this.loadChart());
        }
        this.$emit("dashboardDimensions", this.getDashboardDimensions);
    },
};
</script>

<style scoped lang="css">
.chart-outer-container {
    height: 100%;
    display: flex;
    flex-direction: column;
}

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
