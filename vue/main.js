import { createApp } from "vue";
import store from "./src/store";
import App from "./app.vue";
import router from "./src/router";
import BootstrapVue3 from "bootstrap-vue-3";
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css'

export function init(coursemoduleid, contextid) {
	store.commit("setCourseModuleID", parseInt(coursemoduleid));
	store.commit("setContextID", parseInt(contextid));
	store.dispatch("loadComponentStrings");

	createApp(App)
			.use(router)
			.use(store)
			.use(BootstrapVue3)
		.mount("#mod-write-app");
}

// We need to overwrite the variable for lazy loading.
/* eslint-disable-next-line no-undef */
__webpack_public_path__ = M.cfg.wwwroot + "/mod/write/amd/build/";
