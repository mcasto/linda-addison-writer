<template>
  <q-table
    :columns="columns"
    :rows="rows"
    :pagination="{ rowsPerPage: 10 }"
    dense
  >
    <template #top>
      <div class="row full-width q-gutter-x-xs items-center q-mb-md">
        <div class="col-3">
          <q-input
            type="text"
            label="Search"
            v-model="filter"
            outlined
            dense
            clearable
            @keydown.esc="filter = null"
            class="full-width"
          >
            <template #append> <q-icon name="search"></q-icon> </template>
          </q-input>
        </div>

        <div class="col-3">
          <q-select
            v-model="selectedType"
            :options="publicationTypes"
            label="Publication Type"
            outlined
            dense
            option-label="name"
            clearable
          />
        </div>

        <div class="col-3">
          <q-select
            v-model="selectedYear"
            :options="years"
            label="Year"
            outlined
            dense
            clearable
          />
        </div>

        <div class="col text-right">
          <q-btn icon="mdi-new-box" color="primary" flat size="lg" />
        </div>
      </div>
    </template>
    <template #body-cell-tools="props">
      <q-td class="text-center">
        <q-btn
          flat
          round
          icon="mdi-pencil"
          @click="editPublication(props.row)"
          size="small"
        />
        <q-btn
          flat
          round
          icon="mdi-delete"
          @click="deletePublication(props.row)"
          size="small"
        />
      </q-td>
    </template>
    <template #body-cell-url="props">
      <q-td class="text-center">
        <q-btn flat icon="mdi-link" @click="openUrl(props.row.url)" />
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { uniq, uniqBy } from "lodash-es";
import { useStore } from "src/stores/store";
import { computed, ref } from "vue";

const store = useStore();

console.log({ publications: store.admin.publications });

const filter = ref(null);
const selectedType = ref(null);
const selectedYear = ref(null);

const columns = [
  { name: "tools", align: "center" },
  {
    label: "Publication Type",
    field: (rec) => rec.publication_type.name,
    align: "center",
  },
  {
    label: "Year",
    field: "year",
    align: "center",
  },
  {
    label: "Title",
    field: "title",
    align: "left",
  },
  { name: "url", label: "URL", field: "url", align: "center" },
];

const publicationTypes = computed(() => {
  return uniqBy(
    store.admin.publications.map(({ publication_type }) => publication_type),
    "name"
  );
});

const rows = computed(() => {
  return store.admin.publications.filter((pub) => {
    const searchVal = !filter.value
      ? true
      : pub.title.toLowerCase().includes(filter.value.toLowerCase());

    const typeVal =
      !selectedType.value || pub.publication_type.id === selectedType.value.id;

    const yearVal = !selectedYear.value || pub.year === selectedYear.value;

    return searchVal && typeVal && yearVal;
  });
});

const years = computed(() => {
  return uniq(store.admin.publications.map((pub) => pub.year)).sort();
});

const openUrl = (url) => {
  window.open(url, "_blank");
};
</script>
