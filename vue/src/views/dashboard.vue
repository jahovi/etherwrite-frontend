<template>
	<div class="container-fluid">
		<!-- project dashboard -->
		<div id="project-dashboard" class="row border-bottom dashboard">
			<h3 class="col-12">Projekt Dashboard</h3>
			<div class="col-12 mt-4 board-area" style="display: flex; gap: 8px">
				<ChartWrapper v-for="(widget, key) in projectCharts" :component="widget.component" :id="widget.id"
											:key="key" style="width: 32%">
				</ChartWrapper>
			</div>
		</div>
		<!-- custom dashboard -->
		<div id="custom-dashboard" class="row d-flex dashboard">
			<h3 class="col-6 mr-auto p-2">Dein Dashboard</h3>
			<!-- add widget button -->
			<button id="open-widget-catalog-btn" class="btn btn-success rounded p-2"
							@click.prevent="openWidgetCatalog()">
				<i class="fa fa-plus"></i>
			</button>
			<!-- grid -->
			<div class="col-12">
				<GridLayout
						:layout.sync="userCharts"
						:col-num="12"
						:cols="{ lg: 4, md: 4, sm: 2, xs: 1, xxs: 1}"
						:row-height="300"
						:is-draggable="draggable"
						:is-resizable="resizable"
						:responsive="responsive"
						:vertical-compact="true"
						:use-css-transforms="true">
					<GridItem v-for="(item, key) in userCharts"
										:x="item.x"
										:y="item.y"
										:w="item.w"
										:h="item.h"
										:i="item.i" :key="item.i"
										@moved="saveGrid"
										@resized="saveGrid">

						<ChartWrapper :component="item.component" :id="item.id" :key="key">
						</ChartWrapper>

						<span class="remove" @click="removeItemFromGrid(item)">
							<i class="fa fa-close"></i>
						</span>
					</GridItem>
				</GridLayout>
			</div>
		</div>
		<WidgetCatalog @add-widget-event="addWidget($event)"/>
	</div>
</template>

<script lang="js">
import { GridItem, GridLayout } from "vue3-grid-layout";
import ChartWrapper from "../components/chart-wrapper.vue";
import WidgetCatalog from "../components/widget-catalog.vue";
import Communication from "../classes/communication";
import { v4 as uuid } from "uuid";

export default {
	name: "dashboardView",
	components: {
		GridLayout,
		GridItem,
		ChartWrapper,
		WidgetCatalog,
	},
	data: () => ({
		projectCharts: [],
		userCharts: [],
		draggable: true,
		resizable: true,
		responsive: true,
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
			const widgets = this.userCharts.map(child => {
				let id = child.id;
				const component = child.component;
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
		removeItemFromGrid: function (val) {
			this.userCharts = this.userCharts
					.filter(u => u !== val);
			this.saveGrid();
		},
		/**
		 * Open widget catalog.
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
			newWidget.w = newWidget.w || newWidget.minW;
			newWidget.h = newWidget.h || newWidget.minH;
			newWidget.x = newWidget.x || 0;
			newWidget.y = newWidget.y || 0;
			// { "x": 0, "y": 0, "w": 2, "h": 2, "i": "0"} neccessary
			newWidget.i = uuid();
			this.userCharts.push(newWidget);
			this.saveGrid();
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
		const cmid = this.$store.getters.getCMID;
		Communication.webservice("getDashboards", {cmid}).then(result => {
			this.projectCharts = result.project.map(this.transformWidget);

			result.user.map(this.transformWidget).map(this.addWidget);
			// Make the UI resize according to the rendered grid.
			window.dispatchEvent(new Event("resize"));
		});
		this.$store.dispatch("users/load");
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

.btn {
	width: 30px;
	height: 30px;
	margin: 5px;
	justify-self: center;
}

.fa {
	position: relative;
	top: -5px;
	left: 0px;
	font-size: 1em;
}

/* grid */
.remove {
	position: absolute;
	right: 5px;
	top: 5px;
	cursor: pointer;
}

.vue-grid-layout {
	background: transparent;
}

.vue-grid-item:not(.vue-grid-placeholder) {
	background: transparent;
	border: 1px solid rgba(0, 0, 0, 0.125);
}

.vue-grid-item .resizing {
	opacity: 0.9;
}

.vue-grid-item .no-drag {
	height: 100%;
	width: 100%;
}

.vue-grid-item .minMax {
	font-size: 12px;
}

.vue-grid-item .add {
	cursor: pointer;
}

.vue-draggable-handle {
	position: absolute;
	width: 20px;
	height: 20px;
	top: 0;
	left: 0;
	background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10'><circle cx='5' cy='5' r='5' fill='#999999'/></svg>") no-repeat;
	background-position: bottom right;
	padding: 0 8px 8px 0;
	background-repeat: no-repeat;
	background-origin: content-box;
	box-sizing: border-box;
	cursor: pointer;
}
</style>
