<template>
	<div class="chart-container">
		<h4>{{ i18n["widgets.cohesionDiagram.title"] }}</h4>
		<div class="content-container">
			<div :id="elementId" class="chart">
				<svg width="100%" height="100%" ref="chart" class="focus-author" :name="focusedAuthor"></svg>
			</div>
			<AuthorLegend :chart-id="elementId" :authors="authors" interactive
										:focused-author="focusedAuthor" @update:focused-author="focusedAuthor = $event"/>
		</div>
	</div>
</template>
<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";
import AuthorLegend from "../author-legend.vue";
import store from "../../store";

export default {
	name: "cohesionDiagram",
	components: {AuthorLegend},
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
		w: Number,
		h: Number,
	},
	computed: {
		elementId() {
			return "cohesionDiagram_" + this.id;
		},
		authors() {
			if (!this.diagramData.nodes) {
				return [];
			} else {
				return this.diagramData.nodes.map(n => ({
					epId: n.id,
					fullName: n.name ? n.name : store.getters["users/usersByEpId"][n.id].fullName,
					color: n.color ? n.color : store.getters["users/usersByEpId"][n.id].color,
				}));
			}
		},
		i18n() {
			return store.getters.getStrings;
		},
	},
	async mounted() {
		this.loadData().then(() => {
			this.buildDiagram();
			this.$emit("dashboardDimensions", this.getDashboardDimensions);
		});
	},
	methods: {
		async loadData() {
			if (this.isMock) {
				this.diagramData = this.mockData;
			} else {
				this.diagramData = await Communication.getFromEVA("getCohDiagData", {padName: this.padName});
				console.log("Calling Coh API");
			}
		},
		getDashboardDimensions() {
			return {w: 4, h: 17};
		},
		/**
		 * Creates an SVG arrow head in the given "defs" section to be referenced when drawing a path.
		 * @param defs {SVGDefsElement} The defs to create the arrow in.
		 * @param id {string} The string to set as "id".
		 * @param name {string} The string to set as "name".
		 * @param size {number} The size (= width = height) of the arrow head.
		 * @param color {string} The color of the arrow head.
		 */
		createArrowHead(defs, id, name, size, color) {
			defs.append("marker")
					.attr("id", id)
					.attr("markerUnits", "strokeWidth")
					.attr("viewBox", "0 0 6 6")
					.attr("markerWidth", size)
					.attr("markerHeight", size)
					.attr("refX", "4")
					.attr("refY", "3")
					.attr("orient", "auto")
					.attr("name", name)
					.append("path")
					.attr("d", "M0,0 6,3 L0,6 L0,0")
					.style("fill", color);
		},
		/**
		 * Creates the basic SVG stuff needed for drawing the diagram.
		 * @param data {Object} The data to draw from.
		 * @param colors {object} A map of author ids to their corresponding colors.
		 * @return {Selection} The SVG element.
		 */
		createSvgElement(data, colors) {
			// Get SVG element
			const svg = d3.select(`#${this.elementId} svg`);

			// Create arrow heads.
			const defs = svg.append("defs");
			Object.entries(colors).forEach(([id, color]) => {
				this.createArrowHead(defs, `arrow_${id}`, id, 3, color);
				this.createArrowHead(defs, `arrow_lg_${id}`, id, 6, color);
			});

			return svg;
		},
		/**
		 * (Re-)draws the diagram from the data.
		 * @return {Promise<void>}
		 */
		async buildDiagram() {
			document.querySelectorAll(`#${this.elementId} svg > *`).forEach(child => child.remove());
			const data = this.diagramData;

			const nodeRadius = 15;
			const nodeExtendedRadiusFactor = 1.3;

			// Wait for the empty svg to render to get the correct container size.
			this.$forceUpdate();
			await this.$nextTick();

			const width = this.$refs.chart.getBoundingClientRect().width;
			const height = this.$refs.chart.getBoundingClientRect().height;
			const nodeMaxDist = Math.min(width, height);

			// Generate a color map from all nodes.
			const colors = data.nodes.reduce((result, node) => ({
				...result,
				[node.id]: node.color ? node.color : store.getters["users/usersByEpId"][node.id].color,
			}), {});

			const svg = this.createSvgElement(data, colors);

			// Create nodes
			const node = svg
					.selectAll("circle")
					.data(data.nodes)
					.join("circle")
					.attr("r", nodeRadius)
					.attr("title", d => d.name)
					.attr("name", d => d.id)
					.style("fill", d => colors[d.id]);

			/**
			 * The following variables will contain the
			 * necessary data for drawing the connections
			 * between the nodes.
			 */
			const posX = {};
			const posY = {};
			const vectors = [];
			const curvedVectors = [];

			// Fit the distances to the maximum of 1 to make use of the entire svg canvas.
			// E.g. if the highest distance is 0.2, all distances will be multiplied by 5.
			const highestDistance = Math.max(...data.distances.map(d => d.dist));
			if (highestDistance < 1) {
				data.distances.forEach(d => {
					d.dist = d.dist / highestDistance;
				});
			}

			/**
			 * A D3 forceSimulation decides which positioning of the
			 * nodes best fits the given distances.
			 */
			const sim = d3.forceSimulation(data.nodes)
					.force("link", d3.forceLink(data.distances)
							.id(function (d) {
								return d.id;
							})
							.distance(d => d.dist * nodeMaxDist),
					)
					.force("center", d3.forceCenter(width / 2, height / 2));

			sim.on("tick", () => {
				// This helps accelerate the
				// process of the simulation
				for (let i = 0; i < 50; i++) {
					sim.tick();
				}
			});

			sim.on("end", endOfSim);

			/**
			 * Stores the final node positions and
			 * calls the functions that are responsible
			 * for drawing the connections.
			 */
			function endOfSim() {
				node
						.attr("cx", function (d) {
							// Make sure that the nodes stay well within the boundaries
							const x = Math.max(nodeRadius * 1.3, Math.min(width - nodeRadius * 1.3, d.x));
							posX[d.id] = x;
							return x;
						})
						.attr("cy", function (d) {
							// Make sure that the nodes stay well within the boundaries
							const y = Math.max(nodeRadius * 1.3, Math.min(height - nodeRadius * 1.3, d.y));
							posY[d.id] = y;
							return y;
						});
				createVectors();
				createCurvedVectors();
				drawCurvedVectors();
			}

			/**
			 * This function creates starting and end postions for
			 * vectors between each pair of nodes. For a better optical
			 * effect, these points will be placed a bit outside of the radius
			 * of the nodes. The output is stored in the "vectors" array.
			 */
			function createVectors() {
				data.connections
						.filter(con => con.intensity > 0)
						.forEach(con => {
							const startX = posX[con.source];
							const startY = posY[con.source];
							const endX = posX[con.target];
							const endY = posY[con.target];
							const vectorX = endX - startX;
							const vectorY = endY - startY;
							const length = Math.sqrt(vectorX * vectorX + vectorY * vectorY);
							const lambda = nodeExtendedRadiusFactor * nodeRadius / length;
							// The vector between each two nodes is shortened at the
							// beginning and at the end, in order to be positioned
							// clearly outside of the nodes.
							const adjustedStartX = startX + lambda * vectorX;
							const adjustedStartY = startY + lambda * vectorY;
							const adjustedEndX = endX - lambda * vectorX;
							const adjustedEndY = endY - lambda * vectorY;
							const vector = {
								x1: adjustedStartX,
								y1: adjustedStartY,
								x2: adjustedEndX,
								y2: adjustedEndY,
								intensity: con.intensity,
								source: con.source,
								target: con.target,
							};
							vectors.push(vector);
						});
			}

			/**
			 * Produces curved vectors from the content of the
			 * "vectors" array by testing, if the direct connection
			 * between any two points will cross or come too close
			 * to other nodes. If this is the case, then this function
			 * will try to overcome this by bending the connection as
			 * much as necessary.
			 */
			function createCurvedVectors() {
				vectors.forEach(v => {
					let curvedVector = bendLine(v, 30, 1, 0);
					let bended = false;
					let i = 0;
					while (testCollisions(curvedVector)) {
						// Attempt to find a path that doesn´t come
						// too close to other nodes
						const sign = i % 2 == 0 ? 1 : -1;
						const bend = Math.floor(i / 2 + 1) * 0.05;
						curvedVector = bendLine(v, 30, sign, bend);
						i++;
						bended = true;
						if (i > 2000) {
							// Give up and just show a straight line.
							curvedVector = bendLine(v, 1, sign, 0);
							break;
						}
					}

					if (bended === false) {
						// No bending happened. There could still be an overlap with a line that goes the other way around.
						const opposingVector = vectors.find(v2 => v.x1 === v2.x2 && v.y1 === v2.y2);
						if (opposingVector) {
							curvedVector = bendLine(v, 30, 1, 0.05);
						}
					}

					const curve = {...v, cVec: curvedVector, intensity: v.intensity};
					curvedVectors.push(curve);
				});
			}

			/**This function accepts a curved vector produced
			 * by the bendLine function and will return true, if
			 * the input vector does not keep enough distance
			 * to any of the positioned nodes.
			 *
			 * @param {*} curvedVector
			 */
			function testCollisions(curvedVector) {
				for (const n of data.nodes) {
					const nodeX = posX[n.id];
					const nodeY = posY[n.id];
					for (const point of curvedVector) {
						if ((point.x - nodeX) * (point.x - nodeX) + (point.y - nodeY) * (point.y - nodeY) <= (nodeRadius * nodeRadius) * 1.5) {
							return true;
						}
					}
				}
				return false;
			}

			/**
			 * Input is a line (defined by the coordinates of
			 * two points).
			 * The function returns an array of points. All
			 * these points are placed on a curve that starts
			 * respectively ends at the points given as input.
			 * If curveBend is set to 1, the curve will be a full
			 * sinus curve. Values between 0 and 1 will result in
			 * a toned-down sinus curve.
			 * The parts-parameter defines, with how many points curve
			 * will be defined in the returned array. A larger number
			 * of parts will help in achieving a better graphical resolution.
			 * The sign-parameter is either 1 or -1. This allows to have either
			 * a left-bent or right-bent curve.
			 */
			function bendLine(line, parts, sign = 1, curveBend = 0.05) {
				const out = [];
				const direction = getDirection(line);
				const normVector = getNormVector(direction);
				for (let i = 0; i <= parts; i++) {
					const fraction = i / parts;
					let x = line.x1 + fraction * direction.x;
					let y = line.y1 + fraction * direction.y;
					const deviation = Math.sin(Math.PI * fraction) * curveBend;
					x = x + sign * deviation * normVector.x;
					y = y + sign * deviation * normVector.y;
					out.push({x: x, y: y});
				}
				return out;
			}

			/**Transforms a line into a vector with the same
			 * direction but with the starting point transposed
			 * to (0,0).
			 *
			 * @param {*} line
			 */
			function getDirection(line) {
				return {x: line.x2 - line.x1, y: line.y2 - line.y1};
			}

			/**
			 * Returns a vector that is orthogonal
			 * to the input vector
			 * @param {*} vector
			 */
			function getNormVector(vector) {
				return {x: -vector.y, y: vector.x};
			}

			/**
			 * Draws all lines that are stored in the "curvedVectors" array.
			 */
			function drawCurvedVectors() {
				curvedVectors.sort((a, b) => b.intensity - a.intensity);
				curvedVectors.forEach(cV => {
					const points = cV.cVec.map(point => [point.x, point.y]);
					const curve = d3.line().curve(d3.curveNatural);

					// Drawing a slightly thicker white line below the actual one creates a border around the line to better distinguish the arrows from each other.
					svg.append("path")
							.attr("d", curve(points))
							.attr("fill", "none")
							.style("stroke", "white")
							.attr("stroke-width", Math.ceil(cV.intensity * 10) + 2)
							.attr("name", cV.source);

					svg.append("path")
							.attr("d", curve(points))
							.attr("fill", "none")
							.style("stroke", colors[cV.source])
							.attr("stroke-width", Math.ceil(cV.intensity * 10))
							.attr("marker-end", `url(#${cV.intensity < 0.2 ? "arrow_lg" : "arrow"}_${cV.source})`)
							.attr("name", cV.source);

				});
			}
		},
	},
	watch: {
		padName() {
			this.loadData().then(() => {
				this.buildDiagram();
			});
		},
		w() {
			this.buildDiagram();
		},
		h() {
			this.buildDiagram();
		},
	},
	data() {
		return {
			diagramData: {},
			focusedAuthor: null,
			mockData: {
				"nodes": [
					{
						"id": "a.1",
						"name": "Antonia",
						"color": "#00ffff",
					},
					{
						"id": "a.2",
						"name": "Berti",
						"color": "#008000",
					},
					{
						"id": "a.3",
						"name": "Carla",
						"color": "#0000ff",
					},
					{
						"id": "a.4",
						"name": "Dieter",
						"color": "#888800",
					},
					{
						"id": "a.5",
						"name": "Else",
						"color": "#ffc0cb",
					},
					{
						"id": "a.6",
						"name": "Fred",
						"color": "#808080",
					},
				],
				"distances": [
					{
						"source": "a.1",
						"target": "a.2",
						"dist": 0.2,
					},
					{
						"source": "a.1",
						"target": "a.3",
						"dist": 0.4,
					},
					{
						"source": "a.1",
						"target": "a.4",
						"dist": 0.5,
					},
					{
						"source": "a.1",
						"target": "a.5",
						"dist": 0.6,
					},
					{
						"source": "a.1",
						"target": "a.6",
						"dist": 0.5,
					},
					{
						"source": "a.2",
						"target": "a.3",
						"dist": 0.38,
					},
					{
						"source": "a.2",
						"target": "a.4",
						"dist": 0.4,
					},
					{
						"source": "a.2",
						"target": "a.5",
						"dist": 0.85,
					},
					{
						"source": "a.2",
						"target": "a.6",
						"dist": 0.4,
					},
					{
						"source": "a.3",
						"target": "a.4",
						"dist": 1.0,
					},
					{
						"source": "a.3",
						"target": "a.5",
						"dist": 0.5,
					},
					{
						"source": "a.3",
						"target": "a.6",
						"dist": 0.3,
					},
					{
						"source": "a.4",
						"target": "a.5",
						"dist": 0.4,
					}, {
						"source": "a.4",
						"target": "a.6",
						"dist": 0.3,
					},
					{
						"source": "a.5",
						"target": "a.6",
						"dist": 0.4,
					},
				],
				"connections": [
					{
						"source": "a.1",
						"target": "a.2",
						"intensity": 0.1,
					},
					{
						"source": "a.1",
						"target": "a.3",
						"intensity": 0.6,
					},
					{
						"source": "a.1",
						"target": "a.4",
						"intensity": 0.33,
					},
					{
						"source": "a.1",
						"target": "a.5",
						"intensity": 0.3,
					},
					{
						"source": "a.1",
						"target": "a.6",
						"intensity": 0.3,
					},
					{
						"source": "a.2",
						"target": "a.3",
						"intensity": 0.4,
					},
					{
						"source": "a.2",
						"target": "a.4",
						"intensity": 0.6,
					},
					{
						"source": "a.4",
						"target": "a.2",
						"intensity": 0.2,
					},
					{
						"source": "a.2",
						"target": "a.5",
						"intensity": 0.22,
					},
					{
						"source": "a.2",
						"target": "a.6",
						"intensity": 0.3,
					},
					{
						"source": "a.3",
						"target": "a.4",
						"intensity": 1.0,
					},
					{
						"source": "a.3",
						"target": "a.5",
						"intensity": 0.1,
					},
					{
						"source": "a.3",
						"target": "a.6",
						"intensity": 0.23,
					},
					{
						"source": "a.4",
						"target": "a.5",
						"intensity": 0.25,
					},
					{
						"source": "a.4",
						"target": "a.6",
						"intensity": 0.3,
					},
					{
						"source": "a.5",
						"target": "a.6",
						"intensity": 0.4,
					},
				],
			},
		};
	},
};
</script>
<style scoped lang="css">
.chart-container {
	display: flex;
	flex-direction: column;
}

.chart {
	width: 100%;
	height: 100%;
	display: block;
	margin: auto;
}

.content-container {
	display: flex;
	height: 100%;
}
</style>