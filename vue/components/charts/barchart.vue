<template>
  <div>
    <h4>Beispielgrafik</h4>
    <div class="container-chart text-align-left">
      <div class="chart"></div>
    </div>    
    <a href="https://github.com/gywgithub/vue-d3-examples/blob/master/src/components/BarChartI.vue">Quelle des Beispieldiagramms</a>
    <div class="text-danger">Nutzen Sie am besten Componenten um Grafiken zu erstellen. (s. Quellcode)</div>
  </div>
</template>
<script>
import * as d3 from 'd3'
export default {
  data () {
    return {}
  },
  name: "barCharComponent",
  mounted () {
    let data = [4, 8, 15, 16, 23, 42]
    d3.select('.chart')
      .selectAll('div')
      .data(data)
      .enter().append('div')
      .style('width', function (d) { return d * 10 + 'px' })
      .text(function (d) { return d })
  }
}
</script>
<style scoped>
</style>

<style>
.chart div {
  font: 10px sans-serif;
  background-color: steelblue;
  text-align: right;
  padding: 3px;
  margin: 1px;
  color: white;
}
.container-chart {
  /* width: 50%; */
  margin: auto;
}
</style>