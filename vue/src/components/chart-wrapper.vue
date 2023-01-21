<template>
	<div class="chart-wrapper" :id="'chart-wrapper-' + id" :name="component">
		<component
				:padName="padName"
				:is="component"
				:id="'custom-chart-' + id"
				:isMock="isMock"
				:w="w"
				:h="h"
				@click.capture="stopEvents"
				@dashboardDimensions="setDashboardDimensions"
				style="height: 100%; overflow-x: hidden; overflow-y: hidden"
		/>
		<slot name="btn"></slot>
		<button
				class="btn info-btn-circle border fa fa-question"
				@focusin.prevent="toggleInfoPanel(true)"
				@focusout.prevent="toggleInfoPanel(false)"
		></button>
		<div
				:id="'chart-wrapper-info' + id"
				class="info-panel border rounded"
				v-if="isInfoPanelOpen"
				v-html="getStrings[`widget-info-${component}`]"></div>
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
import documentMetrics from "./charts/documentMetrics.vue";


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
		documentMetrics,
	},
	props: {
		component: String,
		id: String,
		isMock: Boolean,
		padName: String,
		w: Number,
		h: Number,
	},
	data: function () {
		return {
			isInfoPanelOpen: false,
		};
	},
	mounted() {
	},
	methods: {
		setDashboardDimensions(dimensions) {
			this.$emit("dashboardDimensions", dimensions);
		},
		stopEvents(event) {
			if (this.isMock) {
				event.stopPropagation();
				event.preventDefault();
			}
		},
		toggleInfoPanel(bool) {
			this.isInfoPanelOpen = bool;
		},
	},
	computed: {
		getStrings() {
			return this.$store.getters.getStrings;
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

.info-btn-circle {
	position: absolute;
	bottom: 2px;
	left: 2px;
	width: 30px;
	height: 30px;
	text-align: center;
	padding: 6px;
	font-size: 1em;
	border-radius: 15px;
}

.info-panel {
	position: absolute;
	overflow-y: auto;
	background-color: white;
	z-index: 999;
	padding: 10px;
}
</style>
