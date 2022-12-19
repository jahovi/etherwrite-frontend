<template>
	<div>
		<h4>Projektteilnehmer(innen)</h4>
		<ul class="legend">
			<li v-for="user in users" :key="user.id">
				<i v-if="user.color && user.color.startsWith('#')" class="fa fa-square" :style="{ color: user.color }"></i>
				<i v-else class="fa fa-square-o" style="color: gray"></i>
				{{ user.fullName }}
			</li>
		</ul>
	</div>
</template>

<script>
import store from "../../store";
import Communication from "../../classes/communication";

export default {
	data() {
		return {
			authors: {},
		};
	},
	props: {
		id: String,
		isMock: Boolean,
	},
	computed: {
		users() {
			if (this.isMock) {
				return [{
					id: 0,
					fullName: "User 1",
					color: "#d4d",
				}, {
					id: 1,
					fullName: "User 2",
					color: "#4d4",
				}];
			} else {
				return store.state.users.users.map(user => ({
					...user,
					...Object.values(this.authors).find(u => u.mapper2author === String(user.id)),
				}));
			}
		},
	},
	name: "groupParticipants",
	async mounted() {
		this.authors = await Communication.getFromEVA("minimap/authorInfo");
	},
	methods: {},
};
</script>
<style scoped>
@import "../../style/charts.scss";
</style>