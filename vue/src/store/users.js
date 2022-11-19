import Communication from "../classes/communication";

export default {
	namespaced: true,
	state: {
		users: [],
	},
	getters: {
		usersById(state) {
			return state.users.reduce((result, user) => {
				result[user.id] = user;
				return result;
			}, {});
		},
	},
	mutations: {
		setUsers(state, users) {
			state.users = users;
		},
	},
	actions: {
		async getUser({getters, dispatch}, userId) {
			const user = getters.usersById(userId);
			if (user === undefined) {
				await dispatch("load");
			}
			return getters.usersById(userId);
		},
		async load({rootGetters, commit}) {
			const cmid = rootGetters.getCMID;
			commit("setUsers", await Communication.webservice("getAuthors", {cmid}));
		},
	},
};