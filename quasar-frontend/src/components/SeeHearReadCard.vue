<template>
  <q-card style="width: 100%;" class="q-my-xs q-mx-md">
    <q-toolbar>
      <q-toolbar-title
        v-html="row.title"
        class="shr-card-title"
        :class="Screen.lt.md ? 'text-subtitle2' : ''"
      >
      </q-toolbar-title>
      <a :href="row.url" target="_blank">
        <q-btn icon="link" flat round></q-btn>
      </a>
    </q-toolbar>
    <q-separator></q-separator>
    <q-card-section>
      <div v-html="row.contents"></div>
      <div class="flex justify-end">
        {{ format(parseISO(row.date), "PP") }}
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { format, parseISO } from "date-fns";
import { Screen } from "quasar";

const props = defineProps(["row"]);
</script>

<style>
.shr-card-title {
  white-space: normal;
  word-break: break-word;
}
</style>
