<template>
    <div>
        <h3>Lehrenden-Dashboard</h3>
        <BarChart />
    </div>
</template>
<script>
    import BarChart from '../components/charts/barchart.vue';
    export default {
        data: function(){
            return {}
        },
        name: 'teacherDashboardView',
        components: {
            BarChart
        }
    }
</script>
<style scoped>
</style>