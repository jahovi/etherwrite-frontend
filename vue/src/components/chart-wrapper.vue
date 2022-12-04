<template>
	<div :id="'chart-wrapper-' + id">
		{{ component }}
		<component :is="component" :id="'custom-chart-' + id"/>
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
	},
	data: function () {
		return {};
	},
	mounted() {
	},
};
</script>

<style scoped lang="css">

</style>
