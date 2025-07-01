<template>
  <page-container>
    <div class="bg-white shadow-1">
      <q-toolbar>
        <q-toolbar-title>
          Biography
        </q-toolbar-title>
      </q-toolbar>
      <q-tabs v-model="tab">
        <q-tab name="shortest">
          Shortest
        </q-tab>
        <q-tab name="short">
          Short
        </q-tab>
        <q-tab name="longer">
          Longer
        </q-tab>
      </q-tabs>
    </div>

    <q-tab-panels v-model="tab">
      <q-tab-panel name="shortest">
        <div v-html="store.bio.shortest"></div>
      </q-tab-panel>
      <q-tab-panel name="short">
        <div v-html="store.bio.short"></div>
      </q-tab-panel>
      <q-tab-panel name="longer">
        <div v-html="store.bio.longer"></div>
      </q-tab-panel>
    </q-tab-panels>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const tab = ref("shortest");

const tabList = Object.keys(store.bio);
</script>
