<template>
  <page-container>
    <div class="row shadow-1 bg-white q-pb-xs items-center q-px-xs">
      <div class="col-12 col-sm-6 col-md-4 text-h6 text-center">
        Publications
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <q-input
          type="text"
          label="Search"
          v-model="filter"
          style="width: 25vw;"
          dense
          outlined
          clearable
        ></q-input>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <q-select
          label="Publication Type"
          stack-label
          dense
          v-model="pubType"
          :options="pubTypes"
          outlined
          option-label="name"
        ></q-select>
      </div>
    </div>

    <q-table :rows="rows"></q-table>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

const store = useStore();

console.log({ publications: store.publications });

const tab = ref(0);
const pubType = ref(null);
const filter = ref(null);

const columns = [
  {
    label: "TITLE",
    name: "title",
    field: "title",
  },
  {
    label: "YEAR",
    name: "year",
    field: "year",
  },
];

const rows = computed(() => {
  if (!pubType.value) {
    return [];
  }
  const pub = store.publications.find(({ id }) => id == pubType.value.id);
  return pub.publications;
});

const pubTypes = store.publications.map(({ id, name }) => {
  return {
    id,
    name: name.toUpperCase(),
  };
});

onMounted(() => {
  pubType.value = pubTypes[0];
});
</script>
