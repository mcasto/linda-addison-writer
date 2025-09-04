<template>
  <div>
    <a
      :href="row[tableFields[tableName].url]"
      target="_blank"
      class="text-primary text-subtitle1"
      v-if="!broken"
    >
      {{ row[tableFields[tableName].name] }}
    </a>
    <div v-else>
      {{ row[tableFields[tableName].name] }}
    </div>
  </div>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { computed } from "vue";

const props = defineProps(["tableName", "row"]);

const tableFields = {
  events: {
    name: "name",
    url: "url",
  },
  finds: {
    name: "title",
    url: "url",
  },
  online_resource_links: {
    name: "name",
    url: "url",
  },
  publications: {
    name: "title",
    url: "url",
  },
};

const store = useStore();

const broken = computed(() => {
  return props.row.broken_link && !props.row.broken_link.confirmed_working;
});
</script>
