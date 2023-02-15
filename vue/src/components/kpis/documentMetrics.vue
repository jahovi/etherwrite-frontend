<template>
  <div class="p-3">
    <u>{{ getStrings["document-metrics-chars"] }}</u>
    {{ `${this.numChars}, ` }}
    <u>{{ getStrings["document-metrics-words"] }}</u>
    {{ `${this.numWords}` }}
  </div>
</template>

<script lang="js">
import Communication from "../../classes/communication";

export default {
	name: "documentMetrics",
	props: {
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
		getStrings() {
			return this.$store.getters.getStrings;
		},
	},
	mounted() {
		this.openSocket();
	},
	unmounted() {
		this.closeSocket();
	},
	methods: {
		openSocket() {
			this.ws = Communication.openSocket("documentmetrics", {
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
	watch: {
		padName() {
			this.closeSocket();
			this.openSocket();
		},
	},
};
</script>

<style scoped lang="css">

</style>
