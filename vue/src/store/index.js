/* eslint-disable import/no-unresolved */
/* These are libraries provided by moodle. */
import moodleAjax from "core/ajax";
import moodleStorage from "core/localstorage";
import { createStore } from "vuex";
import base from "./base";
import users from "./users";
import widgets from "./widgets";

/*
	@author Marc Burchart
	@copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de
*/

export default createStore({
	state: {
		courseModuleID: 0,
		contextID: 0,
		strings: {},
		alertShow: false,
		alertType: "alert-primary",
		alertMessage: "",
		widgetCatalogOpen: false,
	},
	// strict: process.env.NODE_ENV !== 'production',
	getters: {
		getStrings: function (state) {
			return state.strings;
		},
		getCMID: function (state) {
			return state.courseModuleID;
		},
		getAlertShow: function (state) {
			return state.alertShow;
		},
		getAlertType: function (state) {
			return state.alertType;
		},
		getAlertMessage: function (state) {
			return state.alertMessage;
		},
		isWidgetCatalogOpen: function (state) {
			return state.widgetCatalogOpen;
		},
	},
	mutations: {
		setCourseModuleID: function (state, id) {
			state.courseModuleID = id;
		},
		setContextID: function (state, id) {
			state.contextID = id;
		},
		setStrings: function (state, strings) {
			state.strings = strings;
		},
		setAlertPermanent: function (state, arr) {
			scroll(0, 0);
			state.alertShow = true;
			state.alertType = arr[0];
			state.alertMessage = arr[1];
		},
		setAlertWithTimeout: function (state, arr) {
			scroll(0, 0);
			state.alertShow = true;
			state.alertType = arr[0];
			state.alertMessage = arr[1];
			new Promise(
				resolve => setTimeout(resolve, arr[2]),
			).then(
				() => {
					state.alertShow = false;
					state.alertType = "alert-primary";
					state.alertMessage = "";
				},
			);
		},
		removeAlert: function (state) {
			state.alertShow = false;
			state.alertType = "alert-primary";
			state.alertMessage = "";
		},
		setWidgetCatalogOpen: function (state) {
			state.widgetCatalogOpen = !state.widgetCatalogOpen;
		},
	},
	actions: {
		async loadComponentStrings(context) {
			const lang = document.documentElement.lang.replace(/-/g, "_");
			const cacheKey = "mod_write/strings/" + lang;
			const cachedStrings = moodleStorage.get(cacheKey);
			if (cachedStrings) {
				context.commit("setStrings", JSON.parse(cachedStrings));
			} else {
				const request = {
					methodname: "core_get_component_strings",
					args: {
						"component": "mod_write",
						lang,
					},
				};
				const loadedStrings = await moodleAjax.call([request])[0];
				let strings = {};
				loadedStrings.forEach((s) => {
					strings[s.stringid] = s.string;
				});
				context.commit("setStrings", strings);
				moodleStorage.set(cacheKey, JSON.stringify(strings));
			}
		},
	},
	modules: {
		base,
		users,
		widgets,
	},
});
