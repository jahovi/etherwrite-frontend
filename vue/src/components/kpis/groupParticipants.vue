<template>
  <div class="p-3">
    <div class="container-fluid" v-if="group">
      <div class="row d-flex flex-wrap align-items-center">
        <span class="pr-2">
          <u>{{ getStrings["widgets.groupParticipants"] }}</u>
          <span v-if="group" class="mr-2"> ({{ getStrings["widgets.groupParticipants.group"] }} <strong>{{ group.name }}</strong>)</span>
        </span>
        <div v-for="user in members" :key="user.id" class="px-2">
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
  },
  methods: {},
  mounted: function () {
  },
};
</script>

<style scoped lang="css">
@import "../../style/charts.scss";

</style>
