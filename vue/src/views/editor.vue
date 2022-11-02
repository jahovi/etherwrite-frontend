<template>
    <div>
        <Editor />
    </div>
</template>
<script>
import Editor from "../components/editor.vue";
export default {
	data: function() {
		return {
			link: null,
		};
	},
	name: "editorView",
	components: {
		Editor,
	},
};
</script>
<style scoped>
</style>