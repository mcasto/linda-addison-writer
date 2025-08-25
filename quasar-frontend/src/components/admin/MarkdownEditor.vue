<template>
  <div>
    <q-toolbar>
      <q-tabs v-model="tab">
        <q-tab name="editor" label="Editor"> </q-tab>
        <q-tab name="preview" label="Preview"></q-tab>
      </q-tabs>
    </q-toolbar>

    <q-tab-panels v-model="tab" dark>
      <q-tab-panel name="editor">
        <q-input
          type="textarea"
          outlined
          label="Contents"
          stack-label
          v-model="contents"
          :rows="rows || 6"
          dark
          color="white"
        >
        </q-input>
      </q-tab-panel>
      <q-tab-panel name="preview">
        <div v-html="preview"></div>
      </q-tab-panel>
    </q-tab-panels>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import MarkdownIt from "markdown-it";
const md = new MarkdownIt({ html: true, breaks: true });

const tab = ref("editor");

const contents = defineModel();
const props = defineProps(["rows"]);

const preview = computed(() => {
  return md.render(contents.value);
});
</script>
