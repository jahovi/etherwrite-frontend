<template>
  <div>
    <h4>Revisionshistorie</h4>
    <div class="chart-container">
      <div
        :id="elementId"
        class="chart"
        style="width: 100%; height: 100%"
      ></div>
    </div>
  </div>
</template>

<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";
import store from "../../store";

export default {
	data() {
		return {
			etherVizData: []
		};
	},
	props: {
		id: String,
		isMock: Boolean,
	},
	computed: {
		elementId() {
			return `etherViz_diagram${this.id}`;
		},
        padName() {
			return this.$store.state.base.padName;
		},
	},
	name: "etherViz_diagram",
	mounted() {
		if (this.isMock) {
			this.getData().then(() =>
					this.loadEtherViz());
		} else {
			this.getData().then(() =>
					this.loadEtherViz());
		}
	},
	methods: {
		async getData() {
			return Communication.getFromEVA("getEtherVizData", {pad: this.padName})
					.then(data => {
                        this.etherVizData = [];
                        console.log("Length of response array: " + data.length);
                        data.slice(0, 12).forEach(d => { // ToDo: Remove Slicing, need to mitigate a bug in EVA
                            const dT = this.prettifyDateTime(d.dateTime);
                            if("rectangles" in d) {
                                this.etherVizData.push(
                                    {dateTime: dT, rectangles: d.rectangles}
                                );
                            } else {
                                this.etherVizData.push(
                                    {dateTime: dT, rectangles: []}
                                );
                            }
                            if("parallelograms" in d) {
                                this.etherVizData.push(
                                    {dateTime: dT + "b", parallelograms: d.parallelograms}
                                );
                            } else {
                                this.etherVizData.push(
                                    {dateTime: dT + "b", parallelograms: []}
                                );
                            }
                        });
                        this.etherVizData = this.etherVizData.slice(0, this.etherVizData.length-1);
					})
					.catch(() => {
						store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
					});

		},
        prettifyDateTime(dT) {
            const datePart = dT.split(", ")[0].split(".");
            const timePart = dT.split(", ")[1].split(":");
            const time = timePart[0] + ":" + timePart[1];
            const day = datePart[0].length == 1 ? "0" + datePart[0] : datePart[0];
            const month = datePart[1];
            const year = datePart[2].slice(2);
            const date = day + "." + month + "." + year;
            return date + ", " + time;
        },
		async loadEtherViz() {          

           const numberOfChars = d3.max(this.etherVizData.map(timeStampData => {
                return timeStampData.rectangles ? timeStampData.rectangles.map(e => e.lowerLeft) : [0];
            }).flat());
            
            // set the dimensions and margins of the graph
            var margin = {top: 110, right: 30, bottom: 30, left: 60},
                width = 560 - margin.left - margin.right,
                height = 500 - margin.top - margin.bottom;

            // append the svg object to the body of the page
            var svg = d3.select(`#${this.elementId}`)
            .append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
            .append("g")
                .attr("transform",
                    "translate(" + margin.left + "," + margin.top + ")");

            
            // X axis
            var x = d3.scaleBand()
            .range([ 0, width ])
            .domain(this.etherVizData.map(function(d) { return d.dateTime; }));
            //.padding(0.2);
            svg.append("g")
            .attr("transform", "translate(0, 0)")
            .call(d3.axisTop(x).tickSize(0))
            .selectAll("text")
                .attr("transform", "translate(10,-10)rotate(-90)")
                .style("text-anchor", "start");

            // Add Y axis
            var y = d3.scaleLinear()
            .domain([numberOfChars, 0])
            .range([height, 0]);
            svg.append("g")
            .call(d3.axisLeft(y));

            this.etherVizData.forEach((d) => {
                if("rectangles" in d) {
                    d.rectangles.forEach((rectangle) => {
                        svg.append("rect")
                        .attr("x", x(d.dateTime))
                        .attr("y", y(rectangle.upperLeft))
                        .attr("width", x.bandwidth())
                        .attr("height", y(rectangle.lowerLeft-rectangle.upperLeft))
                        .attr("fill", rectangle.authorColor);
                    });
                } else if("parallelograms" in d){
                    d.parallelograms.forEach((pgram) => {
                        const heightOfPgram = pgram.lowerLeft - pgram.upperLeft;
                        var areaFunc = d3.area()
                                .x0(x(d.dateTime))
                                .x1(x(d.dateTime) + x.bandwidth())
                                .y0(function(offset) {
                                    return y(pgram.upperLeft + offset);
                                })
                                .y1(function(offset) {
                                    return y(pgram.upperRight + offset);
                                });

                        svg.append("path")
                            .attr("d", areaFunc([0, heightOfPgram]))
                            .attr("fill", pgram.authorColor)
                            .attr("opacity", 0.75);
                    });
                }
            });

            // Remove ticks for parallelogram columns
            var ticks = d3.selectAll(".tick text");
                ticks.each(function(val){
                    if(x(val) !== undefined && val.endsWith("b")) {
                        d3.select(this).remove();
                    }
                });
        },
	},
};
</script>
<style scoped>
.chart-container {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: flex-start;
}

.chart >>> div {
  font: 10px sans-serif;
  text-align: right;
  padding: 3px;
  margin: 1px;
  color: white;
}

@import "../../style/charts.scss";
</style>
