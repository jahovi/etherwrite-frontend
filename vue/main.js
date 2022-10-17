import Vue from 'vue';
import VueRouter from 'vue-router';
import { store } from './store';
import generateRouter from "./router";
import App from "./app.vue";


function init(coursemoduleid, contextid) {
    // We need to overwrite the variable for lazy loading.
    __webpack_public_path__ = M.cfg.wwwroot + '/mod/write/amd/build/';

    Vue.use(VueRouter);

    store.commit('setCourseModuleID', coursemoduleid);
    store.commit('setContextID', contextid);
    store.dispatch('loadComponentStrings');  

    const router = generateRouter(coursemoduleid);

    new Vue({
        el: '#mod-write-app',
        store,
        router,
        render: (h) => h(App),
    });
}

export { init };
