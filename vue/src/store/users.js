import Communication from "../classes/communication";

export default {
	namespaced: true,
	state: {
		groups: [],
		authorInfo: null,
	},
	getters: {
		usersById(state) {
			return state.groups
					.map(g => g.members)
					.reduce((result, users) => {
						users.forEach(user => result[user.id] = user);
						return result;
					}, {});
		},
		usersByEpId(state) {
			return state.groups
					.map(g => g.members)
					.reduce((result, users) => {
						users
								.filter(user => user.epId)
								.forEach(user => result[user.epId] = user);
						return result;
					}, {});
		},
		users(state) {
			return state.groups
					.map(g => g.members)
					.reduce((result, members) => [...result, ...members], []);
		},
		groups(state) {
			return state.groups;
		},
	},
	mutations: {
		setGroups(state, payload) {
			state.groups = payload;
		},
		setAuthorInfo(state, payload) {
			const groups = state.groups;
			Object.entries(payload).forEach(([epId, epAuthorData]) => {
				if (!epAuthorData.mapper2author) {
					return;
				}
				for (const group of groups) {
					const author = group.members
							.find(a => a.id === parseInt(epAuthorData.mapper2author));
					if (author) {
						author.epId = epId;
						author.epAlias = epAuthorData.epAlias;
						author.color = epAuthorData.color;
					}
				}
			});
			state.groups = groups;
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
			const groups = await Communication.webservice("getAuthors", {cmid});
			const authorInfo = await Communication.getFromEVA("authors");
			commit("setGroups", groups);
			commit("setAuthorInfo", authorInfo);
		},
		async initialize({dispatch}) {
			await dispatch("load");
			setInterval(() => dispatch("load"), 5000);
		},
	},
};