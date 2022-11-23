<template>
	<div class="container-fluid">
		<!-- project dashboard -->
		<div id="project-dashboard" class="row border-bottom dashboard">
			<h3 class="col-12">Projekt Dashboard</h3>
			<div class="col-12">
				<strong>Aktive Benutzer(innen):</strong>
				<br/>
				<span class="badge badge-light badge-rounded pa-1" v-for="user in users" :key="user.id">
					{{ user.fullName }}
				</span>
			</div>
			<div class="col-12 mt-4 board-area" v-if="padName">
				<authoringRatios/>
			</div>
		</div>
		<!-- custom dashboard -->
		<div id="custom-dashboard" class="row border-bottom dashboard">
			<h3 class="col-6">Dein Dashboard</h3>
			<div class="col-6">
				<button id="add-chart-btn" class="btn btn-primary rounded pull-right"
								@click.prevent="this.addChartContainer()">Diagramm
					hinzufügen
				</button>
			</div>
			<div class="col-12 board-area">
				<div v-for="widget in userCharts" :key="widget.id">
					<!-- tbd; container component with chart -->
					<component :is="widget.component" :configuration="widget.configuration"></component>
				</div>
			</div>
		</div>
		<!-- extend custom dashboard -->
		<div class="row extend">
			<button id="extend-custom-dashboard-btn" type="button" class="btn btn-primary btn-circle"
							@click.prevent="this.extendDashboard()">
				<i class="arrow down"></i>
			</button>
			<p>Dashboard erweitern</p>
		</div>
	</div>
</template>

<script lang="js">
import Communication from "../classes/communication";
import {defineAsyncComponent} from "vue";

export default {
	name: "dashboardView",
	data: () => ({
		projectCharts: [],
		userCharts: [],
	}),
	components: {
		// eslint-disable-next-line vue/no-unused-components
		barchart: defineAsyncComponent(() => import("../components/charts/barchart.vue")),
		authoringRatios: defineAsyncComponent(() => import("../components/charts/authoringRatios.vue")),
	},
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
	},
	watch: {},
	methods: {
		extendDashboard() {
			let customDashboard = document.getElementById("custom-dashboard");
			let height = parseInt(customDashboard.clientHeight);
			customDashboard.style.height = (height + 300) + "px";
		},
		addChartContainer() {
			console.log("tbd; add an instance of chart container component");
		},
		transformWidget(widgetData) {
			// TODO eventually parse the backend's widget configuration.
			return {
				id: widgetData.id,
				component: widgetData.component,
				configuration: {},
			};
		},
	},
	mounted() {
		const cmid = this.$store.getters.getCMID;
		Communication.webservice("getDashboards", {cmid}).then(result => {
			this.projectCharts = result.project.map(this.transformWidget);
			this.userCharts = result.user.map(this.transformWidget);
		});
		this.$store.dispatch("users/load");
	},
};
</script>

<style scoped lang="css">
h3 {
	align-self: flex-start;
	text-decoration: underline;
	margin-bottom: 2%;
}

.dashboard {
	height: 1000px;
	padding: 2% 0%;
}

#custom-dashboard {
	transition: height 1s;
}

.board-area {
	height: 100%;
	padding-bottom: 40px;
}

.btn-circle {
	width: 50px;
	height: 50px;
	border-radius: 50px;
}

.arrow {
	width: 1.5rem;
	height: 1.5rem;
	border: solid white;
	border-width: 0 5px 5px 0;
	display: inline-block;
}

.arrow.down {
	transform: rotate(45deg);
}

.extend {
	padding-top: 2%;
}

.extend p {
	align-self: center;
	margin: 0px 20px;
	padding: 0%;
}
</style>
