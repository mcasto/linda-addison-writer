<template>
  <page-container>
    <q-card class="q-pb-xl">
      <q-table
        :rows="filteredAndPaginatedRows"
        row-key="id"
        :pagination="pagination"
        :loading="loading"
        hide-pagination
        grid
      >
        <template #top>
          <div class="bg-white full-width">
            <div class="row items-center q-px-xs full-width">
              <div class="col-12 col-md-4 text-h6 text-center q-px-xs">
                See / Hear / Read
              </div>
              <div class="col-12 col-sm-6 col-md-4 q-px-xs">
                <q-select
                  label="Section"
                  stack-label
                  dense
                  v-model="sectionType"
                  :options="sectionTypes"
                  outlined
                  option-label="name"
                />
              </div>
              <div class="col-12 col-sm-6 col-md-4 q-px-xs">
                <q-input
                  type="text"
                  label="Search"
                  v-model="filter"
                  dense
                  outlined
                  clearable
                >
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </div>
            <div
              class="flex justify-between items-center full-width q-mt-md q-px-sm"
            >
              <div class="text-caption text-grey-7">
                Showing {{ firstItemIndex }} to {{ lastItemIndex }} of
                {{ filteredRows.length }} entries
                <span v-if="filter" class="text-italic">(filtered)</span>
              </div>
              <div
                class="row items-center q-gutter-x-sm"
                :class="Screen.xs ? 'justify-center column' : ''"
              >
                <q-select
                  dense
                  outlined
                  v-model="pagination.rowsPerPage"
                  :options="[5, 10, 15, 20, 25, 50]"
                  style="min-width: 100px;"
                  class="per-page-select q-pt-md"
                  @update:model-value="pagination.page = 1"
                  hint="Rows Per Page"
                />
                <q-separator v-if="Screen.xs" class="q-my-sm"></q-separator>
                <q-pagination
                  v-model="pagination.page"
                  :max="maxPaginationPages"
                  :max-pages="4"
                  direction-links
                  boundary-links
                  color="grey-8"
                  active-color="primary"
                  active-text-color="white"
                  :class="Screen.xs ? 'q-pb-xs' : ''"
                />
              </div>
            </div>
          </div>
        </template>

        <template #item="props">
          <see-hear-read-card :row="props.row"></see-hear-read-card>
        </template>
      </q-table>
    </q-card>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import SeeHearReadCard from "src/components/SeeHearReadCard.vue";

import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

const filter = ref(null);
const loading = ref(false);

const pagination = ref({
  sortBy: "year",
  descending: true,
  page: 1,
  rowsPerPage: 10,
});

const see = computed(() => {
  return store.finds.find(({ name }) => name == "see");
});
const hear = computed(() => {
  return store.finds.find(({ name }) => name == "hear");
});
const read = computed(() => {
  return store.finds.find(({ name }) => name == "read");
});

const sectionType = ref(null);

const sectionTypes = computed(() => {
  return [
    {
      id: see.value.id,
      name: "SEE",
      finds: see.value.finds,
    },
    {
      id: hear.value.id,
      name: "HEAR",
      finds: hear.value.finds,
    },
    {
      id: read.value.id,
      name: "READ",
      finds: read.value.finds,
    },
  ];
});

const currentFindRows = computed(() => {
  if (!sectionType.value) return [];
  const section = store.finds.find(({ id }) => id == sectionType.value.id);
  return section?.finds || [];
});

const filteredRows = computed(() => {
  if (!filter.value) return currentFindRows.value;

  const searchTerm = filter.value.toLowerCase();
  const isYearSearch = /^\d{4}$/.test(filter.value);

  return currentFindRows.value.filter((row) => {
    if (isYearSearch) {
      return String(row.year).includes(searchTerm);
    }
    return (
      row.title.toLowerCase().includes(searchTerm) ||
      (row.contents && row.contents.toLowerCase().includes(searchTerm))
    );
  });
});

const filteredAndPaginatedRows = computed(() => {
  const start = (pagination.value.page - 1) * pagination.value.rowsPerPage;
  const end = start + pagination.value.rowsPerPage;
  return filteredRows.value.slice(start, end);
});

const firstItemIndex = computed(() => {
  return Math.min(
    (pagination.value.page - 1) * pagination.value.rowsPerPage + 1,
    filteredRows.value.length
  );
});

const lastItemIndex = computed(() => {
  const end = pagination.value.page * pagination.value.rowsPerPage;
  return Math.min(end, filteredRows.value.length);
});

const maxPaginationPages = computed(() => {
  return Math.max(
    1,
    Math.ceil(filteredRows.value.length / pagination.value.rowsPerPage)
  );
});

watch([sectionType, filter], () => {
  pagination.value.page = 1;
});

onMounted(() => {
  sectionType.value = sectionTypes.value[0];
});
</script>
