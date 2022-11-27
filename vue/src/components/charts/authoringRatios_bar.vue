<template>
	<div>
		<h4>Schreibanteil pro Person</h4>
		<div class="chart-container">
			<div class="chart" style="width: 100%; height: 100%" :id="elementId"></div>
			<ul class="legend mt-3" v-if="authors.length" style="flex-shrink: 0">
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
	methods: {
		async getData() {
			return Communication.getFromEVA("authoring_ratios", {pad: this.$store.state.base.padName})
					.then(data => {
						this.authors = data.authors;
						this.ratios = data.ratios;
						this.colors = data.colors;
					})
					.catch(() => {
						this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
					});
		},
		loadBar() {
			// Add a filler if the given numbers don't add up to 100%.
			// Can happen with weird test data.
			const sum = this.ratios.reduce((result, entry) => result + entry);
			if (sum < 1) {
				this.keys = [...this.keys, "Unbekannt"];
				this.ratios = [...this.ratios, 1 - sum];
				this.colors = [...this.colors, "#ccc"];
			}


			d3.select(`#${this.elementId}`)
					.selectAll("div")
					.data(this.ratios)
					.enter()
					.append("div")
					.style("width", d => d + "%")
					.style("background-color", (d, i) => this.colorFn(i))
					.text(d => d);
		},
	},
};
</script>
<style scoped lang="css">
.chart-container {
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	align-items: center;
	gap: 20px;
}

.chart >>> div {
	font: 10px sans-serif;
	text-align: right;
	padding: 3px;
	margin: 1px;
	color: white;
}
</style>