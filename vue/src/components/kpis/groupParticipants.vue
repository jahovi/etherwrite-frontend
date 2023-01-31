<template>
	<div class="groupParticipants">
		<h5>
			<u>
				{{ getStrings["widgets.groupParticipants"] }}
				{{ getStrings["widgets.groupParticipants.group"] }}
			</u>
			<strong>{{ " " + group.name }}</strong>
		</h5>
		<div class="container-fluid" v-if="group">
			<div class="row">
				<div v-for="user in members" :key="user.id" class="col-12 col-sm-6 col-md-4 col-xl-2 user">
					<template v-if="user.color && `${user.color}`.startsWith('#')">
						<i v-if="user.color === '#FFF' || user.color === '#FFFFFF'" class="fa fa-square-o"
							style="color: black"></i>
						<i v-else class="fa fa-square" :style="{ color: user.color }"></i>
					</template>
					<i v-else class="fa fa-square-o" style="color: lightgrey"></i>
					{{ user.fullName }}
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="js">
import store from "../../store";

export default {
	name: "groupParticipants",
	data() {
		return {};
	},
	props: {
		padName: String,
	},
	computed: {
		activeGroup() {
			return parseInt(this.padName.split("_g_").pop());
		},
		group() {
			return store.getters["users/groups"].find(g => g.id === this.activeGroup);
		},
		members() {
			const members = this.group.members;
			members.sort((m1, m2) => m1.fullName.split(" ").pop().localeCompare(m2.fullName.split(" ").pop()));
			return members;
		},
		getStrings() {
			return store.getters.getStrings;
		},
		padName() { },
	},
	methods: {},
	mounted: function () { },
};
</script>

<style scoped lang="css">
@import "../../style/charts.scss";

.groupParticipants {
	padding-top: 10px;
	padding-bottom: 10px;
}

h5 {
	margin-bottom: 5px;
}

.group {
	margin-bottom: 5px;
}

.user {
	padding-left: 0px;
}
</style>
