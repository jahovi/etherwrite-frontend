<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
	<div>
		<transition name="fade">
			<div class="alert mt-4" v-bind:class="getAlertType" v-if="getAlertShow" role="alert">
				{{ getAlertMessage }}
			</div>
		</transition>
		<ul class="nav nav-tabs nav-vue">
			<li class="nav-item">
				<router-link to="/" class="nav-link">Aufgabenstellung</router-link>
			</li>
			<template v-for="editor in getEditorInstances" :key="editor.padName">
				<li class="nav-item">
					<router-link :to="`/editor/${editor.padName}`" class="nav-link" :text="`Texteditor (${editor.groupName})`"/>
				</li>
				<li class="nav-item">
					<router-link :to="`/dashboard/${editor.padName}`"  class="nav-link" :text="`Dashboard (${editor.groupName})`"/>
				</li>
			</template>
		</ul>
		<div class="tab-content main-vue">

			<div class="mx-3 mt-4 mb-3">
				<router-view v-if="baseInfoLoaded"/>
				<div class="spinner-grow text-primary mt-4" role="status" v-else>
					<span class="sr-only">Loading...</span>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import store from './src/store';


export default {
	data: function () {
		return {
			baseInfoLoaded: false,
		};
	},
	name: "app",
	async mounted() {
		await this.$store.dispatch("loadEditorBaseInfo");
		this.baseInfoLoaded = true;
	},
	computed: {
		getAlertShow: function () {
			return this.$store.getters.getAlertShow;
		},
		getAlertType: function () {
			return this.$store.getters.getAlertType;
		},
		getAlertMessage: function () {
			return this.$store.getters.getAlertMessage;
		},
		getEditorInstances: function () {
			return this.$store.state.base.editorInstances;
		}	
	},
};
</script>
<style scoped>
</style>
