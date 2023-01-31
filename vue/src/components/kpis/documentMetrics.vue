<template>
	<div>
		<u>{{ getStrings["document-metrics-chars"] }}</u>
		{{`${this.numChars}, `}}
		<u>{{ getStrings["document-metrics-words"] }}</u>
		{{`${this.numWords}` }}
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
	},
};
</script>

<style scoped lang="css">

</style>
