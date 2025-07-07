<template>
  <page-container>
    <q-card>
      <q-toolbar>
        <q-toolbar-title>
          <div class="ellipsis" :class="Screen.xs ? 'text-subtitle2' : ''">
            {{ store.freebie.title }}
          </div>
          <div class="text-subtitle1">
            {{ store.freebie.note }}
          </div>
        </q-toolbar-title>

        <div>
          <div class="text-subtitle2">Next Update:</div>
          <div class="text-subtitle1">{{ nextUpdate }}</div>
        </div>
      </q-toolbar>

      <q-separator></q-separator>

      <q-card-section>
        <div v-html="store.freebie.contents"></div>
      </q-card-section>
      <q-separator></q-separator>
      <div class="flex justify-end q-pa-md" v-html="store.freebie.sub"></div>
    </q-card>
  </page-container>
</template>

<script setup>
import { add, format, parseISO } from "date-fns";
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed } from "vue";

const store = useStore();

const nextUpdate = computed(() => {
  return format(add(parseISO(store.freebie.end_date), { days: 1 }), "PP");
});
</script>
