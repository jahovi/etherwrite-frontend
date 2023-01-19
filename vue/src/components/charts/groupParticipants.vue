<template>
  <div>
    <h4>{{ i18n["widgets.groupParticipants"] }}</h4>
    <h5 v-if="group">
      {{ i18n["widgets.groupParticipants.group"] }}
      <strong>{{ group.name }}</strong>
    </h5>
    <ul class="legend" v-if="group">
      <li v-for="user in members" :key="user.id">
        <template v-if="user.color && `${user.color}`.startsWith('#')">
          <i
            v-if="user.color === '#FFF' || user.color === '#FFFFFF'"
            class="fa fa-square-o"
            style="color: black"
          ></i>
          <i v-else class="fa fa-square" :style="{ color: user.color }"></i>
        </template>
        <i v-else class="fa fa-square-o" style="color: lightgrey"></i>
        {{ user.fullName }}
      </li>
    </ul>
  </div>
</template>

<script>
import store from "../../store";

export default {
	data() {
		return {
			authors: {},
		};
	},
	props: {
		id: String,
		isMock: Boolean,
		padName: String,
	},
	computed: {
		activeGroup() {
			return parseInt(this.padName.split("_g_").pop());
		},
		group() {
			if (this.isMock) {
				return {
					id: 0,
					name: "Group 1",
					members: [{
						id: 0,
						fullName: "User 1",
						color: "#d4d",
					}, {
						id: 1,
						fullName: "User 2",
						color: "#4d4",
					}],
				};
			} else {
				return store.getters["users/groups"].find(g => g.id === this.activeGroup);
			}
		},
		members() {
			const members = this.group.members;
			members.sort((m1, m2) => m1.fullName.split(" ").pop().localeCompare(m2.fullName.split(" ").pop()));
			return members;
		},
		i18n() {
			return store.getters.getStrings;
		},
	},
	name: "groupParticipants",
	methods: {},
};
</script>
<style scoped>
@import "../../style/charts.scss";
</style>