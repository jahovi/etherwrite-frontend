<template>
    <div>
        <h4>Partizipationsdiagramm</h4>
        <div class="chart-container">
            <div class="chart" :id="elementId"></div>
            <!-- <ul class="legend mt-3" v-if="authorSet.size">
				<li v-for="author of authorSet.values()" :key="author">
					<i class="fa fa-square" :style="{ color: rgba(129,172,220,1) }"></i> {{ author }}
				</li>
			</ul> -->
            <!-- <svg width="300" height="300" :id="elementId"></svg> -->
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
        // authorColor(author) {
        //     console.log(author);
        //     const index = [...this.authorSet].indexOf(author);
        //     console.log(index);
        //     return this.authorColor[index];
        // },
    },
    mounted() {
        this.getData().then(() => this.loadChart());
    },
    methods: {
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

            let margin = { top: 20, right: 30, bottom: 30, left: 30 };
            let w = 460 - margin.left - margin.right;
            let h = 400 - margin.top - margin.bottom;

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
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            // Add xAxis
            let xAxis = d3.axisBottom(xScale)
            .tickValues(this.datasets.map(d => d.timestamp))
            .tickFormat(d3.timeFormat("%d.%m"));
            svg.append("g")
            .attr("transform", "translate(" + margin.left + "," + h + ")")
            .call(xAxis);
            
            // Add blank axis so that xAxis meets yAxis
            let xAxisBlank = d3.axisBottom(xScale)
                .tickValues([]);
            svg.append("g")
                .attr("transform", "translate(" + 0 + "," + h + ")")
                .call(xAxisBlank);

            // Add yAxis
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
                .attr("width", 40)
                .attr("y", (d) => yScale(d[1]))
                .attr("x", (d) => xScale(d.data.timestamp) - 20)
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

<style>

</style>