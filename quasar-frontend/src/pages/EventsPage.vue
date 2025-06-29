<template>
  <page-container>
    <events-table
      title="Happening Now!"
      :events="current"
      v-if="current.length > 0"
    ></events-table>
    <events-table
      title="Don't Miss These Upcoming Events!"
      :events="future"
      v-if="future.length > 0"
    ></events-table>
    <events-table
      title="We Missed You!"
      :events="past"
      v-if="past.length > 0"
    ></events-table>
  </page-container>
</template>

<script setup>
import { isWithinInterval, parseISO } from "date-fns";
import { useStore } from "src/stores/store";
import { computed } from "vue";

import PageContainer from "src/components/PageContainer.vue";
import EventsTable from "src/components/EventsTable.vue";

const store = useStore();

const past = computed(() => {
  return store.events.filter(({ start_date }) => {
    return parseISO(start_date) < new Date();
  });
});

const future = computed(() => {
  return store.events.filter(({ start_date }) => {
    return parseISO(start_date) > new Date();
  });
});

const current = computed(() => {
  return store.events.filter(({ id, start_date, end_date }) => {
    const start = parseISO(start_date);

    if (!end_date) {
      return false;
    }

    const end = parseISO(end_date);

    return isWithinInterval(start_date, end_date);
  });
});
</script>
