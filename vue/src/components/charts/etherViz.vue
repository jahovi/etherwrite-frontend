<template>
  <div>
    <h4>Revisionshistorie</h4>
    <div class="chart-container">
      <div class="MultiRangeSliderContainer">
        <MultiRangeSlider
          :min="0"
          :max="numberOfRevisions"
          :step="1"
          :ruler="true"
          :label="true"
          :labels="labels"
          :minValue="sliderMinValue"
          :maxValue="sliderMaxValue"
          @input="updateValues"
        />
      </div>
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
import MultiRangeSlider from "multi-range-slider-vue";

export default {
	data() {
		return {
			responseData: [],
			etherVizData: [],
			revisionDateTimes: [],
			sliderMinValue: 0,
			sliderMaxValue: 1,
			numberOfRevisions: 1,
			sliderHasBeenModified: false
		};
	},
	components: {
		MultiRangeSlider,
	},
	props: {
		id: String,
		isMock: Boolean,
	},
	computed: {
		elementId() {
			return `etherViz_${this.id}`;
		},
		padName() {
			return this.$store.state.base.padName;
		},
		labels() {
			return this.revisionDateTimes.map(e => {
				if(this.revisionDateTimes.indexOf(e) == 0 || this.revisionDateTimes.indexOf(e) == this.revisionDateTimes.length -1 ) {
					return e.toLocaleDateString();// + ", " + e.toLocaleTimeString().substring(0, 5);
				}
				else {
					return "";
				}
			});
		}
	},
	name: "etherViz",
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
		updateValues(e) {
			this.sliderMinValue = e.minValue;
			this.sliderMaxValue = e.maxValue;
			this.loadEtherViz();
		},
		dateIsInRange(dateTime) {
			const minDate = this.revisionDateTimes[this.sliderMinValue];
			const maxDate = this.revisionDateTimes[this.sliderMaxValue];
			return minDate <= dateTime && dateTime <= maxDate;
		},
		parseDate(dateTimeString) {
			const dateRegex = /(\d{2})\.(\d{2})\.(\d{2}),\s(\d{2}):(\d{2})/;
			const match = dateRegex.exec(dateTimeString);

			if (match) {
				const day = match[1];
				const month = match[2] - 1; // subtract 1 to convert from 1-based to 0-based
				const year = "20" + match[3];
				const hours = match[4];
				const minutes = match[5];

				return new Date(year, month, day, hours, minutes);
			} else {
				return new Date("invalid date");
			}

		},
		async getData() {
			return Communication.getFromEVA("getEtherVizData", {pad: this.padName})
				.then(data => {

					data.forEach(d => {
						d.dateTime = this.parseDate(d.dateTime);
					});

					this.responseData = data;
					this.revisionDateTimes = data.map(e => e.dateTime);
					this.numberOfRevisions = this.revisionDateTimes.length-1;
					if(this.sliderHasBeenModified == false) {
						this.sliderMaxValue = this.numberOfRevisions;
						this.sliderHasBeenModified = true;
					}
				})
				.catch(() => {
					store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
				});
		},
		async loadEtherViz() {
			this.etherVizData = [];

			this.responseData.forEach(d => {
				const dateTimeLabel = d.dateTime.toLocaleDateString("de-DE", {day: "2-digit", month: "2-digit", year: "2-digit"}) + ", " + d.dateTime.toLocaleTimeString().substring(0, 5);

				if (this.dateIsInRange(d.dateTime)) {
					if ("rectangles" in d) {
					this.etherVizData.push(
							{dateTime: dateTimeLabel, rectangles: d.rectangles}
					);
					} else {
						this.etherVizData.push(
								{dateTime: dateTimeLabel, rectangles: []}
						);
					}
					if ("parallelograms" in d) {
						this.etherVizData.push(
								{dateTime: dateTimeLabel + "b", parallelograms: d.parallelograms}
						);
					} else {
						this.etherVizData.push(
								{dateTime: dateTimeLabel + "b", parallelograms: []}
						);
					}
				}
				
			});
			this.etherVizData = this.etherVizData.slice(0, this.etherVizData.length - 1);

			const numberOfChars = d3.max(this.etherVizData.map(timeStampData => {
				return timeStampData.rectangles ? timeStampData.rectangles.map(e => e.lowerLeft) : [0];
			}).flat()) + 1;

			// set the dimensions and margins of the graph
			var margin = {top: 110, right: 30, bottom: 30, left: 60},
					width = 560 - margin.left - margin.right,
					height = 500 - margin.top - margin.bottom;

			d3.select("svg").remove();
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
					.range([0, width])
					.domain(this.etherVizData.map(function (d) {
						return d.dateTime;
					}));
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
				if ("rectangles" in d) {
					d.rectangles.forEach((rectangle) => {
						svg.append("rect")
								.attr("x", x(d.dateTime))
								.attr("y", y(rectangle.upperLeft))
								.attr("width", x.bandwidth())
								.attr("height", y(rectangle.lowerLeft - rectangle.upperLeft + 1))
								.attr("fill", rectangle.authorColor);
					});
				} else if ("parallelograms" in d) {
					d.parallelograms.forEach((pgram) => {
						const heightOfPgram = pgram.lowerLeft - pgram.upperLeft + 1;
						var areaFunc = d3.area()
								.x0(x(d.dateTime))
								.x1(x(d.dateTime) + x.bandwidth())
								.y0(function (offset) {
									return y(pgram.upperLeft + offset);
								})
								.y1(function (offset) {
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
			ticks.each(function (val) {
				if (x(val) !== undefined && val.endsWith("b")) {
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
  flex-direction: column;
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

.MultiRangeSliderContainer {
  margin-left: 50px;
  margin-bottom: -30px;
  padding: 5px;
  float: right;
  width: 100%;
  max-width: 485px;
}

@import "../../style/charts.scss";
</style>
