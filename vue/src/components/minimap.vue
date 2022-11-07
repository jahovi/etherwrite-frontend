<!-- eslint-disable vue/multi-word-component-names -->
<!-- @author Marie Freise -->
<!-- @copyright Marie Freise, 2022, marie_freise@web.de -->

<template>
  <div class="minimap" v-dragscroll.y="true">
    <span style="white-space: pre-line">{{ ethContent }}</span>
  </div>
</template>
    
<script>
import { dragscroll } from "vue-dragscroll";

export default {
	data: function() {
		return {
			ethContent: "",
			keepRefreshing: true,
			response: "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat," + "\n" + "sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet," + "\n" + "consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.",
		};
	},
	directives: {
		dragscroll,
	},
	methods: {
		refreshMinimap: async function() {
			function sleep(ms) {
				return new Promise(resolve => setTimeout(resolve, ms));
			}
			while(this.keepRefreshing) {

				/// Dieser Bereich muss einkommentiert und mit der richtigen API versehen werden, sobald unser Webservice (EVA) steht.
				/* 
        		let response = await fetch("https://official-joke-api.appspot.com/random_joke");
				const data = await response.json();
				this.ethContent += data.setup + " ";
        		*/
				this.ethContent += this.response; //Dieser Codeteil muss dann entfernt werden.

				await sleep(1000);
			}
		},
	},
	mounted() {
		this.refreshMinimap();
	},
};
</script>
    
<style scoped>
.minimap {
  background-color: rgba(0, 0, 0, 0.05);
  position: absolute;
  float: left;
  width: 15%;
  height: 620px;
  top: 40px;
  bottom: 40px;
  font-size: 3px;
  padding: 10px;
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