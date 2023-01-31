<template>
	<div class="chart-outer-container">
		<h3>Dokumentenmetriken</h3>
		<h4>Anzahl der Zeichen:</h4>
		<h4>{{ numChars }}</h4>
		<h4>Anzahl der Wörter:</h4>
		<h4>{{ numWords }}</h4>
	</div>
</template>

<script>
import Communication from "../../classes/communication";

export default {
	name: "documentMetrics",
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
	},
	data() {
		return {
			ws: null,
			numChars: 0,
			numWords: 0,
		};
	},
	computed: {
		elementId() {
			return `documentMetrics_${this.id}`;
		},
	},
	mounted() {
		if (!this.isMock) {
			this.openSocket();
		}
		this.$emit("dashboardDimensions", this.getDashboardDimensions);
	},
	unmounted() {
		if (!this.isMock) {
			this.closeSocket();
		}
	},
	methods: {
		openSocket() {
			this.ws = Communication.openSocket("wstest", {
				padName: this.padName,
			});
			this.ws.on("update", (msg) => {
				this.numChars = msg[0];
				this.numWords = msg[1];
			});
		},
		closeSocket() {
			this.ws.disconnect();
		},
		getDashboardDimensions() {
			return {w: 3, h: 6};
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
</style>