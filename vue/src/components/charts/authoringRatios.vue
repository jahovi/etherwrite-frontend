<template>
	<div>
		<h4>Schreibanteil p.P.</h4>
		<svg width="300" height="200"></svg>
		<ul class="legend mt-3" v-if="colorFn">
			<li v-for="author in pad.authors" :key="author">
				<i class="fa fa-square" :style="{color: colorFn(author)}"></i> {{ author }}
			</li>
		</ul>
	</div>
</template>

<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";

export default {
	data() {
		return {
			pad: {
				authors: [],
				ratios: [],
				colors: [],
			},
			colorFn: null,
		};
	},
	name: "authoringRatios",
	mounted() {
		this.getData().then(() =>
				this.loadPie());
	},
	methods: {
		async getData() {
			return Communication.getFromEVA("authoring_ratios", {pad: this.$store.state.base.padName})
					.then(data => {
						this.pad = {
							authors: data.authors,
							ratios: data.ratios,
							colors: data.colors,
						};
					})
					.catch(() => {
						this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
					});
		},

		loadPie() {
			const keys = this.pad.authors;
			const data = this.pad.ratios;
			const colors = this.pad.colors;

			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = data.reduce((result, entry) => result + entry);
			if (sum < 1) {
				keys.push("Unbekannt");
				data.push(1 - sum);
				colors.push("#ccc");
			}

			this.colorFn = d3.scaleOrdinal(this.pad.colors);

			const svg = d3.select("svg"),
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
					.data(pie(data))
					.enter().append("g")
					.attr("class", "arc");

			arcs.append("path")
					.attr("fill", (d, i) => this.colorFn(i))
					.attr("d", arc)
					.append("title")
					.text(d => d.value * 100);
		},
	},
};
</script>
<style scoped>
@import "../../style/charts.scss";
</style>
