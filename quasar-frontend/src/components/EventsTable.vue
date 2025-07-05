<template>
  <div class="q-pb-xl">
    <q-table
      flat
      :rows="store.events[key].data"
      :columns="columns"
      row-key="id"
      :pagination="pagination"
      :loading="loading"
      hide-pagination
      dense
    >
      <template #top>
        <div
          class="flex justify-between items-center full-width q-mt-md q-px-sm"
        >
          <div class="text-caption text-grey-7">
            Showing {{ meta.current_page }} to {{ meta.per_page }} of
            {{ meta.total }} entries
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

      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td auto-width>
            <q-btn
              color="primary"
              dense
              @click="props.expand = !props.expand"
              :icon="props.expand ? 'remove' : 'add'"
              size="sm"
            />
          </q-td>
          <q-td key="name" :props="props" style="width: 70vw; max-width: 70vw;">
            <div class="ellipsis" style="width: 100%;">
              <div v-if="key == 'past'">
                {{ props.row.name }}
              </div>
              <a
                :href="props.row.url"
                target="_blank"
                class="text-primary text-subtitle1"
                v-else
              >
                {{ props.row.name }}
              </a>
            </div>
          </q-td>
          <q-td key="schedule" :props="props" class="text-subtitle1">
            {{ props.row.schedule }}
          </q-td>
        </q-tr>
        <q-tr v-show="props.expand" :props="props">
          <q-td colspan="100%">
            <div
              v-html="props.row.contents"
              class="text-wrap overflow-hidden"
              style="white-space: normal; word-break: break-word;"
            ></div>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script setup>
import { Screen } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";

const store = useStore();

const props = defineProps(["meta"]);

const key = computed(() => {
  return props.meta.path.split("/").pop();
});

const loading = ref(false);

const pagination = ref({
  sortBy: "year",
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.events[key.value].total,
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

    const path = `/events/${key.value}?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;

    const response = await callApi({
      path,
      method: "get",
    });

    store.events[key.value] = response;
  },
  { deep: true }
);
</script>
