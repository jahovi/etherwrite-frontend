<template>
	<div :id="'chart-wrapper-' + id" :name="component">
		<component :is="component" :id="'custom-chart-' + id" :isMock="isMock" @click.capture="stopEvents"/>
		<slot name="btn"></slot>
	</div>
</template>

<script lang="js">
import barchart from "./charts/barchart.vue";
import authoringRatios from "./charts/authoringRatios.vue";

export default {
	name: "chart-wrapper",
	components: {
		// eslint-disable-next-line vue/no-unused-components
		barchart,
		// eslint-disable-next-line vue/no-unused-components
		authoringRatios,
	},
	props: {
		component: String,
		id: Number,
		isMock: Boolean,
	},
	data: function () {
		return {};
	},
	mounted() {
		// button clickable, component not clickable -> for wrapper in widget catalog
		if (this.isMock) {
			let comp = document.getElementById("custom-chart-" + this.id);
			comp.style.pointerEvents = "none";
		}
	},
	methods: {
		stopEvents(event) {
			if (this.isMock) {
				event.stopPropagation();
				event.preventDefault();
			}
		},
	},
};
</script>

<style scoped lang="css">

</style>
