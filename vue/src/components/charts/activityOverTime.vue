<template>
	<div class="chart-outer-container">
		<div class="chart-options">
			<h2 style="display: inline-block;padding-right: 1cm;">{{ getStrings["activityovertimewidgettitle"] }}</h2>
			<input type="radio" id="linear" value="linear" v-model="scaling" class="radioButton">
			<label for="linear" class="radioLabel">{{ getStrings["activityovertimewidgetscalinglinear"] }}</label>
			<input type="radio" id="logarithmic" value="logarithmic" v-model="scaling" class="radioButton">
			<label for="linear" class="radioLabel">{{ getStrings["activityovertimewidgetscalinglogarithmic"] }}</label>
		</div>
		<div class="chart-container">
			<div class="chart" style="width: 100%; height: 100%" :id="elementId" ref="chart"></div>
			<ul class="legend mt-3" style="flex-shrink: 0">
				<li v-for="(color, author) in legend" :key="author">
					<i class="fa fa-square" :style="{ color }"></i> {{ author }}
				</li>
			</ul>
		</div>
	</div>
</template>

<script lang=js>
import Communication from "../../classes/communication";
import store from "../../store";
import * as d3 from "d3";

export default {
	data() {
		return {
			data: [],
			scaling: "linear",
			widthOfSvg: this.w || 1000,
			heightOfSvg: this.h || 150,
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
			return `activityOverTime_${this.id}`;
		},
		legend() {
			const authorIds = this.data
				.map(d => d.authorToActivities)
				.map(a2a => Object.keys(a2a))
				.reduce((prev, curr) => [...prev, ...curr], []);
			return authorIds.reduce((result, id) => {
				if (id === "others") {
					result["Andere"] = "#ccc";
				} else {
					const author = store.getters["users/usersByEpId"][id];
					if (author) {
						result[author.fullName] = author.color;
					}
				}

				return result;
			}, {});
		},
		getStrings() {
			return this.$store.getters.getStrings;
		},

	},
	name: "activityOverTime",
	mounted() {
		if (this.isMock) {
			this.data = [{
				timestamp: "01.01.23",
				authorToActivities: {
					"others": 15,
				},
			}, {
				timestamp: "02.01.23",
				authorToActivities: {
					"others": 0,
				},
			}, {
				timestamp: "03.01.23",
				authorToActivities: {
					"others": 65,
				},
			}];
			this.loadLine();
		} else {
			this.getData().then(() =>
				this.loadLine());
		}
		this.$emit("rearrangeDashboard");
	},
	watch: {
		padName() {
			if (!this.isMock) {
				this.getData().then(() =>
					this.loadLine());
			}
		},
		scaling() {
			this.loadLine();
		},
		w(val) {
			this.widthOfSvg = val;
			this.loadLine();
		},
		h(val) {
			this.heightOfSvg = val;
			this.loadLine();
		},
	},
	methods: {
		async getData() {
			return Communication.getFromEVA(`activity/activities/${this.padName}`)
				.then(data => {
					this.data = data;
				})
				.catch(() => {
					store.commit("setAlertWithTimeout", ["alert-danger", store.getters.getStrings.unknown_error, 3000]);
				});
		},
		loadLine: function () {
			document.getElementById(this.elementId).childNodes.forEach(c => c.remove());

			let width = this.widthOfSvg - 350;
			let height = this.heightOfSvg - 80;

			if (width < 0) {
				width = 100;
			}
			if (height < 0) {
				height = 110;
			}

			const svg = d3.select(`#${this.elementId}`)
				.append("svg")
				.attr("width", "100%")
				.attr("height", "100%")
				.append("g")
				.attr("transform",
					"translate(30, 5)");

			const timestamps = this.data.map(d => d.timestamp);
			const range = [];
			for (let i = 0; i <= timestamps.length; i++) {
				range.push((width / timestamps.length) * i);
			}
			const datasets = {};
			this.data
				.map(d => d.authorToActivities)
				.map(a2a => Object.keys(a2a))
				.reduce((prev, curr) => [...prev, ...curr], [])
				.forEach(author => datasets[author] = []);

			this.data.forEach(entry => {
				Object.keys(datasets).forEach(key => {
					datasets[key].push({
						timestamp: entry.timestamp,
						activity: entry.authorToActivities[key] || 0,
					});
				});
			});

			// labels for x- and y-axes
			svg.append("text")
				.attr("class", "x label")
				.attr("text-anchor", "end")
				.attr("x", width)
				.attr("y", height + 50)
				.attr("font-size", "1.1em")
				.text(this.getStrings["activityovertimewidgetxaxis"]);
			svg.append("text")
				.attr("class", "y label")
				.attr("text-anchor", "start")
				.attr("x", 10)
				.attr("y", -5)
				.attr("dy", ".75em")
				.attr("font-size", "1.1em")
				.text(this.getStrings["activityovertimewidgetyaxis"]);

			const x = d3.scaleOrdinal()
				.domain(timestamps)
				.range(range);

			svg.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x)
					.ticks(timestamps.length))
				.selectAll("text")
				.style("text-anchor", "end")
				.attr("dx", "-.8em")
				.attr("dy", ".15em")
				.attr("transform", "rotate(-65)");

			let max = 0;
			this.data.forEach(d => {
				Object.values(d.authorToActivities || {}).forEach(v => {
					if (v > max) {
						max = v;
					}
				});
			});

			Object.entries(datasets).forEach(([authorId, datapoints]) => {

				const author = store.getters["users/usersByEpId"][authorId];
				const color = author ? author.color : "#ccc";

				let y;
				if (this.scaling === "linear") {
					y = d3.scaleLinear(datapoints)
						.domain([0, max])
						.range([height, 0]);
				} else {
					y = d3.scaleLog(datapoints)
						.domain([1, max])
						.range([height, 0]).clamp(true);
				}

				svg.append("g")
					.call(d3.axisLeft(y).ticks(5));

				// Add the line
				svg.append("path")
					.data(datapoints)
					.attr("fill", "none")
					.attr("stroke", color)
					.attr("stroke-width", 3)
					.attr("d", d3.line()
						.x(d => x(d.timestamp))
						.y(d => y(d.activity))(datapoints),
					);
			});
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

label.radioLabel {
	width: 60px;
}
.radioButton {
	margin-right: 2px;
}
</style>