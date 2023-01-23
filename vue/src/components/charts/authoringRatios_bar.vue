<template>
	<div>
		<h4>Schreibanteil pro Person</h4>
		<div class="chart-container">
			<ul class="legend mt-3" v-if="authors.length" style="flex-shrink: 0">
				<li v-for="author in authors" :key="author">
					{{ author }}<i class="fa fa-square" :style="{ color: colorFn(author) }"></i>
				</li>
			</ul>
			<div class="chart" style="width: 100%; height: 100%" :id="elementId"></div>
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
		};
	},
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
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
	},
	watch: {
		padName() {
			if (!this.isMock) {
				this.getData().then(() =>
					this.loadBar());
			}
		}
	},
	methods: {
		async getData() {
			return Communication.getFromEVA("authoring_ratios", { pad: this.padName })
				.then(data => {
					this.authors = data.authors;
					this.ratios = data.ratios;
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

		},
	},
};
</script>
<style lang="css">
.chart-container {
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	align-items: center;
	gap: 20px;
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