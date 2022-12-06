<template>
	<div>
		<h4>Schreibanteil pro Person</h4>
		<div class="chart-container">
			<svg width="200" height="200" :id="elementId"></svg>
			<ul class="legend mt-3" v-if="authors.length">
				<li v-for="author in authors" :key="author">
					<i class="fa fa-square" :style="{ color: colorFn(author) }"></i> {{ author }}
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
		};
	},
	props: {
		id: String,
		isMock: Boolean,
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
	},
	methods: {
		async getData() {
			return Communication.getFromEVA("authoring_ratios", {pad: store.state.base.padName})
					.then(data => {
						this.authors = data.authors;
						this.ratios = data.ratios;
						this.colors = data.colors;
					})
					.catch(() => {
						store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
					});

		},

		async loadPie() {

			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = this.ratios.reduce((result, entry) => result + entry, 0);
			if (sum < 1) {
				this.authors = [...this.authors, "Unbekannt"];
				this.ratios = [...this.ratios, 1 - sum];
				this.colors = [...this.colors, "#ccc"];
			}

			const svg = d3.select(`#${this.elementId}`),
					width = svg.attr("width"),
					height = svg.attr("height"),
					radius = Math.min(width, height) / 2;

			const chart = svg.append("g")
					.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

			const pie = d3.pie();
			const arc = d3.arc()
					.innerRadius(0)
					.outerRadius(radius);

			const arcs = chart.selectAll("arc")
					.data(pie(this.ratios))
					.enter().append("g")
					.attr("class", "arc");

			arcs.append("path")
					.attr("fill", (d, i) => this.colorFn(i))
					.attr("d", arc)
					.append("title")
					.text(d => d.value);
		},
	},
};
</script>
<style scoped>
.chart-container {
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	align-items: center;
}

@import "../../style/charts.scss";
</style>
