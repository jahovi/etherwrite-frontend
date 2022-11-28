<template>
	<div :id="'chart-wrapper-' + id">
		{{ component }}
		<component :is="component" :id="'custom-chart-' + id" :mockOnly="mockOnly" />
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
		mockOnly: Boolean,
	},
	data: function () {
		return {};
	},
	mounted() {
		// button clickable, component not clickable -> for wrapper in widget catalog
		if (this.mockOnly) {
			let comp = document.getElementById("custom-chart-" + this.id);
			comp.style.pointerEvents = "none";
		}
	},
};
</script>

<style scoped lang="css">

</style>
