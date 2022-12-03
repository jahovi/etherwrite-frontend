<template>
	<div id="widget-catalog">
		<div class="widget-catalog-backdrop" @click.prevent="closeWidgetCatalog" v-if="isWidgetCatalogOpen"></div>
		<div class="widget-catalog-panel border rounded" v-if="isWidgetCatalogOpen">
			<div class="categories">
				<b-tabs content-class="mt-3">
					<b-tab v-for="(category, key) in categories" :title="category" :key="key">
						<div class="widgets">
							<ChartWrapper class="wrapper border rounded" v-for="(widget, key) in widgets" :mockOnly=true
														:component="widget.component" :id="widget.id" :key="key">
								<template #btn>
									<button :id="'add-widget-btn-' + widget.id" class="btn btn-success btn-add-widget"
													@click.prevent="addToDashboard(widget)">
										<i class="fa fa-plus"></i>
									</button>
								</template>
							</ChartWrapper>
						</div>
					</b-tab>
				</b-tabs>
			</div>
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
	data: () => ({
		categories: [],
		widgets: [],
	}),
	computed: {
		isWidgetCatalogOpen() {
			return this.$store.getters.isWidgetCatalogOpen;
		},
	},
	watch: {},
	methods: {
		closeWidgetCatalog() {
			this.$store.commit("setWidgetCatalogOpen");
		},
		addToDashboard(widget) {
			this.$emit("add-widget-event", {
				...widget,
				w: widget.minW,
				h: widget.minH,
			});
			this.$store.commit("setWidgetCatalogOpen");
		},
	},
	mounted() {
		// TODO: mock data, load data from widgets store (Jan #26)
		this.widgets = [
			{
				component: "barchart",
				category: "barchart",
				configuration: {},
				minW: 3,
				minH: 1,
			},
			{
				component: "authoringRatios",
				category: "piechart",
				configuration: {},
				minW: 3,
				minH: 1,
			},
		];
		// create tabs from categories
		this.widgets.forEach(widget => {
			if (!this.categories.includes(widget.category)) {
				let category = widget.category.charAt(0).toUpperCase() + widget.category.slice(1);
				this.categories.push(category);
			}
		});
	},
};
</script>

<style scoped lang="css">
.widget-catalog-backdrop {
	background-color: rgba(0, 0, 0, 0.5);
	width: 100%;
	height: 100%;
	position: fixed;
	z-index: 998;
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
	filter: grayscale(50%);
	margin-bottom: 5px;
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
