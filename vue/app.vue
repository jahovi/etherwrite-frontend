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
			<li class="nav-item" v-for="editor in getEditorInstances" :key="`${editor.padName}_editor`">
				<strong v-if="getEditorInstances.length > 1">{{ editor.groupName }}</strong>
				<div>
					<router-link :to="`/editor/${editor.padName}`" class="nav-link" :text="`Texteditor`"/>
					<router-link :to="`/dashboard/${editor.padName}`" class="nav-link" :text="`Dashboard`"/>
				</div>
			</li>
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


export default {
	data: function () {
		return {
			baseInfoLoaded: false,
			authorWebsocket: null,
		};
	},
	name: "app",
	async mounted() {
		await this.$store.dispatch("loadEditorBaseInfo");
		await this.$store.dispatch("users/load");
		// this.authorWebsocket = await this.$store.dispatch("users/initAuthorsWebsocket");
		this.baseInfoLoaded = true;
	},
	unmounted(){
		this.authorWebsocket.close();
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
		},
	},
};
</script>
<style scoped>
.nav-item {
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
}

.nav-item div {
	display: flex;
	flex-wrap: nowrap;
}

.nav-item strong {
	text-align: center;
	padding: 4px 0;
	margin: 0 8px -1px;
	border-bottom: 1px solid #eee;
}

.router-link-active {
	background-color: #0F6CBF;
	color: white;
}
</style>
