<template>
  <page-container>
    <div class="q-pb-xl">
      <q-table
        :rows="filteredAndPaginatedRows"
        :columns="columns"
        row-key="id"
        :pagination="pagination"
        :loading="loading"
        hide-pagination
      >
        <template #top>
          <div class="row items-center q-px-xs full-width">
            <div class="col-12 col-md-4 text-h6 text-center q-px-xs">
              Publications
            </div>
            <div class="col-12 col-sm-6 col-md-4 q-px-xs">
              <q-select
                label="Publication Type"
                stack-label
                dense
                v-model="pubType"
                :options="pubTypes"
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
            <div class="row items-center q-gutter-x-sm">
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
              <q-pagination
                v-model="pagination.page"
                :max="maxPaginationPages"
                :max-pages="6"
                direction-links
                boundary-links
                color="grey-8"
                active-color="primary"
                active-text-color="white"
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
            <q-td key="title" :props="props">
              <a
                :href="props.row.url"
                target="_blank"
                class="text-primary text-subtitle1"
              >
                {{ props.row.title }}
              </a>
            </q-td>
            <q-td key="year" :props="props" class="text-subtitle1">
              {{ props.row.year }}
            </q-td>
          </q-tr>
          <q-tr v-show="props.expand" :props="props">
            <q-td colspan="100%">
              <div class="expanded-content">
                <div v-html="props.row.contents"></div>
              </div>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();
const tab = ref(0);
const pubType = ref(null);
const filter = ref(null);
const loading = ref(false);

const pagination = ref({
  sortBy: "year",
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
    field: (row) => row.title,
    format: (val) => `${val}`,
    sortable: true,
  },
  {
    name: "year",
    align: "center",
    label: "Year",
    field: "year",
    sortable: true,
  },
];

const pubTypes = computed(() => {
  return store.publications.map(({ id, name }) => ({
    id,
    name: name.toUpperCase(),
  }));
});

const currentPublicationRows = computed(() => {
  if (!pubType.value) return [];
  const pub = store.publications.find(({ id }) => id == pubType.value.id);
  return pub?.publications || [];
});

const filteredRows = computed(() => {
  if (!filter.value) return currentPublicationRows.value;

  const searchTerm = filter.value.toLowerCase();
  const isYearSearch = /^\d{4}$/.test(filter.value);

  return currentPublicationRows.value.filter((row) => {
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

watch([pubType, filter], () => {
  pagination.value.page = 1;
});

watch(
  pagination,
  (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }
  },
  { deep: true }
);

onMounted(() => {
  pubType.value = pubTypes.value[0];
});
</script>

<style lang="scss">
.my-sticky-header-table {
  .expanded-content {
    padding: 16px;
    width: 100%;
    overflow-x: auto;
    max-width: 100%;
    word-wrap: break-word;
    white-space: pre-wrap;

    p,
    div,
    span {
      white-space: normal;
      word-break: break-word;
    }

    table {
      width: 100%;
      max-width: 100%;
      border-collapse: collapse;

      td,
      th {
        word-break: break-word;
      }
    }

    img {
      max-width: 100%;
      height: auto;
    }
  }

  .q-table__expanded-item {
    padding: 0;
  }

  .per-page-select {
    .q-field__control {
      height: 32px;
    }
    .q-field__native {
      min-height: auto;
      padding: 0;
    }
  }

  .q-pagination {
    .q-btn {
      width: 32px;
      height: 32px;
      margin: 0 2px;
      &.q-btn--actionable {
        border-radius: 4px;
      }
    }
  }
}
</style>
