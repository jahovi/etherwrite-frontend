<template>
    <div class="border rounded countdown" :class="{ danger: inDanger }">
        {{ getStrings["kpi-countdown-text"] }} {{ timeLeft }}
    </div>
</template>

<script lang="js">
import Communication from "../../classes/communication.js";

export default {
    name: "countdown_kpi",
    components: {},
    data: () => {
        return {
            deadline: 0,
            currentTime: 0,
            inDanger: false,
        }
    },
    computed: {
        timeLeft() {
            if (this.deadline !== 0) {
                let milliseconds = this.deadline - this.currentTime;
                // <1d left -> danger
                this.inDanger = milliseconds <= 86400000 ? true : false;
                // calc 
                let days = Math.floor(milliseconds / 86400000);
                milliseconds -= days * 86400000;
                let hours = Math.floor(milliseconds / 3600000);
                milliseconds -= hours * 3600000;
                let minutes = Math.floor(milliseconds / 60000);
                milliseconds -= minutes * 60000;
                let seconds = Math.floor(milliseconds / 1000);
                // format
                seconds = String(seconds).padStart(2, "0");
                minutes = String(minutes).padStart(2, "0");
                hours = String(hours).padStart(2, "0");

                return `${days}:${hours}:${minutes}:${seconds}`;
            }
            return this.getStrings["kpi-countdown-no-deadline"];
        },
        getStrings() {
            return this.$store.getters.getStrings;
        },
    },
    watch: {},
    methods: {},
    mounted: async function () {
        const cmid = this.$store.getters.getCMID;
        let task = await Communication.webservice("getTaskDescription", { cmid });
        this.deadline = task.deadline * 1000;
        window.setInterval(() => {
            this.currentTime = Date.now();
        }, 1000)
    },
}
</script>
 
<style scopred lang="css">
.countdown {
    padding: 0px 10px 0px 10px;
}

.danger {
    color: red;
}
</style>
