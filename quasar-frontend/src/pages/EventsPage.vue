<template>
  <page-container>
    <q-card>
      <q-toolbar>
        <q-toolbar-title v-if="Screen.gt.xs">
          <div v-html="title"></div>
        </q-toolbar-title>

        <q-tabs v-model="tab">
          <q-tab
            v-for="tab of tabList"
            :key="`event-tab-${tab.name}`"
            :disable="tab.disable"
            :name="tab.name"
          >
            <div>
              {{ tab.name }}
              <q-badge class="q-ml-xs" color="primary">{{ tab.count }}</q-badge>
            </div>
          </q-tab>
        </q-tabs>
      </q-toolbar>

      <q-card-section v-if="tab">
        <events-table :meta="store.events[tab]"></events-table>
      </q-card-section>
    </q-card>
  </page-container>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

import PageContainer from "src/components/PageContainer.vue";
import EventsTable from "src/components/EventsTable.vue";
import { Screen } from "quasar";

const store = useStore();

const tab = ref(null);

const tabList = computed(() => {
  const list = [];
  for (let key of ["current", "future", "past"]) {
    list.push({
      name: key,
      label: key.toUpperCase(),
      disable: store.events[key] === null,
      count: store.events[key] === null ? 0 : store.events[key].total,
    });
  }

  return list;
});

const title = computed(() => {
  switch (tab.value) {
    case "past":
      return "Past Events &mdash; We missed you!";
      break;

    case "future":
      return "Future Events &mdash; Hope to see you there!";
      break;

    default:
      return "Current Events &mdash; Welcome!";
  }
});

onMounted(() => {
  const firstIndex = tabList.value.findIndex(({ disable }) => !disable);
  tab.value = tabList.value[firstIndex].name;
});
</script>
