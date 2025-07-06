<template>
  <page-container>
    <q-card class="q-pb-xl">
      <q-table
        :rows="store.finds.data"
        row-key="id"
        :pagination="pagination"
        :loading="loading"
        hide-pagination
        :columns="columns"
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
                Showing {{ store.finds.from }} to {{ store.finds.to }} of
                {{ store.finds.total }} entries
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
                  :max="store.finds.last_page"
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
            <q-td
              key="title"
              :props="props"
              style="width: 70vw; max-width: 70vw;"
            >
              <div class="ellipsis" style="width: 100%;">
                <a
                  :href="props.row.url"
                  target="_blank"
                  class="text-primary text-subtitle1"
                >
                  {{ props.row.title }}
                </a>
              </div>
            </q-td>
            <q-td key="date" :props="props" class="text-subtitle1">
              {{ format(parseISO(props.row.date), "PP") }}
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
    </q-card>
  </page-container>
</template>

<script setup>
import { format, parseISO } from "date-fns";
import { Screen } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import SeeHearReadCard from "src/components/SeeHearReadCard.vue";

import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

const filter = ref(null);
const loading = ref(false);

const pagination = ref({
  sortBy: "date",
  descending: true,
  page: 1,
  rowsPerPage: 10,
});

const columns = [
  {
    name: "expand",
    label: "",
    field: "",
    align: "left",
  },
  {
    name: "title",
    required: true,
    label: "Title",
    align: "left",
    field: "title",
    sortable: true,
  },
  {
    name: "date",
    align: "center",
    label: "Date",
    field: "date",
    sortable: true,
  },
];

const sectionType = ref(null);

const sectionTypes = computed(() => {
  return store.findTypes.map((type) => {
    return {
      ...type,
      name: type.name.toUpperCase(),
    };
  });
});

const findPath = computed(() => {
  let path = `/finds/${sectionType.value.id}?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;
  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

watch([sectionType, filter], async () => {
  pagination.value.page = 1;

  store.finds = await callApi({
    path: findPath.value,
    method: "get",
  });
});

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    store.finds = await callApi({
      path: findPath.value,
      method: "get",
    });
  },
  { deep: true }
);

onMounted(() => {
  sectionType.value = sectionTypes.value[0];
});
</script>
