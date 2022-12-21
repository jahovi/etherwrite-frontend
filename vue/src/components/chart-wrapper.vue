<template>
	<div class="chart-wrapper" :id="'chart-wrapper-' + id" :name="component">
		<component :is="component" :id="'custom-chart-' + id" :isMock="isMock" @click.capture="stopEvents"/>
		<slot name="btn"></slot>
	</div>
</template>

<script lang="js">
import authoringRatios_bar from "./charts/authoringRatios_bar.vue";
import authoringRatios_pie from "./charts/authoringRatios_pie.vue";
import participation_diagram from "./charts/participation_diagram.vue";
import groupParticipants from "./charts/groupParticipants.vue";

export default {
	name: "chart-wrapper",
	components: {
		authoringRatios_pie,
		authoringRatios_bar,
		participation_diagram,
		groupParticipants,
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
	width: 100%;
	height: 100%;
}
</style>
