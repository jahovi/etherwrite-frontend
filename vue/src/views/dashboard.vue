<template>
  <div class="container-fluid">
    <!-- project dashboard -->
    <div id="project-dashboard" class="row border-bottom dashboard">
      <h3 class="col-12"></h3>
      <div class="col-12 mt-4 board-area" style="display: flex; gap: 8px">
        <ChartWrapper
            v-for="(widget, key) in projectCharts"
            :component="widget.component"
            :id="widget.id"
            :key="key"
            :padName="padName"
            style="width: 32%"
        >
        </ChartWrapper>
        <Countdown/>
      </div>
    </div>
    <!-- custom dashboard -->
    <div id="custom-dashboard" class="row d-flex dashboard">
      <!-- Info on how to move widgets -->
      <small id="moveInfo" v-if="userCharts.length">
        <i class="fa fa-info-circle text-info mr-1"></i>
        <span class="mr-1">{{ getStrings["dashboard.move"] }}</span>
        <span>
          <kbd v-if="isMacOS">CMD</kbd><kbd v-else>CTRL</kbd> + <kbd>{{ getStrings["leftclick"] }}</kbd>
        </span>
      </small>
      <div class="flex-grow-1"></div>
      <!-- add widget button -->
      <button
          id="open-widget-catalog-btn"
          class="btn btn-success rounded p-2"
          @click.prevent="openWidgetCatalog()"
      >
        <i class="fa fa-plus"></i>
      </button>
      <!-- grid -->
      <div class="col-12">
        <div id="placeholder" v-if="!userCharts.length">
          <div id="placeholder_arrow">
            <i class="fa fa-reply"></i>
          </div>
          <div id="placeholder_cta">
            <strong>{{ getStrings["dashboard.nothingToSee.title"] }}</strong>
            <br/><br/>
            <p>
              <button class="btn btn-success rounded p-2" disabled>
                <i class="fa fa-plus"></i>
              </button>
              {{ getStrings["dashboard.nothingToSee.cta"] }}
            </p>
          </div>
        </div>
        <GridLayout v-if="userCharts.length"
                    :layout.sync="userCharts"
                    :col-num="colNum"
                    :cols="{ lg: 12, md: 10, sm: 6, xs: 4, xxs: 2 }"
                    :row-height="rowHeight"
                    :is-draggable="draggable"
                    :is-resizable="resizable"
                    :responsive="responsive"
                    :vertical-compact="true"
                    :prevent-collision="false"
                    :use-css-transforms="false"
        >
          <GridItem
              v-for="item in userCharts"
              :x="item.x"
              :y="item.y"
              :w="item.w"
              :h="item.h"
              :i="item.i"
              :key="item.i"
              @moved="saveGrid"
              @resized="saveGrid"
          >
            <ChartWrapper
              :component="item.component"
              :padName="padName"
              :id="item.i"
              :key="item.i"
              :w="getWidthOfElement(item.w)"
              :h="getHeightOfElement(item.h)"
              @rearrangeDashboard="() => doRearrangeDashboard(item)"
            >
            </ChartWrapper>

            <span class="remove" @click="removeItemFromGrid(item)">
              <i class="fa fa-close"></i>
            </span>
          </GridItem>
        </GridLayout>
      </div>
    </div>
    <WidgetCatalog
      @add-widget-event="addWidget($event)"
      :selectedWidgets="userCharts"
    />
  </div>
</template>

<script lang="js">
import { GridItem, GridLayout } from "vue3-grid-layout";
import ChartWrapper from "../components/chart-wrapper.vue";
import WidgetCatalog from "../components/widget-catalog.vue";
import Communication from "../classes/communication";
import { v4 as uuid } from "uuid";
import Countdown from "../components/kpis/countdown.vue";


export default {
	name: "dashboardView",
	components: {
		GridLayout,
		GridItem,
		ChartWrapper,
		WidgetCatalog,
		Countdown,
	},
	data: () => ({
		projectCharts: [],
		userCharts: [],
		draggable: false,
		resizable: true,
		responsive: true,
		colNum: 12,
		rowHeight: 30,
	}),
	computed: {
		editorInstance() {
			const padName = this.$route.params.padName;
			return this.$store.state.base.editorInstances.find(e => e.padName === padName);
		},
		getStrings() {
			return this.$store.getters.getStrings;
		},
		users() {
			return this.$store.state.users.users;
		},
		padName() {
			return this.editorInstance.padName;
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
		getWidthOfElement(width) {
			const result = document.getElementById("custom-dashboard").clientWidth * (width / this.colNum);
			return result;
		},
		getHeightOfElement(height) {
			const result = this.rowHeight * height;
			return result;
		},
		saveGrid() {
			Communication.webservice("saveDashboard", {
				cmid: this.$store.getters.getCMID,
				widgets: this.userCharts.map(this.detransformWidget),
			});
		},
		onKeyDown(e) {
			if (e.key === "Control" || e.key === "Meta") {
				this.draggable = true;
			}
		},
		onKeyUp(e) {
			if (e.key === "Control" || e.key === "Meta") {
				this.draggable = false;
			}
		},
		/**
		 * Move existing widgets down to make room for a newly added one. Is called through an event bubbling up through the chart wrapper 
		 * from the mounted() method of each widget.
		 * 
		 * @param {*} item The newly added widget to accommodate
		 */
		doRearrangeDashboard(item) {
			if (this.userCharts.length > 1) {
				this.userCharts.forEach((e) => {
					if (e != item) {
						e.y = e.y + item.h;
					}
				});
				this.saveGrid();
			}
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
				category: String,
				configuration: Object,
				x: Number,
				y: Number,
				defaultWidth: Number,
				defaultHeight: Number,
				w: Number,
				h: Number,
			} widget Widget to be added to dashboard.
		 */
		addWidget(newWidget) {
			newWidget.w = newWidget.defaultWidth || newWidget.w;
			newWidget.h = newWidget.defaultHeight || newWidget.h;
			newWidget.x = 0;//newWidget.x || (this.userCharts.length * 2) % (this.colNum || 12);
			newWidget.y = 0;//newWidget.y || this.userCharts.length + (this.colNum || 12);
			// { "x": 0, "y": 0, "w": 2, "h": 2, "i": "0"} neccessary
			newWidget.i = uuid();
			this.userCharts.push(newWidget);
			this.saveGrid();
		},
		transformWidget(widgetData) {
			return {
				i: widgetData.uuid,
				component: widgetData.component,
				configuration: {},
				x: parseInt(widgetData.x),
				y: parseInt(widgetData.y),
				w: parseInt(widgetData.w),
				h: parseInt(widgetData.h),
			};
		},
		detransformWidget(widgetData) {
			return {
				uuid: widgetData.i,
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
			this.userCharts = result.user.map(this.transformWidget);
			// Make the UI resize according to the rendered grid.
			window.dispatchEvent(new Event("resize"));
		});

		document.addEventListener("keydown", this.onKeyDown);
		document.addEventListener("keyup", this.onKeyUp);
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
  justify-content: top;
  align-content: flex-start;
  min-height: 800px;
}

.btn {
  width: 30px;
  height: 30px;
  margin: 5px;
  justify-self: center;
  padding: 0 !important;
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

#moveInfo {
  opacity: 0.7;
}

#placeholder {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  opacity: 0.7;
  margin-top: 10px;
  gap: 5px;
}

#placeholder_cta {
  width: 100%;
  max-width: 500px;
  background-color: lightgrey;
  border-radius: 5px;
  padding: 20px;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.2;
  }
  100% {
    opacity: 1;
  }
}

#placeholder_arrow {
  width: 100%;
  text-align: right;
  animation: pulse 1s infinite linear;
}

#placeholder_arrow > i {
  margin-right: -10px;
  font-size: 30pt;
  color: lightgrey;
  transform: rotate(90deg);
}
</style>
