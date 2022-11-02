<template>
    <div>
        <h3>Lernenden-Dashboard</h3>
        <BarChart />
    </div>
</template>
<script>
import BarChart from "../components/charts/barchart.vue";
export default {
	data: function(){
		return {};
	},
	name: "learnerDashboardView",
	components: {
		BarChart,
	},
};
</script>
<style scoped>
</style>