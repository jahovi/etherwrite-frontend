<!-- eslint-disable indent -->
<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
	<div>
		<div v-if="!loading">
			<div class="text-right mb-2">
				<a href="#" v-on:click="reloadFrame"
				><i class="icon fa fa-refresh fa-fw" title="refresh" role="img"></i>
					Editor neu laden</a
				>
			</div>
		</div>
		<div class="holder" v-if="!loading">
			<Minimap :padName="padName"/>
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

export default {
	data: () => ({
		loading: true,
	}),
	components: {
		Minimap,
	},
	name: "EditorComponent",
	computed: {
		getStrings() {
			return this.$store.getters.getStrings;
		},
		link() {
			return this.$store.state.base.editorLink;
		},
		padName() {
			return this.$store.state.base.padName;
		},
	},
	mounted() {
		Communication.getFromEVA("etherpad/authorize").then(response => {
			const expiry = new Date();
			expiry.setDate(new Date().getDate() + 1);
			document.cookie = `token=${response.token}; expires=${expiry}; path=/`;
			this.loading = false;
		});
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

