<template>
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
			widthOfSvg: this.w || 800,
			heightOfSvg: this.h || 200,
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
		this.$emit("rearrangeDashboard");
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
		},
	},
	methods: {
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

			if (this.ratios.length === 0) {
				return;
			}
			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = this.ratios.reduce((result, entry) => result + entry, 0);
			if (sum < 1) {
				this.authors = [...this.authors, "Unbekannt"];
				this.ratios = [...this.ratios, 1 - sum];
				this.colors = [...this.colors, "#ccc"];
			}

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
			var margin = {top: 25, right: 0, bottom: 50, left: 150},
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

			const xAxis = d3.axisBottom(x);

			svg.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis)
				.selectAll("text")
				.style("font-family", "sans-serif")
				.style("font-size", "12px")
				.attr("transform", "translate(-10,0)rotate(-45)")
				.style("text-anchor", "end");

			// Y axis
			var y = d3.scaleBand()
				.range([ 0, height ])
				.domain(data.map(function(d) { return d.author; }))
				.padding(.1);
			
			const yAxis = d3.axisLeft(y);
			
			svg.append("g")
				.call(yAxis)
				.selectAll("text")
				.style("font-family", "sans-serif")
				.style("font-size", "12px");

			// Bars
			svg.selectAll("myRect")
				.data(data)
				.enter()
				.append("rect")
				.attr("x", x(0) )
				.attr("y", function(d) { return y(d.author); })
				.attr("width", function(d) { return x(d.ratio); })
				.attr("height", y.bandwidth() )
				.attr("fill", d => d.color);

			// X-axis labels
			svg.append("text")
				.attr("text-anchor", "middle")
				.style("font-weight", "bold")
				.attr("transform", "translate("+ (width/2) + "," +(height+(margin.bottom * 0.90)) + ")")
				.text("Schreibanteil in Prozent");
			
				
			// Horizontal bar labels
			svg.append("g")
				.attr("transform", "translate(5, 0)")
				.selectAll(".text")
				.data(data)
				.enter()
				.append("text")
				.style("font-size", "12px")
				.style("font-family", "sans-serif")
				.attr("x", d => x(d.ratio))
				.attr("y", d => y(d.author) + y.bandwidth()/2)
				.text(d => d.ratio.toFixed(2) + "%"); 

	},
	}
};

</script>
<style scoped>
.chart-container {
  display: flex;
  flex-direction: row;
  justify-content: left;
  align-items: center;
}

.chart-container .bar {
  text-align: right;
}
</style>