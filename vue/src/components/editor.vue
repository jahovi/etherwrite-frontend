<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
    <div>
        <div class="d-flex flex-column align-items-center justify-content-center" v-if="isLoading">
            <div class="row">
                <div class="spinner-grow text-primary mt-4" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="row">
                <p class="mt-2">{{getStrings.loadeditor}}</p>
            </div>
        </div>
        <div>
            <div v-if="!isLoading" class="text-right mb-2">
                <a href="#" v-on:click="reloadFrame"><i class="icon fa fa-refresh fa-fw " title="refresh" role="img"></i> Editor neu laden</a>
            </div>
        </div>
        <iframe id="writerview" v-bind:src="link" title="Texteditor" v-if="!isLoading"></iframe>
    </div>
</template>
<script>
import Communication from "../classes/communication";
export default {
	data: function(){
		return {
			link: null,
		};
	},
	name: "EditorComponent",
	beforeMount: async function(){
		try{
			const cmid = this.$store.getters.getCMID;
			const req = await Communication.webservice("getEditorLink", {cmid});
			if(req.success !== true){
				if(req.data){
					this.$store.commit("setAlertWithTimeout", ["alert-danger", req.data, 3000]);
				} else {
					this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
				}
				return;
			}
			this.link = req.data.link;
		} catch(error){
			this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
		}
	},
	computed: {
		getStrings: function(){
			return this.$store.getters.getStrings;
		},
		isLoading: function(){
			return this.link === null;
		},
	},
	methods: {
		reloadFrame: function(){
			const element = document.getElementById("writerview");
			element.src += "";
		},
	},
};
</script>
<style scoped>
    iframe {
        width: 100%;
        height: 700px;
        border-style: none;
    }
</style>

