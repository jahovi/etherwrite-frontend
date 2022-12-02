import Communication from "../classes/communication";

export default {
	state: {
		padName: null,
		isModerator: false,
		groupId: null,
		editorLink: null,
		evaUri: null,
		jwt: null,
		initialized: false,
	},
	mutations: {
		setEditorInfo(state, payload) {
			state.padName = payload.padName;
			state.isModerator = payload.isModerator;
			state.groupId = payload.groupId;
			state.editorLink = payload.link;
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