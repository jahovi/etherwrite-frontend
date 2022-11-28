<template>
<<<<<<< HEAD
	<div :id="'chart-wrapper-' + id">
		{{ component }}
		<component :is="component" :id="'custom-chart-' + id" :isMock="isMock" />
=======
	<div :id="'chart-wrapper-' + id" :name="component">
		<component :is="component" :id="'custom-chart-' + id" :isMock="isMock" @click.capture="stopEvents"/>
>>>>>>> 3965282bfdba601920c817234a334edbc43796ff
		<slot name="btn"></slot>
	</div>
</template>

<script lang="js">
import barchart from "./charts/barchart.vue";
import authoringRatios from "./charts/authoringRatios.vue";

export default {
	name: "chart-wrapper",
	components: {
		// eslint-disable-next-line vue/no-unused-components
		barchart,
		// eslint-disable-next-line vue/no-unused-components
		authoringRatios,
	},
	props: {
		component: String,
		id: Number,
		isMock: Boolean,
	},
	data: function () {
		return {};
	},
	mounted() {
		// button clickable, component not clickable -> for wrapper in widget catalog
		if (this.isMock) {
			let comp = document.getElementById("custom-chart-" + this.id);
			comp.style.pointerEvents = "none";
		}
<<<<<<< HEAD
=======
	},
	methods: {
		stopEvents(event) {
			if (this.mockOnly) {
				event.stopPropagation();
				event.preventDefault();
			}
		},
>>>>>>> 3965282bfdba601920c817234a334edbc43796ff
	},
};
</script>

<style scoped lang="css">

</style>
