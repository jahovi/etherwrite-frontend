<template>
<<<<<<< HEAD
  <div>
    <h4>Schreibanteil pro Person</h4>
    <div class="chart-container">
      <div
        class="chart"
        style="width: 100%; height: 100%"
        :id="elementId"
      ></div>
      <ul class="legend mt-3" v-if="authors.length" style="flex-shrink: 0">
        <li v-for="author in authors" :key="author">
          <i class="fa fa-square" :style="{ color: colorFn(author) }"></i>
          {{ author }}
        </li>
      </ul>
    </div>
  </div>
=======
	<div>
		<h4>Schreibanteil pro Person</h4>
		<div class="chart-container">
			<ul class="legend mt-3" v-if="authors.length" style="flex-shrink: 0">
				<li v-for="author in authors" :key="author">
					{{ author }} <i class="fa fa-square" :style="{ color: colorFn(author) }"></i>
				</li>
			</ul>
			<div class="chart" style="width: 100%; height: 100%" :id="elementId"></div>
		</div>
	</div>
>>>>>>> 6edd61f (Legende verschoben)
</template>

<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";
import store from "../../store";

export default {
	data() {
		return {
			authors: [],
			ratios: [],
			colors: [],
			widthOfSvg: 800,
			heightOfSvg: 200,
		};
	},
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
		w: Number,
		h: Number,
	},
	computed: {
		elementId() {
			return `authoringRatios_bar_${this.id}`;
		},
		colorFn() {
			if (!this.colors || !this.colors.length) {
				return null;
			}
			return d3.scaleOrdinal(this.colors);
		},
	},
	name: "authoringRatios_bar",
	mounted() {
		if (this.isMock) {
			this.authors = ["Mueller", "Fuchs", "Gamma"];
			this.ratios = [15, 35, 50];
			this.colors = ["#00B2EE", "#FF7F24", "#008B45"];
			this.loadBar();
		} else {
			this.getData().then(() =>
				this.loadBar());
		}
		this.$emit("dashboardDimensions", this.getDashboardDimensions);
	},
	watch: {
		w(val) {
			this.widthOfSvg = val;
			this.loadBar();
		},
		h(val) {
			this.heightOfSvg = val;
			this.loadBar();
		},
		padName() {
			if (!this.isMock) {
				this.getData().then(() =>
					this.loadBar());
			}
		}
	},
	methods: {
		getDashboardDimensions() {
			return {w: 7, h: 12};
		},
		async getData() {
			return Communication.getFromEVA("authoring_ratios", { pad: this.padName })
				.then(data => {

					const base = data.ratios.reduce((e, result) => e+result);
					this.authors = data.authors;
					this.ratios = data.ratios.map(e => e/base*100);

					this.colors = data.colors;
				})
				.catch(() => {
					store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
				});
		},
		loadBar() {
			document.getElementById(this.elementId).childNodes.forEach(c => c.remove());

			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = this.ratios.reduce((result, entry) => result + entry, 0);
			if (sum < 1) {
				this.authors = [...this.authors, "Unbekannt"];
				this.ratios = [...this.ratios, 1 - sum];
				this.colors = [...this.colors, "#ccc"];
			}

<<<<<<< HEAD
=======
			console.log(this.ratios);

			const svg = d3.select(`#${this.elementId}`);

			const chart = svg.append("bar")
				.attr("class", "bar");
			
			//Eigenschaften der Balken festlegen	
			const arcs = chart.selectAll("arc")
				.data(this.ratios)
				.enter().append("div")
				.style("width", d => d + "%")
				.style("background-color", (i) => this.colorFn(i))
				.style("overflow", "visible");
			
			//Text auf den Balken mit dem jeweiligen Wert
			arcs.append("span")
				.attr("class", "chart-label")
				.text(d => `${d} %`);
>>>>>>> 9c29d1b (Kommentare reingeschrieben, weil ich die sonst immer vergesse)

			d3.select("#authoringRatiosBar-svg").remove();

			const data = [];
			for (let i = 0; i < this.ratios.length; i++) {
				data.push({
					author: this.authors[i],
					ratio: this.ratios[i],
					color: this.colors[i],
				});
			}

			// set the dimensions and margins of the graph
			var margin = {top: 25, right: 0, bottom: 25, left: 130},
				width = this.widthOfSvg * 0.6 - margin.left - margin.right,
				height = this.heightOfSvg - margin.top - margin.bottom;

			// append the svg object to the body of the page
			const svg = d3.select(`#${this.elementId}`)
				.append("svg")
				.attr("id", "authoringRatiosBar-svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", height + margin.top + margin.bottom)
			.append("g")
				.attr("transform",
					"translate(" + margin.left + "," + margin.top + ")");

			// Add X axis
			var x = d3.scaleLinear()
				.domain([0, 100])
				.range([ 0, width]);

			var xAxis = d3.axisBottom(x);

			svg.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis)
				.selectAll("text")
				.attr("transform", "translate(-10,0)rotate(-45)")
				.style("text-anchor", "end");

			// Y axis
			var y = d3.scaleBand()
				.range([ 0, height ])
				.domain(data.map(function(d) { return d.author; }))
				.padding(.1);
			svg.append("g")
				.call(d3.axisLeft(y));

			//Bars
			svg.selectAll("myRect")
				.data(data)
				.enter()
				.append("rect")
				.attr("x", x(0) )
				.attr("y", function(d) { return y(d.author); })
				.attr("width", function(d) { return x(d.ratio); })
				.attr("height", y.bandwidth() )
				.attr("fill", d => d.color);


	},
	}
};

</script>
<style scoped>
.chart-container {
<<<<<<< HEAD
  display: flex;
  flex-direction: row;
  justify-content: left;
  align-items: center;
=======
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 20px;
>>>>>>> 6edd61f (Legende verschoben)
}

.chart-container .bar {
  text-align: right;
}

.chart-label {
  background-color: rgba(255, 255, 255, 0.6);
  color: black;
  padding: 2px;
  font: 10px sans-serif;
  white-space: nowrap;
  vertical-align: middle;
}
</style>