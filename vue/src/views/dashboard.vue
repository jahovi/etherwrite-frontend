<template>
	<div class="container-fluid">
		<!-- project dashboard -->
		<div id="project-dashboard" class="row border-bottom dashboard">
			<h3 class="col-12">Projekt Dashboard</h3>
			<div class="col-12">
				<strong>Aktive Benutzer(innen):</strong>
				<br />
				<span class="badge badge-light badge-rounded pa-1" v-for="user in users" :key="user.id">
					{{ user.fullName }}
				</span>
			</div>
			<div class="col-12 mt-4 board-area">
				<ChartWrapper v-for="(widget, key) in projectCharts" :component="widget.component" :id="widget.id"
					:key="key">
				</ChartWrapper>
			</div>
		</div>
		<!-- custom dashboard -->
		<div id="custom-dashboard" class="row dashboard">
			<h3 class="col-12">Dein Dashboard</h3>
			<!-- add chart wrapper button -->
			<button id="add-chart-btn" class="btn btn-primary rounded" @click.prevent="openWidgetCatalog()">Hinzufügen
			</button>
			<!-- save dashboard button -->
			<button id="save-dashboard-btn" class="btn btn-primary rounded" @click.prevent="saveGrid">Speichern</button>
			<!-- load dashboard button -->
			<button id="load-dashboard-btn" class="btn btn-primary rounded" @click.prevent="loadGrid">Laden</button>
			<!-- compact dashboard button -->
			<button id="compact-btn" class="btn btn-primary rounded" @click.prevent="compact()">Verdichten</button>
			<!-- clear dashboard button -->
			<button id="clear-btn" class="btn btn-primary rounded" @click.prevent="clear()">Leeren</button>
			<!-- toggle float button -->
			<button id="toggle-float-btn" class="btn btn-primary rounded" @click.prevent="toggleFloat()">Toggle
				Float: {{ floatOption }}
			</button>
			<!-- trash -->
			<div class="col-12 text-danger text-center">Toggle Float muss false sein, wenn ein Diagramm
				hinzugefügt wird, sonst alles &#128163; &#129327; &#9760; !!!
			</div>
			<div class="col-6 offset-3 delete-wrapper">
				<i class="fa fa-trash-o"></i>
			</div>
			<!-- gridstack -->
			<div id="grid-stack" class="col-12 ">
				<div class="grid-stack">
				</div>
			</div>
		</div>
		<WidgetCatalog @add-widget-event="addWidget($event)" />
	</div>
</template>

<script lang="js">
import { createApp, defineComponent, reactive } from "vue";
import store from "../store";
import { GridStack } from "gridstack";
import ChartWrapper from "../components/chart-wrapper.vue";
import WidgetCatalog from "../components/widget-catalog.vue";
import "gridstack/dist/gridstack.css";
import Communication from "../classes/communication";
import {createApp, defineComponent, reactive} from "vue";
import store from "../store";

export default {
	name: "dashboardView",
	components: {
		ChartWrapper,
		WidgetCatalog,
	},
	data: () => ({
		projectCharts: [],
		userCharts: [],
		grid: null,
		floatOption: false,
	}),
	computed: {
		getStrings() {
			return this.$store.getters.getStrings;
		},
		users() {
			return this.$store.state.users.users;
		},
		padName() {
			return this.$store.state.base.padName;
		},
		isLoading() {
			return !this.$store.state.base.initialized;
		},
	},
	watch: {},
	methods: {
		/**
		 * Save custom dashboard.
		 */
		saveGrid() {
			let saveData = this.grid.save(true, true);
			localStorage.removeItem("modWriteCustomDashboardSave");
			localStorage.setItem("modWriteCustomDashboardSave", JSON.stringify(saveData));
			Communication.webservice("saveDashboard", {
				cmid: this.$store.getters.getCMID,
				widgets: this.userCharts.map(this.detransformWidget),
			});
		},
		/**
		 * Toggle float option for gridstack.
		 */
		toggleFloat() {
			this.grid.float(!this.grid.getFloat());
			this.floatOption = this.grid.getFloat();
		},
		/**
		 * Compact custom dashboard.
		 */
		compact() {
			this.grid.compact();
		},
		/**
		 * Clear custom dashboard.
		 */
		clear() {
			this.grid.removeAll();
		},
		/**
		 * Open the widget catalog.
		 */
		openWidgetCatalog() {
			this.$store.commit("setWidgetCatalogOpen");
		},
		/**
		 * Add widget to dashboard. 
		 * @param {
				component: String,
				id: Number,
				category: String,
				configuration: Object,
				options: {
					x: Number,
					y: Number,
					minW: Number,
					minH: Number,
					w: Number,
					h: Number,
				}
			} widget Widget to be added to dashboard. 
		 */
		addWidget(newWidget) {
			this.userCharts.push(newWidget);
			// create grid stack widget
			let chartWrapper = defineComponent(ChartWrapper);
			let props = reactive({
				id: newWidget.id,
				component: newWidget.component,
			});
			let div = document.createElement("div");
			createApp(chartWrapper, props).use(store).mount(div);

			this.widgetCount += 1;
			this.grid.addWidget({
				x: newWidget.options.x,
				y: newWidget.options.y,
				minW: newWidget.options.minW,
				minH: newWidget.options.minH,
				w: newWidget.options.w,
				h: newWidget.options.h,
				content: div.innerHTML,
			});

		},
		transformWidget(widgetData) {
			// TODO eventually parse the backend's widget configuration.
			return {
				id: widgetData.id,
				component: widgetData.component,
				configuration: {},
			};
		},
		detransformWidget(widgetData) {
			return {
				...widgetData,
				configuration: JSON.stringify({}),
			};
		},
	},
	mounted() {
		// init grid
		let options = {
			float: false, // TODO: fix: float option true crashes on addWidget() and makeWidget()
			// TODO: move widgets by single column, not only by w columns
			removable: ".delete-wrapper",
			minRow: 1,
			cellHeight: "300px",
			alwaysShowResizeHandle: true,
		};
		let selector = ".grid-stack";

		this.grid = GridStack.init(options, selector);

		const cmid = this.$store.getters.getCMID;
		Communication.webservice("getDashboards", { cmid }).then(result => {
			this.projectCharts = result.project.map(this.transformWidget);
			this.userCharts = result.user.map(this.transformWidget);

			this.userCharts.map(this.addChartWrapper);
		});
		this.$store.dispatch("users/load");
	},
};
</script>

<style scoped lang="css">
h3 {
	align-self: flex-start;
	text-decoration: underline;
	margin-bottom: 20px;
}

/* dashboards */
.dashboard {
	padding: 10px 0 10px 0;
}

#custom-dashboard {
	justify-content: center;
}

.delete-wrapper {
	display: flex;
	height: 100px;
	margin: 0;
	background-color: rgba(255, 0, 0, 0.2);
	border: 0 transparent;
	border-radius: 5px;
	align-items: center;
	justify-content: center;
}

.delete-wrapper:hover {
	border: 5px solid rgba(139, 0, 0, 0.5)
}

.btn {
	width: 150px;
	margin: 0 10px 10px 10px;
	justify-self: center;
}

/* grid stack */
.grid-stack:deep(.grid-stack-item-content) {
	border: 1px solid rgba(0, 0, 0, 0.125);
}

/* fontawesome */
.fa {
	font-size: 3em;
	color: lightslategray;
}
</style>
