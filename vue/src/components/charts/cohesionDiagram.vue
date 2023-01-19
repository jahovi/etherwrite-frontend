<template>
  <div>
    <h4>Kohäsion</h4>
    <div class="chart-container">
      <div :id="elementId" class="chart"></div>
    </div>
  </div>
</template>
<script>
import * as d3 from "d3";
import Communication from "../../classes/communication";
import store from "../../store";

export default {
    name: "cohesionDiagram",
    props: {
        id: String,
        isMock: Boolean,
        padName: String,
    },
    computed: {
        elementId() {
            return "cohesionDiagram_" + this.id;
        },
        // padName() {
		// 	return this.$store.state.base.padName;
		// },
    },
    async mounted() {
        if(this.isMock){
            this.diagramData = this.mockData;
        } else {
            this.diagramData = await Communication.getFromEVA("getCohDiagData", {padName: this.padName});
        }

        this.buildDiagram();
        this.$emit("dashboardDimensions", this.getDashboardDimensions);

    },
    methods: {
        getDashboardDimensions() {
			return {w: 7, h: 17};
		},
        buildDiagram() {
            const data = this.diagramData;

            const nodeRadius = 25;
            const nodeExtendedRadiusFactor = 1.3;

            const width = 700;
            const height = 550;
            const nodeMaxDist = Math.min(width, height);

            // Create SVG element
            const svg = d3.select(`#${this.elementId}`)
                .append("svg")
                .attr("width", width)
                .attr("height", height);

                // Adding background for better contrast
                svg.append("rect")
                .attr("width", "100%")
                .attr("height", "100%")
                .attr("fill", "#c2f0dc")
                .attr("opacity", 0.1);

            // Create nodes
            const node = svg
                .selectAll("circle")
                .data(data.nodes)
                .join("circle")
                .attr("r", nodeRadius)
                .style("fill", d => d.color);

            /**
             * The following variables will contain the
             * necessary data for drawing the connections
             * between the nodes. 
             */
            const posX = {};
            const posY = {};
            const vectors = [];
            const curvedVectors = [];

            /**
             * A D3 forceSimulation decides which positioning of the
             * nodes best fits the given distances. 
             */
            const sim = d3.forceSimulation(data.nodes)
                .force("link", d3.forceLink(data.distances)
                    .id(function (d) { return d.id; })
                    .distance(d => d.dist * nodeMaxDist)
                )
                .force("center", d3.forceCenter(width / 2, height / 2));

                sim.on("tick", ()=>{
                    // This helps accelerate the
                    // process of the simulation
                for(let i = 0 ; i < 50;i++){
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
                        const x = Math.max( nodeRadius * 1.3, Math.min( width - nodeRadius * 1.3 , d.x ));
                        posX[d.id] = x;
                        return x;
                    })
                    .attr("cy", function (d) {
                        // Make sure that the nodes stay well within the boundaries
                        const y = Math.max( nodeRadius * 1.3, Math.min( height - nodeRadius * 1.8 , d.y ));
                        posY[d.id] = y;
                        return y;
                    });
                createVectors();
                createCurvedVectors();
                drawCurvedVectors();
                createLabels();
            }

            /**
             * Places labels with the first names of the
             * authors below the nodes. 
             */
            function createLabels() {
                const fontSize = "16px";
                svg.selectAll("text")
                    .data(data.nodes)
                    .enter()
                    .append("text")
                    .attr("x", d => posX[d.id])
                    // Place labels slightly below the nodes
                    .attr("y", d => posY[d.id] + (nodeRadius * 1.6))
                    .style("fill", "black")
                    .style("background-color","white")
                    .style("text-anchor", "middle")
                    .style("font-weight", "bold")
                    .style("font-family", "Arial")
                    .style("font-size", fontSize)
                    // Displaying first names only
                    .text(d => d.name.split(" ")[0]);
            }


            /**
             * This function creates starting and end postions for 
             * vectors between each pair of nodes. For a better optical
             * effect, these points will be placed a bit outside of the radius
             * of the nodes. The output is stored in the "vectors" array. 
             */
            function createVectors() {
                data.connections.forEach(con => {
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
                        intensity: con.intensity
                    }
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
                    let i = 0;
                    while (testCollisions(curvedVector)) {
                        // Attempt to find a path that doesn´t come
                        // too close to other nodes
                        const sign = i % 2 == 0 ? 1 : -1;
                        const bend = Math.floor(i / 2 + 1) * 0.05;
                        curvedVector = bendLine(v, 30, sign, bend);
                        i++;
                    };
                    const curve = { cVec: curvedVector, intensity: v.intensity };
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
                let collision = false;
                data.nodes.forEach(n => {
                    const nodeX = posX[n.id];
                    const nodeY = posY[n.id];
                    curvedVector.forEach(point => {
                        collision = collision ||
                            ((point.x - nodeX) * (point.x - nodeX) + (point.y - nodeY) * (point.y - nodeY) <= (nodeRadius * nodeRadius) * 1.5);
                    })
                });
                return collision;
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
                    out.push({ x: x, y: y });
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
                return { x: line.x2 - line.x1, y: line.y2 - line.y1 };
            }

            /**
             * Returns a vector that is orthogonal 
             * to the input vector
             * @param {*} vector 
             */
            function getNormVector(vector) {
                return { x: -vector.y, y: vector.x };
            }

            /**
             * Draws all lines that are stored in the
             * "curvedVectors" array. 
             */
            function drawCurvedVectors() {
                curvedVectors.forEach(cV => {
                    for (let i = 0; i < cV.cVec.length - 1; i++) {
                        svg.append("line")
                            .attr("x1", cV.cVec[i].x)
                            .attr("y1", cV.cVec[i].y)
                            .attr("x2", cV.cVec[i + 1].x)
                            .attr("y2", cV.cVec[i + 1].y)
                            .style("stroke", "red")
                            .style("stroke-opacity","0.60")
                            .attr("stroke-width", Math.ceil(cV.intensity * 10));
                    }
                });
            }


        }
    },
    data() {
        return {
            diagramData: {},
            mockData: {
                "nodes": [
                    {
                        "id": "a.1",
                        "name": "Antonia",
                        "color": "#00ffff"
                    },
                    {
                        "id": "a.2",
                        "name": "Berti",
                        "color": "#008000"
                    },
                    {
                        "id": "a.3",
                        "name": "Carla",
                        "color": "#0000ff"
                    },
                    {
                        "id": "a.4",
                        "name": "Dieter",
                        "color": "#ffff00"
                    },
                    {
                        "id": "a.5",
                        "name": "Else",
                        "color": "#ffc0cb"
                    },
                    {
                        "id": "a.6",
                        "name": "Fred",
                        "color": "#808080"
                    }
                ],
                "distances": [
                    {
                        "source": "a.1",
                        "target": "a.2",
                        "dist": 0.2
                    },
                    {
                        "source": "a.1",
                        "target": "a.3",
                        "dist": 0.4
                    },
                    {
                        "source": "a.1",
                        "target": "a.4",
                        "dist": 0.5
                    },
                    {
                        "source": "a.1",
                        "target": "a.5",
                        "dist": 0.6
                    },
                    {
                        "source": "a.1",
                        "target": "a.6",
                        "dist": 0.5
                    },
                    {
                        "source": "a.2",
                        "target": "a.3",
                        "dist": 0.38
                    },
                    {
                        "source": "a.2",
                        "target": "a.4",
                        "dist": 0.4
                    },
                    {
                        "source": "a.2",
                        "target": "a.5",
                        "dist": 0.85
                    },
                    {
                        "source": "a.2",
                        "target": "a.6",
                        "dist": 0.4
                    },
                    {
                        "source": "a.3",
                        "target": "a.4",
                        "dist": 1.0
                    },
                    {
                        "source": "a.3",
                        "target": "a.5",
                        "dist": 0.5
                    },
                    {
                        "source": "a.3",
                        "target": "a.6",
                        "dist": 0.3
                    },
                    {
                        "source": "a.4",
                        "target": "a.5",
                        "dist": 0.4
                    }, {
                        "source": "a.4",
                        "target": "a.6",
                        "dist": 0.3
                    },
                    {
                        "source": "a.5",
                        "target": "a.6",
                        "dist": 0.4
                    }
                ],
                "connections": [
                    {
                        "source": "a.1",
                        "target": "a.2",
                        "intensity": 0.1
                    },
                    {
                        "source": "a.1",
                        "target": "a.3",
                        "intensity": 0.6
                    },
                    {
                        "source": "a.1",
                        "target": "a.4",
                        "intensity": 0.33
                    },
                    {
                        "source": "a.1",
                        "target": "a.5",
                        "intensity": 0.3
                    },
                    {
                        "source": "a.1",
                        "target": "a.6",
                        "intensity": 0.3
                    },
                    {
                        "source": "a.2",
                        "target": "a.3",
                        "intensity": 0.4
                    },
                    {
                        "source": "a.2",
                        "target": "a.4",
                        "intensity": 0.6
                    },
                    {
                        "source": "a.2",
                        "target": "a.5",
                        "intensity": 0.22
                    },
                    {
                        "source": "a.2",
                        "target": "a.6",
                        "intensity": 0.3
                    },
                    {
                        "source": "a.3",
                        "target": "a.4",
                        "intensity": 1.0
                    },
                    {
                        "source": "a.3",
                        "target": "a.5",
                        "intensity": 0.1
                    },
                    {
                        "source": "a.3",
                        "target": "a.6",
                        "intensity": 0.23
                    },
                    {
                        "source": "a.4",
                        "target": "a.5",
                        "intensity": 0.25
                    },
                    {
                        "source": "a.4",
                        "target": "a.6",
                        "intensity": 0.3
                    },
                    {
                        "source": "a.5",
                        "target": "a.6",
                        "intensity": 0.4
                    }
                ]
            }
        }
    },

}
</script>
<style scoped lang="css">
.chart-container {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: flex-start;
}

.chart {
  width: 100%;
  height: 100%;
  display: block;
  margin: auto;
}
</style>