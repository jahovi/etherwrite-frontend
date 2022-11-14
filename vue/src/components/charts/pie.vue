<template>
  <div>
    <h4>Kuchendiagramm</h4>
    <div class="container-chart text-align-left">
      <div class="chart"></div>
    </div>
    <svg width="300" height="200"></svg>
    <div>
      <svg id="legend" height=300 width=450></svg>
    </div>
  </div>
</template>

<script>
import * as d3 from "d3";


export default {
  data() {
    return {
      pad: {
        authors: [],
        ratios: [],
        colors: []
      }
    }
  },
  props: {
		padName: {
			required: true,
			type: String,
		},
	},
  name: "pieChartComponent",
  mounted() {
      this.getData().then(()=>
      this.loadPie());
  },
  methods: {
   async getData() {
      return fetch('http://localhost:8083/authoring_ratios?pad=' + this.padName)
        .then(response => response.json())
        .then(data => {
          this.pad.authors = data.authors;
          this.pad.ratios = data.ratios;
          this.pad.colors = data.colors;
        })
        .catch(error => { console.log("ERROR FROM THE BACKEND", error); reject(error); });
    },

    loadPie() {
      console.log(this.pad);
      var keys = this.pad.authors;
      var data = this.pad.ratios;
      var color = d3.scaleOrdinal(this.pad.colors);


      var svg = d3.select("svg"),
        width = svg.attr('width'),
        height = svg.attr('height'),
        radius = Math.min(width, height) / 2

      var chart = svg.append('g')
        .attr('transform', 'translate(' + width / 2 + ',' + height / 2 + ')');

      var pie = d3.pie();
      var arc = d3.arc()
        .innerRadius(0)
        .outerRadius(radius);
        

      var arcs = chart.selectAll('arc')
        .data(pie(data))
        .enter().append('g')
        .attr('class', 'arc')

      arcs.append('path')
        .attr('fill', function (d, i) {
          return color(i)
        })
        .attr('d', arc)
        .append("title")
        .text(function (d) { return d.value*100; });
      
      var legend = d3.select("#legend")

      // Add one sqare in the legend for each name.
      var size = 20
      legend.selectAll("mydots")
        .data(keys)
        .enter()
        .append("rect")
        .attr("x", 100)
        .attr("y", function (d, i) { return 25 + i * (size + 5) }) // 100 is where the first dot appears. 25 is the distance between dots
        .attr("width", size)
        .attr("height", size)
        .style("fill", function (d) { return color(d) })

      // Add one label in the legend for each name.
      legend.selectAll("mylabels")
        .data(keys)
        .enter()
        .append("text")
        .attr("x", 100 + size * 1.2)
        .attr("y", function (d, i) { return 25 + i * (size + 5) + (size / 2) }) // 100 is where the first dot appears. 25 is the distance between dots
        .style("fill", function (d) { return color(d) })
        .text(function (d) { return d})
        .attr("text-anchor", "left")
        .style("alignment-baseline", "middle")
    }
  }
}
</script>
