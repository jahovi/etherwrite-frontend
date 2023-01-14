<template>
  <div>
    <h4>Schreibanteil pro Person</h4>
    <div class="chart-container">
      <div
        :id="elementId"
        class="chart-authoringRatiosPie"
        style="width: 75%; height: 100%"
      ></div>
      <ul class="legend mt-3" v-if="authors.length">
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
			widthOfSvg: 400,
			heightOfSvg: 200,
			radius: 1,
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
			return `authoringRatios_pie_${this.id}`;
		},
		colorFn() {
			if (!this.colors || !this.colors.length) {
				return null;
			}
			return d3.scaleOrdinal(this.colors);
		},
	},
	name: "authoringRatios_pie",
	mounted() {
		if (this.isMock) {
			this.authors = ["Mueller", "Fuchs", "Gamma"];
			this.ratios = [15, 35, 50];
			this.colors = ["#00B2EE", "#FF7F24", "#008B45"];
			this.loadPie();
		} else {
			this.getData().then(() =>
				this.loadPie());
		}
		this.$emit("dashboardDimensions", this.getDashboardDimensions);
	},
	watch: {
		padName() {
			if (!this.isMock) {
				this.getData().then(() =>
					this.loadPie());
			}
		},
		w(val) {
			this.widthOfSvg = val;
			this.loadPie();
		},
		h(val) {
			this.heightOfSvg = val;
			this.loadPie();
		},
	},
	methods: {
		getDashboardDimensions() {
			return {w: 6, h: 10};
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
		loadPie() {

			document.getElementById(this.elementId).childNodes.forEach(c => c.remove());
			

			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = this.ratios.reduce((result, entry) => result + entry, 0);
			if (sum < 1) {
				this.authors = [...this.authors, "Unbekannt"];
				this.ratios = [...this.ratios, 1 - sum];
				this.colors = [...this.colors, "#ccc"];
			}

			var margin = {top: 25, right: 75, bottom: 25, left: 25},
					width = this.widthOfSvg - margin.left - margin.right,
					height = this.heightOfSvg - margin.top - margin.bottom;

			d3.select("#authoringRatiosPie-svg").remove();

			var svg = d3.select(`#${this.elementId}`)
					.append("svg")
					.attr("id", "authoringRatiosPie-svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom);

			const chart = svg.append("g")
				.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

			this.radius = Math.min(width, height) / 2;

			const pie = d3.pie();
			const arc = d3.arc()
				.innerRadius(0)
				.outerRadius(this.radius);

			const arcs = chart.selectAll("arc")
				.data(pie(this.ratios))
				.enter().append("g")
				.attr("class", "arc");

			arcs.append("path")
				.attr("fill", (i) => this.colorFn(i))
				.attr("d", arc)
				.append("title")
				.text(d => `${d.value.toFixed(2)} %`);
		},
	},
};
</script>
<style scoped>
.chart-container {
  display: flex;
  flex-direction: row;
  justify-content: left;
  align-items: center;
  margin-left: -5%;
}

.chart-authoringRatiosPie {
  float: left;
}

@import "../../style/charts.scss";
</style>
