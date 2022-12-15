<template>
    <div>
        <h4>test</h4>
        <div class="chart-container">
            <canvas width="200" height="200" :id="elementId"></canvas>
        </div>
    </div>
</template>

<script>
import Chart from "../../../node_modules/chart.js/dist/Chart.bundle.min.js";
import Communication from "../../classes/communication";
import store from "../../store";


export default {
    name: "participation_diagram",
    props: {
        id: String,
        isMock: Boolean,
    },
    data() {
        return {
            datapoints: [],
            timestamps: [],
            authorActivityLists: {},
            datasets: [],
        };
    },
    computed: {
        elementId() {
            return `participation_diagram_${this.id}`;
        },
    },
    async mounted() {
        this.getData().then(() => this.loadChart());
    },
    methods: {
        async getData() {
            const data = await Communication.getFromEVA("activity/activities", { padName: store.state.base.padName });

            // construct author set
            const authorSet = new Set();
            for (const elem of data) {
                for (const author in elem.authorToActivities) {
                    authorSet.add(author);
                }
            }

            // prepare an activity list for each author
            for (const item of authorSet) {
                this.authorActivityLists[item] = [];
            }

            function random_rgba() {
                var o = Math.round, r = Math.random, s = 255;
                return "rgba(" + o(r() * s) + "," + o(r() * s) + "," + o(r() * s) + "," + 1 + ")";
            }

            // append all author activity to the appropriate activity list
            for (const elem of data) {
                this.timestamps.push(elem.timestamp);
                for (const author in this.authorActivityLists) {
                    if (Object.hasOwn(elem.authorToActivities, author.toString())) {
                        // author had activity for this timestamp
                        this.authorActivityLists[author].push(elem.authorToActivities[author]);
                    } else {
                        // author had no activity for this timestamp
                        this.authorActivityLists[author].push(0);
                    }
                }
            }

            // try to condense in to lesser number of loops
            // for (const elem of data) {
            //     this.timestamps.push(elem.timestamp);
            //     for (const author of authorSet) {
            //         const dataset = {
            //             label: author,
            //             backgroundColor: random_rgba(),
            //             data: [],
            //         };

            //         if (Object.hasOwn(elem.authorToActivities, author.toString())) {
            //             // author had activity for this timestamp
            //             dataset.data.push(elem.authorToActivities[author]);
            //         } else {
            //             // author had no activity for this timestamp
            //             dataset.data.push(0);
            //         }
            //         this.datasets.push(dataset);
            //     }
            // }



            // repack authorActivityLists into chart specific dataset format
            for (const author in this.authorActivityLists) {
                this.datasets.push({
                    label: author, // TODO: replace with username
                    data: this.authorActivityLists[author],
                    backgroundColor: random_rgba(), // TODO: replace with author color
                });
            }

            console.log(this.timestamps);
            console.log(this.authorActivityLists);
            console.log(this.datasets);
        },

        loadChart() {
            const ctx = document.getElementById(`${this.elementId}`);
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: this.timestamps,
                    datasets: this.datasets,
                },
                options: {
                    scales: {
                        xAxes: [{
                            stacked: true,
                        }],
                        yAxes: [{
                            stacked: true,
                        }],
                    },
                },
            });
        },
    },
};

</script>