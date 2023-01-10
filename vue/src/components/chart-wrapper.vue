<template>
	<div class="chart-wrapper" :id="'chart-wrapper-' + id" :name="component">
		<component :padName="padName" :is="component" :id="'custom-chart-' + id" :isMock="isMock" @click.capture="stopEvents" style="height: 100%"/>
		<slot name="btn"></slot>
	</div>
</template>

<script lang="js">
import authoringRatios_bar from "./charts/authoringRatios_bar.vue";
import authoringRatios_pie from "./charts/authoringRatios_pie.vue";
import participation_diagram from "./charts/participation_diagram.vue";
import groupParticipants from "./charts/groupParticipants.vue";
import operations_bar from "./charts/operations/operations_bar.vue";
import activityOverTime from "./charts/activityOverTime.vue";
import etherViz from "./charts/etherViz.vue";
import cohesionDiagram from "./charts/cohesionDiagram.vue";

export default {
	name: "chart-wrapper",
	components: {
		authoringRatios_pie,
		authoringRatios_bar,
		participation_diagram,
		groupParticipants,
		operations_bar,
		activityOverTime,
		etherViz,
		cohesionDiagram,
	},
	props: {
		component: String,
		id: Number,
		isMock: Boolean,
		padName: String,
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
