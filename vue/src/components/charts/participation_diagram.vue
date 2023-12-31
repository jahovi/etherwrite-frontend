<template>
	<div class="chart-outer-container">
		<h2>{{ getStrings["participationwidgettitle"] }}</h2>
		<div class="chart-container">
			<div class="chart" :id="elementId" ref="chart" style="width: 100%; height: 100%"></div>
			<ul class="legend mt-3" v-if="authorSet.size">
				<li v-for="author of Array.from(authorSet).reverse()" :key="author">
          <i
            class="fa fa-square"
            :style="{ color: getAuthorColor(author) }"
          ></i>
					{{ getUsername(author) }}
				</li>
			</ul>
		</div>
	</div>
</template>

<script lang="js">
import * as d3 from "d3";
import Communication from "../../classes/communication";
import store from "../../store";


export default {
	name: "participation_diagram",
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
		w: Number,
		h: Number,
	},
	data() {
		return {
			datasets: [],
			authorColors: [],
			authorSet: {},
			hourlyTimestamp: false,
			widthOfSvg: this.w || 560,
			heightOfSvg: this.h || 500,
		};
	},
	computed: {
		elementId() {
			return `participation_diagram_${this.id}`;
		},
		getStrings() {
			return this.$store.getters.getStrings;
		},
	},
	mounted() {
		if (this.isMock) {
			this.authorSet = new Set(["Mueller", "Fuchs", "Gamma"]);
			this.authorColors = ["#00B2EE", "#FF7F24", "#008B45"];
			this.datasets = [
				{
					timestamp: new Date("2023-01-01"),
					Mueller: 0.2,
					Fuchs: 0.3,
					Gamma: 0.5,
				},
				{
					timestamp: new Date("2023-02-01"),
					Mueller: 0,
					Fuchs: 0,
					Gamma: 0,
				},
				{
					timestamp: new Date("2023-03-01"),
					Mueller: 0,
					Fuchs: 0.7,
					Gamma: 0.3,
				},
			];
			this.loadChart();
		} else {
			this.getData().then(() => this.loadChart());
		}
		this.$emit("rearrangeDashboard");
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
		getUsername(author) {
			if (this.isMock) {
				return author;
			}
			return store.getters["users/usersByEpId"][author].fullName;
		},
		getAuthorColor(author) {
			const index = [...this.authorSet].indexOf(author);
			return this.authorColors[index];
		},
		async getData() {
			let data = null;
			try {
				data = await Communication.getFromEVA(`activity/activities/${this.padName}`);
			} catch {
				store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
			}

			// construct author set
			this.authorSet = new Set();
			for (const elem of data) {
				for (const author in elem.authorToActivities) {
					this.authorSet.add(author);
				}
			}

			// populate author colors array
			Array.from(this.authorSet).forEach((author) => this.authorColors.push(store.getters["users/usersByEpId"][author].color));

			// init timestamp parsers
			const dalyTimestampParser = d3.timeParse("%d.%m.%Y");
			const hourlyTimestampParser = d3.timeParse("%d.%m.%Y, %H:%M");

			// reformat data, convert activity counts to percentages
			for (const elem of data) {
				let dataset = null;
				if (elem.timestamp.indexOf(",") > -1) {
					// timestamp contains comma therefore it contains a time of day
					this.hourlyTimestamp = true;
					dataset = {timestamp: hourlyTimestampParser(elem.timestamp)};
				} else {
					dataset = {timestamp: dalyTimestampParser(elem.timestamp)};
				}
				let activitySum = 0;
				for (const author in elem.authorToActivities) {
					activitySum += elem.authorToActivities[author];
				}
				for (const author of this.authorSet.values()) {
					if (Object.hasOwn(elem.authorToActivities, author.toString())) {
						// author had activity for this timestamp
						dataset[author] = elem.authorToActivities[author] / activitySum;
					} else {
						dataset[author] = 0;
					}
				}
				this.datasets.push(dataset);
			}
		},

		async loadChart() {
			document.getElementById(this.elementId).childNodes.forEach(c => c.remove());
			// vars
			const margin = {top: 20, right: 300, bottom: 40, left: 30};
			let w = this.widthOfSvg - margin.right;
			let h = this.heightOfSvg - margin.bottom;
			if (w < 0) {
				w = 100;
			}
			if (h < 0) {
				h = 110;
			}
			const barPadding = 3;
			const barWidth = (w / this.datasets.length - barPadding) > 50 ? 50 : (w / this.datasets.length - barPadding);

			// stack data
			const stackGenerator = d3.stack().keys(Array.from(this.authorSet));
			const stackedData = stackGenerator(this.datasets);

			// init scales
			const xScale = d3.scaleTime()
				.domain([this.datasets[0].timestamp, this.datasets[this.datasets.length - 1].timestamp])
				.range([margin.left, w]);

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
				.attr("height", h + margin.top + margin.bottom);

			// labels for x- and y-axes
			svg.append("text")
				.attr("class", "x label")
				.attr("text-anchor", "start")
				.attr("x", w + 50)
				.attr("y", h + 20)
				.attr("font-size", "1.1em")
				.text(this.getStrings["participationwidgetxaxis"]);
			svg.append("text")
				.attr("class", "y label")
				.attr("text-anchor", "start")
				.attr("y", 10)
				.attr("dy", ".75em")
				.attr("font-size", "1.1em")
				.text(this.getStrings["participationwidgetyaxis"]);

			// define xAxis	
			let daysWithActivities = [];
			this.datasets.forEach(set => {
				let sum = 0;
				for (let key in set) {
					if(key !== "timestamp") {
						sum += set[key];
					}
				}
				if(sum === 1) {
					daysWithActivities.push(set);
				}
			});

			let xAxis = d3.axisBottom(xScale)
				.tickSizeOuter(0)
				.tickValues(daysWithActivities.map(d => d.timestamp));
			if (this.hourlyTimestamp) {
				xAxis.tickFormat(d3.timeFormat("%H:%M"));
			} else {
				xAxis.tickFormat(d3.timeFormat("%d.%m"));
			}

			// append xAxis
			svg.append("g")
				.attr("transform", "translate(" + margin.left + "," + h + ")")
				.call(xAxis)
				.selectAll("text")
				.attr("transform", "translate(-10, 2)rotate(-65)")
				.style("text-anchor", "end")
				.attr("font-size", "1.5em");

			// Add blank axis to bridge gap between x and y axis
			let xAxisBlank = d3.axisBottom(xScale)
				.tickValues([])
				.tickSize(0);
			svg.append("g")
				.attr("transform", "translate(" + 0 + "," + h + ")")
				.call(xAxisBlank);

			// append yAxis
			let yAxis = d3.axisLeft(yScale)
				.ticks(8);
			svg.append("g")
				.attr("transform", "translate(" + margin.left + "," + "0)")
				.call(yAxis)
				.attr("font-size", "1em");

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
		},
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
	flex-grow: 1;
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
