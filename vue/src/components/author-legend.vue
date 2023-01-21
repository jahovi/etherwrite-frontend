<template>
  <ul class="legend mt-3" v-if="fullAuthors.length" style="flex-shrink: 0">
    <li>
      <strong>{{ i18n["legend"] }}</strong>
      <small v-if="interactive"><br/>({{ i18n["legend.click-to-highlight"] }})</small>
    </li>
    <li v-for="author in fullAuthors" :key="author.epId" @click="focusAuthor(author)" :class="{
      'unfocused': isUnfocused(author),
      'clickable': interactive,
    }">
      <i class="fa fa-square" :style="{ color: author.color }"></i>
      {{ author.fullName }}
    </li>
  </ul>
</template>
<script lang="js">
import store from "../store";

/**
 * This component can be used to show a colored legend for all authors that are involved in a chart visualization.
 *
 * INTERACTIVE:
 * If the prop "interactive" is set to true, the legend allows a user to click an author to highlight it, i.e. almost hide everyone else.
 * The component will automatically create and apply CSS styles to use in other components to hide the corresponding visualization parts. For this to work,
 * you will need an SVG element with the class "focus-author" and bind the "name" attribute to the focusedAuthor which is emitted by the legend.
 * You should then fill the "name" attribute of everything you draw which belongs to one author, with the etherpad ID of this author.
 * See cohesionDiagram.vue for an example application.
 */
export default {
  name: "AuthorLegend",
  data() {
    return {
      styleElement: null,
    };
  },
  props: {
    /**
     * ID of the chart this legend is used in.
     */
    chartId: {
      type: String,
      required: true,
    },
    /**
     * An array of ids that represent authors which can be found in the users Vuex store.
     * Either give author ids, or a full array of author objects.
     */
    authorIds: {
      type: Array,
      required: false,
    },
    /**
     * An array of authors that contain all author infos.
     * Either give author ids, or a full array of author objects.
     */
    authors: {
      type: Array,
      required: false,
    },
    /**
     * Set this if you want the component to be interactive. If set, the "focusedAuthor" prop must also be bound.
     */
    interactive: Boolean,
    /**
     * Bind this to a variable to receive updates on it when an author is focused.
     */
    focusedAuthor: {
      type: String,
      required: false,
    },
  },
  mounted() {
    this.insertOrUpdateStyleElement();
  },
  computed: {
    /**
     * Returns the authors for the given authorIds, duplicates removed and sorted by their name.
     * @return {*[]} An array of author objects.
     */
    fullAuthors() {
      if (this.authors) {
        return this.authors;
      } else if (this.authorIds) {
        const authors = Array.from(new Set(this.authorIds))
            .map(authorId => store.getters["users/usersByEpId"][authorId]);
        authors.sort((a, b) => a.fullName.localeCompare(b.fullName));
        return authors;
      } else {
        throw new Error("Either provide author ids or a full object array of authors.");
      }
    },
    styles() {
      return this.fullAuthors
          .map(author => author.epId)
          .map(id => `#${this.chartId} svg.focus-author[name="${id}"] > :not([name="${id}"]) { opacity: 0.2 } `)
          .join("\n");
    },
    i18n() {
      return store.getters.getStrings;
    },
  },
  methods: {
    insertOrUpdateStyleElement() {
      const styleElement = this.styleElement || document.createElement("style");
      styleElement.lang = "text/css";
      styleElement.innerHTML = this.styles;
      if (!this.styleElement) {
        document.body.prepend(styleElement);
        this.styleElement = styleElement;
      }
    },
    isUnfocused(author) {
      if (!this.focusedAuthor) {
        return false;
      }
      return this.focusedAuthor !== author.epId;
    },
    focusAuthor(author) {
      if (!this.interactive) {
        return;
      }
      const newFocusedAuthor = author ? author.epId : null;
      if (newFocusedAuthor === this.focusedAuthor) {
        this.$emit("update:focusedAuthor", null);
      } else {
        this.$emit("update:focusedAuthor", newFocusedAuthor);
      }
    },
  },
  watch: {
    styles() {
      this.insertOrUpdateStyleElement();
    },
  },
};
</script>
<style lang="css" scoped>
.unfocused {
  opacity: 0.2;
}

.legend {
  min-width: 150px;
}

small {
  color: gray;
}
</style>