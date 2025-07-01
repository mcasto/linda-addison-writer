<template>
  <page-container>
    <q-card>
      <q-toolbar>
        <q-toolbar-title>
          Lessons & Blessings
        </q-toolbar-title>
      </q-toolbar>
      <q-card-section>
        <div v-html="store.lessons_blessings.header"></div>
      </q-card-section>
      <q-card-section>
        <div v-html="store.lessons_blessings.poem" class="text-center"></div>
      </q-card-section>
    </q-card>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed } from "vue";

const store = useStore();
</script>
