<template>
	<div class="container-fluid">
		<!-- project dashboard -->
		<div id="project-dashboard" class="row border-bottom dashboard">
			<h3 class="col-12">Projekt Dashboard</h3>
			<div class="col-12">
				<strong>Aktive Benutzer(innen):</strong>
				<br/>
				<span class="badge badge-success badge-rounded pa-1 ml-1" v-for="user in users" :key="user.id">
					{{ user.fullName }}
				</span>
			</div>
			<div class="col-12 mt-4 board-area" style="display: flex; gap: 8px">
				<ChartWrapper v-for="(widget, key) in projectCharts" :component="widget.component" :id="widget.id"
											:key="key" style="width: 32%">
				</ChartWrapper>
			</div>
		</div>
		<!-- custom dashboard -->
		<div id="custom-dashboard" class="row d-flex dashboard">
			<h3 class="col-12 col-md-6 mr-auto p-2">Dein Dashboard</h3>
			<!-- add chart wrapper button -->
			<button id="add-chart-btn" class="btn btn-primary rounded p-2" @click.prevent="openWidgetCatalog()">
				<i class="fa fa-plus-square-o"></i>
			</button>
			<!-- compact dashboard button -->
			<button id="compact-btn" class="btn btn-primary rounded p-2" v-if="!oneColumnMode"
							@click.prevent="compact()">
				<i class="fa fa-compress"></i>
			</button>
			<!-- trash -->
			<div class="delete-wrapper p-2">
				<i class="fa fa-trash-o"></i>
			</div>
			<!-- gridstack -->
			<div class="col-12 grid-stack"></div>
		</div>
		<WidgetCatalog @add-widget-event="addWidget($event)"/>
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

export default {
	name: "dashboardView",
	components: {
		ChartWrapper,
		WidgetCatalog,
	},
	data: () => ({
		projectCharts: [{
			id: 1,
			component: "authoringRatios_pie",
		},
			{
				id: 2,
				component: "authoringRatios_bar",
			}],
		userCharts: [],
		grid: null,
		widgetCount: 0,
		windowWidth: window.innerWidth,
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
		oneColumnMode() {
			return this.grid && this.grid.opts.oneColumnSize > this.windowWidth;
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
			const widgets = saveData.children.map(child => {
				let id = child.content.match(/id=\"chart-wrapper-(\d+|undefined)\"/)[1];
				const component = child.content.match(/name=\"(\w+)\"/)[1];
				id = id === "undefined" ? undefined : parseInt(id);
				return {
					id,
					component,
					x: child.x || 0,
					y: child.y || 0,
					w: child.w || child.minW || 1,
					h: child.h || child.minH || 1,
				};
			});
			Communication.webservice("saveDashboard", {
				cmid: this.$store.getters.getCMID,
				widgets: widgets.map(this.detransformWidget),
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
				x: Number,
				y: Number,
				minW: Number,
				minH: Number,
				w: Number,
				h: Number,
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

			newWidget.w = newWidget.w || newWidget.minW;
			newWidget.h = newWidget.h || newWidget.minH;
			newWidget.x = newWidget.x || 0;
			newWidget.y = newWidget.y || 0;

			this.grid.addWidget({
				x: newWidget.x,
				y: newWidget.y,
				minW: newWidget.minW,
				minH: newWidget.minH,
				w: newWidget.w,
				h: newWidget.h,
				content: div.innerHTML,
			});

			createApp(chartWrapper, props).mount(div);
			this.widgetCount += 1;
			this.grid.addWidget({
				x: newWidget.x,
				y: newWidget.y,
				minW: newWidget.minW,
				minH: newWidget.minH,
				w: newWidget.w,
				h: newWidget.h,
				content: div.innerHTML,
			});
		},
		transformWidget(widgetData) {
			return {
				id: widgetData.id,
				component: widgetData.component,
				configuration: {},
				x: widgetData.x,
				y: widgetData.y,
				w: widgetData.w,
				h: widgetData.h,
			};
		},
		detransformWidget(widgetData) {
			return {
				id: widgetData.id,
				component: widgetData.component,
				configuration: JSON.stringify({}),
				x: widgetData.x,
				y: widgetData.y,
				w: widgetData.w,
				h: widgetData.h,
			};
		},
	},
	mounted() {
		// window resize listener
		window.onresize = () => {
			this.windowWidth = window.innerWidth;
		};

		const cmid = this.$store.getters.getCMID;
		Communication.webservice("getDashboards", {cmid}).then(result => {
			// this.projectCharts = result.project.map(this.transformWidget);

			result.user.map(this.transformWidget).map(this.addWidget);
		});
		this.$store.dispatch("users/load");

		// init grid
		this.grid = GridStack.init({
			// TODO: move widgets by single column, not only by w columns
			removable: ".delete-wrapper",
			minRow: 2,
			cellHeight: "300px",
			alwaysShowResizeHandle: true,
		});

		this.grid.load(this.userCharts);

		// save dashboard layout on added widget
		this.grid.on("added", () => {
			this.saveGrid();
		});
		// save dashboard layout on removed widget
		this.grid.on("removed", () => {
			this.saveGrid();
		});
		// save dashboard layout on drag stop
		this.grid.on("dragstop", () => {
			this.saveGrid();
		});
		// save dashboard layout on resize stop
		this.grid.on("resizestop", () => {
			this.saveGrid();
		});
	},
};
</script>

<style scoped lang="css">
h3 {
	align-self: flex-start;
	text-decoration: underline;
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
	height: 50px;
	width: 50px;
	margin: 5px;
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
	width: 50px;
	height: 50px;
	margin: 5px;
	justify-self: center;
}

/* fontawesome */
.fa {
	font-size: 2em;
	color: lightgray;
}

.fa-trash-o {
	color: lightslategray;
}
</style>
