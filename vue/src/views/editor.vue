<template>
  <div class="container-fluid">
    <div
        class="d-flex flex-column align-items-center justify-content-center"
        v-if="isLoading"
    >
      <div class="row">
        <div class="spinner-grow text-primary mt-4" role="status">
          <span class="sr-only">{{ getStrings["loading"] }}</span>
        </div>
      </div>
      <div class="row">
        <p class="mt-2">{{ getStrings["loadeditor"] }}</p>
      </div>
    </div>
    <Editor v-else/>
  </div>
</template>
<script>
import Editor from "../components/editor.vue";

export default {
  data: function () {
    return {
      link: null,
    };
  },
  name: "editorView",
  components: {
    Editor,
  },
  computed: {
    getStrings() {
      return this.$store.getters.getStrings;
    },
    isLoading() {
      return !this.$store.state.base.initialized;
    },
  },
};
</script>
<style scoped>
</style>