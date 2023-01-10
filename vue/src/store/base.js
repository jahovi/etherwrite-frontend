import Communication from "../classes/communication";

export default {
	state: {
		editorInstances:[],
		isModerator: false,
		evaUri: null,
		jwt: null,
		initialized: false,
	},
	mutations: {
		setEditorInfo(state, payload) {
			state.editorInstances = payload.editorInstances;
			state.isModerator = payload.isModerator;
			state.evaUri = payload.eva;
			state.jwt = payload.jwt;
			state.initialized = true;
		},
	},
	actions: {
		async loadEditorBaseInfo({commit, rootGetters}) {
			try {
				const req = await Communication.webservice("getEditorLink", {
					cmid: rootGetters.getCMID,
				});
				commit("setEditorInfo", req);
			} catch (error) {
				commit("setAlertWithTimeout", ["alert-danger", error.message, 3000]);
			}
		},
	},
};