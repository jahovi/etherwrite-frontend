<!-- eslint-disable vue/multi-word-component-names -->
<!-- @author Marie Freise -->
<!-- @copyright Marie Freise, 2022, marie_freise@web.de -->

<template>
  <div class="minimap" v-dragscroll.y="true">
    <span
      class="textBlock"
      :style="{ background: block.color }"
      v-for="block in coloredBlocks"
      :key="block.id"
      >{{ block.content }}</span
    >
  </div>
</template>
    
<script>
import { dragscroll } from "vue-dragscroll";

const ipAddress = "http://192.168.178.26:8083"; //Enter ip address of your local computer.

export default {
	data: function() {
		return {
			keepRefreshing: true,
			coloredBlocks: []
		};
	},
	directives: {
		dragscroll,
	},
	props: {
		padName: {
			required: true,
		},
	},
	methods: {
		refreshMinimap: async function() {
			function sleep(ms) {
				return new Promise(resolve => setTimeout(resolve, ms));
			}
			while(this.keepRefreshing) {

				//Cross-origin requests need to be safe
				const response = await fetch(ipAddress + "/getBlockInfo?padName="+this.padName, {
					method: "GET",
					headers: {
						"Content-Type": "application/json",
						"Accept": "application/json",
					},
				});
				const data = await response.json();
				this.processMinimapContent(data);

				await sleep(100);
			}
		},
		processMinimapContent: async function(data) {

			const response = await fetch(ipAddress + "/getAuthorInfo", {
				method: "GET",
				headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
				},
			});
			const authorData = await response.json();

			this.coloredBlocks = [];

			data.forEach ((block) => {
				let rndContent = "";
				const blockColor = authorData[block.author]["color"]; //Getting the author's color for the current block.
				for (var i=0; i<block.blockLength; i++) { 
					if (block.lineBreakIndices.includes(i)) {
						rndContent += "\n";
					} 
					else { 
						rndContent += "c";
					}
    		}
			this.coloredBlocks.push({id: this.coloredBlocks.length, color: blockColor, content: rndContent});
			});
			
		},
	},
	mounted() {
		this.refreshMinimap();
	},
};
</script>
    
<style scoped>
.textBlock {
  white-space: pre-line;
}

.minimap {
  background-color: rgba(0, 0, 0, 0.05);
  position: absolute;
  float: left;
  width: 10%;
  height: 620px;
  top: 40px;
  bottom: 40px;
  font-size: 3px;
  padding: 10px;
  overflow-wrap: break-word;
  overflow: auto;
  -ms-overflow-style: none; /* Hide Scrollbar Internet Explorer 10+ */
  scrollbar-width: none; /* Hide Scrollbar Firefox */
  cursor: grab;
  user-select: none;
}
.minimap::-webkit-scrollbar {
  display: none; /* Hide Scrollbar Safari and Chrome */
}
</style>