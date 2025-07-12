<template>
  <page-container>
    <q-table
      :rows="store.admin.events.data"
      :columns="columns"
      row-key="id"
      :pagination="pagination"
      :loading="loading"
      hide-pagination
      dense
      grid
    >
      <template #top>
        <div
          class="flex justify-between items-center full-width q-mt-md q-px-sm"
        >
          <q-btn round color="primary" icon="add"></q-btn>
          <div class="text-caption text-grey-7">
            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} entries
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
              :max="meta.last_page"
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
      </template>

      <template #item="props">
        <q-card class="q-ma-sm" style="width: 25vw;">
          <q-card-section style="height: 200px;">
            <div class="text-subtitle1">
              {{ props.row.name }}
            </div>
            <div class="row">
              <div class="text-caption col">
                {{ props.row.start_date }}
              </div>
              <div class="text-caption col">
                {{ props.row.end_date }}
              </div>
            </div>
          </q-card-section>
          <q-separator></q-separator>
          <q-card-actions class="justify-between">
            <q-btn icon="delete" flat round color="negative"></q-btn>
            <q-btn icon="edit" flat round color="primary"></q-btn>
          </q-card-actions>
        </q-card>
      </template>
    </q-table>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";

const store = useStore();

const loading = ref(false);

console.log(store.admin.events);

const meta = computed(() => {
  const { from, to, total, last_page } = store.admin.events;
  return { from, to, total, last_page };
});

const pagination = ref({
  sortBy: "start_date",
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.pubs.total,
});

const columns = [
  {
    name: "expand",
    label: "",
    field: "",
    align: "left",
  },
  {
    name: "name",
    required: true,
    label: "Name",
    align: "left",
    field: "name",
    sortable: true,
  },
  {
    name: "schedule",
    align: "left",
    label: "Schedule",
    field: "schedule",
    sortable: true,
  },
];

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    const path = `/admin/events?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;

    const response = await callApi({
      path,
      method: "get",
      useAuth: true,
    });

    store.admin.events = response;
  },
  { deep: true }
);
</script>
