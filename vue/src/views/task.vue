<template>
	<div>
		<div v-if="show" v-html="task"></div>
		<small v-if="show && deadline">Deadline: <strong>{{ deadline.toLocaleString() }}</strong></small>
	</div>
</template>
<script>
import Communication from "../classes/communication";

export default {
	data: function () {
		return {
			show: false,
			task: null,
			deadline: null,
		};
	},
	name: "taskView",
	beforeMount: async function () {
		try {
			const cmid = this.$store.getters.getCMID;
			const req = await Communication.webservice("getTaskDescription", {cmid});
			this.show = true;
			this.task = req.description;
			if (req.deadline) {
				this.deadline = new Date(req.deadline * 1000);
			}
		} catch (error) {
			this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
		}
	},
};
</script>
<style scoped>
</style>