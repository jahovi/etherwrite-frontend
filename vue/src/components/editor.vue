<!-- eslint-disable indent -->
<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
  <div>
    <KPIs :padName="padName"/>
    <div v-if="!loading">
      <div class="text-right mt-2 mb-2">
        <a href="#" v-on:click="reloadFrame"
        ><i class="icon fa fa-refresh fa-fw" title="refresh" role="img"></i>
          Editor neu laden</a
        >
      </div>
    </div>
    <a href="#" @click="showMinimap = !showMinimap">
      <i class="fa fa-angle-double-left mr-1" v-if="showMinimap"></i>
      <i class="fa fa-angle-double-right mr-1" v-else></i>
      Minimap
    </a>
    <div class="holder" v-if="!loading">
      <div class="minimap-wrapper" :class="{'hidden-minimap': !showMinimap}">
        <Minimap :padName="padName"/>
      </div>
      <iframe
          id="writerview"
          v-bind:src="link"
          title="Texteditor"
      ></iframe>
    </div>
  </div>
</template>
<script>
import Minimap from "./minimap.vue";
import Communication from "../classes/communication";
import KPIs from "../components/kpis.vue";

export default {
	data: () => ({
		loading: true,
		showMinimap: true,
	}),
	components: {
		Minimap,
		KPIs,
	},
	name: "EditorComponent",
	computed: {
		editorInstance() {
			const padName = this.$route.params.padName;
			return this.$store.state.base.editorInstances.find(e => e.padName === padName);
		},
		getStrings() {
			return this.$store.getters.getStrings;
		},
		link() {
			return this.editorInstance.link;
		},
		padName() {
			return this.editorInstance.padName;
		},
	},
	mounted() {
		Communication.getFromEVA("etherpad/authorize").then(response => {
			const expiry = new Date();
			expiry.setDate(new Date().getDate() + 1);
			document.cookie = `token=${response.token}; expires=${expiry}; path=/`;
			this.loading = false;
		});
		this.checkScreenSize();
		window.addEventListener("resize", () => this.checkScreenSize());
	},
	methods: {
		reloadFrame: function () {
			const element = document.getElementById("writerview");
			element.src += "";
		},
		checkScreenSize() {
			if (window.innerWidth < 768) {
				this.showMinimap = false;
			}
		},
	},
};
</script>

<style scoped>
iframe {
  border-style: none;
  flex-grow: 1;
}

.minimap-wrapper {
  width: 10%;
  max-width: 70px;
  padding-top: 41px;
  transition: width 0.3s ease-out;
}

.minimap-wrapper.hidden-minimap {
  width: 0;
}

.holder {
  width: 100%;
  height: 700px;
  display: flex;
  gap: 12px;
}
</style>

