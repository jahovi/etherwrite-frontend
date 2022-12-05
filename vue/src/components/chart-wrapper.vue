<template>
	<div class="chart-wrapper" :id="'chart-wrapper-' + id" :name="component">
		<component :is="component" :id="'custom-chart-' + id" :isMock="isMock" @click.capture="stopEvents"/>
		<slot name="btn"></slot>
	</div>
</template>

<script lang="js">
import authoringRatios_bar from "./charts/authoringRatios_bar.vue";
import authoringRatios_pie from "./charts/authoringRatios_pie.vue";

export default {
	name: "chart-wrapper",
	components: {
		authoringRatios_pie,
		// eslint-disable-next-line vue/no-unused-components
		authoringRatios_bar,
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
.chart-wrapper {
	border: 1px solid rgba(0, 0, 0, 0.125);
	padding: 12px;
}
</style>
