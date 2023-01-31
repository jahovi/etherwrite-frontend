<template>
    <div class="container-fluid">
        <div class="row">
            <Countdown class="col-6 " />
            <DocumentMetrics :padName="padName" class="col-6 border-left" />
            <GroupParticipants :padName="padName" class="col-12 border-top border-bottom" />
        </div>
    </div>
</template>

<script lang="js">
import Countdown from "./kpis/countdown.vue";
import DocumentMetrics from "./kpis/documentMetrics.vue";
import GroupParticipants from "./kpis/groupParticipants.vue";

export default {
    name: "kpis",
    components: {
        Countdown,
        DocumentMetrics,
        GroupParticipants,
    },
    props: {
        padName: String,
    },
    data: () => {
        return {}
    },
    computed: {},
    watch: {},
    methods: {},
    mounted: function () { },
}
</script>

<style scoped lang="css">

</style>
