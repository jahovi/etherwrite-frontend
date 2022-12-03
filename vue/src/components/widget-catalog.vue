<template>
	<div id="widget-catalog">
		<div class="widget-catalog-backdrop" @click.prevent="closeWidgetCatalog" v-if="isWidgetCatalogOpen"></div>
		<div class="widget-catalog-panel border rounded" v-if="isWidgetCatalogOpen">
			<!-- tabs nav -->
			<ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
				<li class="nav-item" :class="key === 0 ? 'active' : ''" role="presentation"
						v-for="(category, key) in categories" :key="key">
					<a class="nav-link" :id="'ex1-tab-' + key" data-mdb-toggle="tab" :href="'#ex1-tabs-' + key" role="tab" :aria-controls="'ex1-tabs-' + key"
						 aria-selected="true">{{ category }}</a>
				</li>
			</ul>
			<!-- tabs content -->
			<div class="tab-content" id="ex1-content">
				<div class="tab-pane fade show" :class="key === 0 ? 'active' : ''" :id="'ex1-tabs-' + key" role="tabpanel" :aria-labelledby="'ex1-tab-' + key"
						 v-for="(category, key) in categories" :key="key">
					<ChartWrapper class="wrapper border rounded" v-for="(widget, key) in widgets"
												v-show="(widget.category === category.toLowerCase())" :isMock="true"
												:component="widget.component" :id="widget.id" :key="key">
						<template #btn>
							<button :id="'add-widget-btn-' + widget.id" class="btn btn-success btn-add-widget"
											@click.prevent="addToDashboard(widget)">
								<i class="fa fa-plus"></i>
							</button>
						</template>
					</ChartWrapper>
				</div>
			</div>
			<!-- close widget-catalog button -->
			<button id="close-widget-catalog-btn" class="btn btn-danger" @click.prevent="closeWidgetCatalog">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>
</template>

<script lang="js">
import ChartWrapper from "../components/chart-wrapper.vue";

export default {

	name: "widgetCatalog",
	components: {
		ChartWrapper,
	},
	data: () => ({}),
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
	},
	watch: {},
	methods: {
		closeWidgetCatalog() {
			this.$store.commit("setWidgetCatalogOpen");
		},
		addToDashboard(widget) {
			this.$emit("add-widget-event", widget);
			this.$store.commit("setWidgetCatalogOpen");
		},
	},
	mounted() {
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
	background-color: rgba(0, 0, 0, 0.5);
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
}

.fa {
	font-size: 1.5em;
}
</style>
