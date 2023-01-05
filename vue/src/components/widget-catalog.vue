<template>
	<div id="widget-catalog">
		<div class="widget-catalog-backdrop" @click.prevent="closeWidgetCatalog" v-if="isWidgetCatalogOpen"></div>
		<div class="widget-catalog-panel border rounded" v-if="isWidgetCatalogOpen">
			<!-- tabs nav -->
			<ul class="nav nav-tabs mb-3 col-10" id="ex1" role="tablist">
				<li class="nav-item" :class="{'active': cat === category }" v-for="(cat, key) in categories" :key="key">
					<a class="nav-link" role="tab" href="#" @click="category = cat" aria-selected="true">
						{{ i18n[`widgets.${cat}`] }}
					</a>
				</li>
			</ul>
			<!-- tabs content -->
			<div role="tabpanel" v-if="category">
				<ChartWrapper class="wrapper border rounded" v-for="(widget, key) in widgetsOfCurrentCategory"
											:isMock="true" :component="widget.component" :id="widget.id" :key="key">
					<template #btn>
						<button :id="'add-widget-btn-' + widget.id" class="btn btn-success rounded btn-add-widget"
										@click.prevent="addToDashboard(widget)">
							<i class="fa fa-plus"></i>
						</button>
					</template>
				</ChartWrapper>
			</div>
			<!-- close widget-catalog button -->
			<button id="close-widget-catalog-btn" class="btn rounded btn-danger" @click.prevent="closeWidgetCatalog">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>
</template>

<script lang="js">
import ChartWrapper from "../components/chart-wrapper.vue";
import store from "../store";

export default {

	name: "widgetCatalog",
	components: {
		ChartWrapper,
	},
	data: () => ({
		category: null,
	}),
	computed: {
		isWidgetCatalogOpen() {
			return this.$store.getters.isWidgetCatalogOpen;
		},
		/**
		 * get widgets from widget-store.
		 */
		widgets() {
			return this.$store.getters.getWidgets;
		},
		/**
		 * Get categories from widget-store.
		 */
		categories() {
			return this.$store.getters.getWidgetCategories;
		},
		widgetsOfCurrentCategory() {
			return this.widgets
					.filter(widget => widget.category === this.category.toLowerCase());
		},
		i18n() {
			return this.$store.getters.getStrings;
		},
	},
	watch: {},
	methods: {
		closeWidgetCatalog() {
			store.commit("setWidgetCatalogOpen");
		},
		addToDashboard(widget) {
			this.$emit("add-widget-event", widget);
			this.closeWidgetCatalog();
		},
	},
	mounted() {
		this.category = this.$store.getters.getWidgetCategories[0];
	},
};
</script>

<style scoped lang="css">
.widget-catalog-backdrop {
	background-color: rgba(0, 0, 0, 0.5);
	width: 100%;
	height: 100%;
	position: fixed;
	top: 0;
	left: 0;
}

.widget-catalog-panel {
	position: fixed;
	overflow-y: auto;
	background-color: white;
	top: 10%;
	left: 16.65%;
	height: 75%;
	width: 66.6%;
	z-index: 999;
	padding: 10px;
}

/* widgets */
.wrapper {
	margin-bottom: 5px;
	filter: grayscale(50%);
	border: 1px solid grey;
	padding: 8px;
}

/* close button */
.btn {
	position: absolute;
	top: 5px;
	right: 5px;
	width: 30px;
	height: 30px;
}

.fa {
	position: absolute;
	top: 7px;
	left: 8px;
	font-size: 1em;
}

.nav-item.active {
	background-color: #0F6CBF;
	color: white;
}

.nav-item.active * {
	color: white !important;
}

</style>
