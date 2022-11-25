<template>
  <h4>Schreibanteil pro Person</h4>
  <div class="chart-container">
    <div class="chart"></div>
    <ul class="legend mt-3" v-if="colorFn">
      <li v-for="author in pad.authors" :key="author">
        <i class="fa fa-square" :style="{ color: colorFn(author) }"></i> {{ author }}
      </li>
    </ul>
  </div>
</template>

<script>
import * as d3 from "d3";
import Communication from '../../classes/communication';

export default {
  data() {
    return {
      pad: {
        authors: [],
        ratios: [],
        colors: [],
      },
      colorFn: null,
    };
  },
  props: {
    isMock: false
  },
  name: "barCharComponent",
  mounted() {
    if (this.isMock) {
      this.pad = {
        authors: ["Mueller", "Fuchs", "Gamma"],
        ratios: [15, 35, 50],
        colors: ["#00B2EE", "#FF7F24", "#008B45"],
      };
      this.loadBar();
    } else {
      this.getData().then(() =>
        this.loadBar());
    }
  },
  methods: {
    async getData() {
      return Communication.getFromEVA("authoring_ratios", { pad: this.$store.state.base.padName })
        .then(data => {
          this.pad = {
            authors: data.authors,
            ratios: data.ratios,
            colors: data.colors,
          };
        })
        .catch(() => {
          this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
        });
    },
    loadBar() {
      const keys = this.pad.authors;
      const data = this.pad.ratios;
      const colors = this.pad.colors;
      // Add a filler if the given numbers don't add up to 100%.
      // Can happen with weird test data.
      const sum = data.reduce((result, entry) => result + entry);
      if (sum < 1) {
        keys.push("Unbekannt");
        data.push(1 - sum);
        colors.push("#ccc");
      }

      this.colorFn = d3.scaleOrdinal(this.pad.colors);

      d3.select(".chart")
        .selectAll("div")
        .data(data)
        .enter().append("div")
        .style("width", function (d) { return d * 10 + "px" })
        .style("background-color", (d, i) => this.colorFn(i))
        .text(function (d) { return d });
    },
  },
}
</script>
<style scoped lang="css">
.chart-container {
  display: flex; 
	flex-direction: row-reverse;
  justify-content: flex-end;
  align-items: center;
  gap: 20px;
}

.chart>>>div {
  font: 10px sans-serif;
  text-align: right;
  padding: 3px;
  margin: 1px;
  color: white;
}
</style>