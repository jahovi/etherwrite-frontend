<template>
    <div>
        <h4>Partizipationsdiagramm</h4>
        <div class="chart-container">
            <div class="chart" :id="elementId"></div>
            <ul class="legend mt-3" v-if="authorSet.size">
                <li v-for="author of Array.from(authorSet).reverse()" :key="author">
                    <i class="fa fa-square" :style="{ color: getAuthorColor(author) }"></i> {{ author }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";
import store from "../../store";


export default {
    name: "participation_diagram",
    props: {
        id: String,
        isMock: Boolean,
    },
    data() {
        return {
            datasets: [],
            authorColors: [],
            authorSet: {},
        };
    },
    computed: {
        elementId() {
            return `participation_diagram_${this.id}`;
        },
    },
    mounted() {
        this.getData().then(() => this.loadChart());
    },
    methods: {
        getRandomColor() {
            var o = Math.round, r = Math.random, s = 255;
            return "rgba(" + o(r() * s) + "," + o(r() * s) + "," + o(r() * s) + "," + 1 + ")";
        },
        getAuthorColor(author) {
            console.log(author);
            const index = [...this.authorSet].indexOf(author);
            console.log(index);
            return this.authorColors[index];
        },
        async getData() {
            const data = await Communication.getFromEVA("activity/activities", { padName: store.state.base.padName });

            // construct author set
            this.authorSet = new Set();
            for (const elem of data) {
                for (const author in elem.authorToActivities) {
                    this.authorSet.add(author);
                }
            }

            function random_rgba() {
                var o = Math.round, r = Math.random, s = 255;
                return "rgba(" + o(r() * s) + "," + o(r() * s) + "," + o(r() * s) + "," + 1 + ")";
            }

            for (const author of this.authorSet.values()) {
                // this.authorColors[author] = random_rgba();
                this.authorColors.push(random_rgba());
            }
            const timestampParser = d3.timeParse("%d.%m.%Y");
            for (const elem of data) {
                // TODO watch out for hour minute timestamps
                const dataset = { timestamp: timestampParser(elem.timestamp) };
                let activitySum = 0;
                for (const author in elem.authorToActivities) {
                    activitySum += elem.authorToActivities[author];
                }
                for (const author of this.authorSet.values()) {
                    if (Object.hasOwn(elem.authorToActivities, author.toString())) {
                        // author had activity for this timestamp
                        dataset[author] = (elem.authorToActivities[author] / activitySum);
                    } else {
                        dataset[author] = 0;
                    }
                }
                this.datasets.push(dataset);
            }

            console.log(this.authorColors);
            console.log(this.authorSet);
            console.log(this.datasets);


        },

        loadChart() {
            // const width = 400;
            // const height = 200;

            const margin = { top: 20, right: 30, bottom: 30, left: 30 };
            // let w = 460 - margin.left - margin.right;
            // let h = 400 - margin.top - margin.bottom;
            const w = 600;
            const h = 200;
            const barWidth = 400 / this.datasets.length;


            const stackGenerator = d3.stack().keys(Array.from(this.authorSet));
            const stackedData = stackGenerator(this.datasets);


            // const xScale = d3.scaleTime()
            //     .domain([this.datasets[0].timestamp, this.datasets[this.datasets.length - 1].timestamp])
            //     .range([50, 235]);
            const xScale = d3.scaleTime()
                .domain([this.datasets[0].timestamp, this.datasets[this.datasets.length - 1].timestamp])
                .range([margin.left, w]);

            // const yScale = d3.scaleLinear()
            //     .domain([0, 1])
            //     .range([275, 25]);
            const yScale = d3.scaleLinear()
                .domain([0, 1])
                .range([h, margin.bottom]);

            const colorScale = d3.scaleOrdinal()
                .domain(Array.from(this.authorSet))
                .range(this.authorColors);


            // svg object 
            let svg = d3.select(`#${this.elementId}`)
                .append("svg")
                .attr("width", w + margin.left + margin.right)
                .attr("height", h + margin.top + margin.bottom)
                // .append("g")
                // .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            // xAxis
            let xAxis = d3.axisBottom(xScale)
                .tickValues(this.datasets.map(d => d.timestamp))
                .tickFormat(d3.timeFormat("%d.%m"));
            svg.append("g")
                .attr("transform", "translate(" + margin.left + "," + h + ")")
                .call(xAxis)
                .selectAll("text")
                .attr("transform", "rotate(-45)")
                .style("text-anchor", "end");
                
            // Add blank axis to bridge gap between x and y axis
            let xAxisBlank = d3.axisBottom(xScale)
                .tickValues([])
                .tickSize(0);
            svg.append("g")
                .attr("transform", "translate(" + 0 + "," + h + ")")
                .call(xAxisBlank);

            // yAxis
            let yAxis = d3.axisLeft(yScale)
                .ticks(8);
            svg.append("g")
                .attr("transform", "translate(" + margin.left + "," + "0)")
                .call(yAxis);

            // add group for each author series
            const series = svg
                //.select("g")
                .selectAll("g.series")
                .data(stackedData)
                .join("g")
                .style("fill", (d) => colorScale(d.key));

            // add rect for each activity in the series
            series.selectAll("rect")
                .data((d) => d)
                .join("rect")
                .attr("width", barWidth)
                .attr("y", (d) => yScale(d[1]))
                .attr("x", (d) => xScale(d.data.timestamp) - 0.5 * barWidth)
                .attr("height", (d) => yScale(d[0]) - yScale(d[1]))
                .attr("transform", "translate(" + margin.left + "," + 0 + ")");

            // stacked area chart
            // const areaGen = d3.area()
            //     .x(d => xScale(d.data.timestamp))
            //     .y0(d => yScale(d[0]))
            //     .y1(d => yScale(d[1]));

            // const test = d3.select(`#${this.elementId}`)
            //     .selectAll(".areas")
            //     .data(stackedData)
            //     .join("path")
            //     .attr("d", areaGen)
            //     .attr("fill", d => colorScale(d.key));

            // console.log(test);
        },


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