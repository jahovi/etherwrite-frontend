<!-- eslint-disable indent -->
<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
	<div>
		<div
				class="d-flex flex-column align-items-center justify-content-center"
				v-if="isLoading"
		>
			<div class="row">
				<div class="spinner-grow text-primary mt-4" role="status">
					<span class="sr-only">Loading...</span>
				</div>
			</div>
			<div class="row">
				<p class="mt-2">{{ getStrings.loadeditor }}</p>
			</div>
		</div>
		<div>
			<div v-if="!isLoading" class="text-right mb-2">
				<a href="#" v-on:click="reloadFrame"
				><i class="icon fa fa-refresh fa-fw" title="refresh" role="img"></i>
					Editor neu laden</a
				>
			</div>
		</div>
		<div class="holder">
			<Minimap :padName="padName" v-if="!isLoading"/>
			<iframe
					id="writerview"
					v-bind:src="link"
					title="Texteditor"
					v-if="!isLoading"
			></iframe>
		</div>
	</div>
</template>
<script>
import Minimap from "./minimap.vue";

export default {
	data: () => ({}),
	components: {
		Minimap,
	},
	name: "EditorComponent",
	computed: {
		getStrings() {
			return this.$store.getters.getStrings;
		},
		isLoading() {
			return this.link === null || this.padName === null;
		},
		link() {
			return this.$store.state.base.editorLink;
		},
		padName() {
			return this.$store.state.base.padName;
		},
	},
	methods: {
		reloadFrame: function () {
			const element = document.getElementById("writerview");
			element.src += "";
		},
	},
};
</script>

<style scoped>
iframe {
	width: 85%;
	height: 700px;
	border-style: none;
	float: right;
}

.holder {
	width: 100%;
	height: 100%;
	position: relative;
}
</style>

