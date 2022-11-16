import Communication from "../classes/communication";

export default {
	state: {
		padName: null,
		groupId: null,
		editorLink: null,
	},
	mutations: {
		setEditorInfo(state, payload) {
			state.padName = payload.padName;
			state.groupId = payload.groupId;
			state.editorLink = payload.link;
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