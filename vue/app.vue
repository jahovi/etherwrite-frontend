<!-- @author Marc Burchart -->
<!-- @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de -->
<template>
    <div>
        <transition name="fade">
            <div class="alert mt-4" v-bind:class="getAlertType" v-if="getAlertShow" role="alert">
                {{getAlertMessage}}
            </div>
        </transition>
        <ul class="nav nav-tabs nav-vue">
            <li class="nav-item">
                <router-link to="/" class="nav-link">Aufgabenstellung</router-link>
            </li>
            <li class="nav-item">
                <router-link to="/editor" class="nav-link">Texteditor</router-link>
            </li>
            <li class="nav-item">
                <router-link to="/dashboard" class="nav-link">Dashboard</router-link>
            </li>
        </ul>
        <div class="tab-content main-vue">
            <div class="mx-3 mt-4 mb->3">
                <router-view />
            </div>
        </div>
    </div>
</template>
<script>
export default {
	data: function() {
		return {};
	},
	name: "app",
	computed: {
		getAlertShow: function() {
			return this.$store.getters.getAlertShow;
		},
		getAlertType: function() {
			return this.$store.getters.getAlertType;
		},
		getAlertMessage: function() {
			return this.$store.getters.getAlertMessage;
		},
	},
};
</script>
<style scoped>
</style>
