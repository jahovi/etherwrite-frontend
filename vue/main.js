import { createApp } from "vue";
import store from "./src/store";
import App from "./app.vue";
import router from "./src/router";

export function init(coursemoduleid, contextid) {
	store.commit("setCourseModuleID", parseInt(coursemoduleid));
	store.commit("setContextID", parseInt(contextid));
	store.dispatch("loadComponentStrings");

	createApp(App)
		.use(router)
		.use(store)
		.mount("#mod-write-app");
}

// We need to overwrite the variable for lazy loading.
/* eslint-disable-next-line no-undef */
__webpack_public_path__ = M.cfg.wwwroot + "/mod/write/amd/build/";
