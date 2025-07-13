<template>
  <page-container>
    <q-card class="q-pb-xl">
      <q-table
        :rows="store.admin.finds.data"
        row-key="id"
        :pagination="pagination"
        :loading="loading"
        hide-pagination
        :columns="columns"
      >
        <template #top>
          <div class="bg-white full-width">
            <div class="row q-px-xs full-width">
              <div class="col-6 q-px-xs">
                <q-btn
                  icon="add"
                  round
                  color="primary"
                  @click="
                    editDialog = { visible: true, find: newFindTemplate() }
                  "
                ></q-btn>
              </div>
              <div class="col-6 q-px-xs">
                <q-input
                  type="text"
                  label="Search"
                  v-model="filter"
                  dense
                  outlined
                  clearable
                  debounce="100"
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
                Showing {{ store.admin.finds.from }} to
                {{ store.admin.finds.to }} of
                {{ store.admin.finds.total }} entries
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
                  :max="store.admin.finds.last_page"
                  :max-pages="4"
                  direction-links
                  boundary-links
                  color="grey-8"
                  active-color="primary"
                  active-text-color="white"
                />
              </div>
            </div>
          </div>
        </template>

        <template #body-cell-type="props">
          <q-td class="text-left">
            {{ props.row.find_type.name.toUpperCase() }}
          </q-td>
        </template>

        <template #body-cell-tools="props">
          <q-td class="text-right">
            <q-btn
              icon="delete"
              color="negative"
              round
              flat
              @click="deleteFind(props.row)"
            ></q-btn>
            <q-btn
              icon="edit"
              color="primary"
              round
              flat
              @click="editDialog = { visible: true, find: props.row }"
            ></q-btn>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <admin-find
      v-model="editDialog.visible"
      :find="editDialog.find"
    ></admin-find>
  </page-container>
</template>

<script setup>
import { formatISO9075 } from "date-fns";
import { Notify, uid } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import AdminFind from "src/components/admin/AdminFind.vue";

import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

console.log({ find: store.admin.finds.data[0] });

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
    name: "title",
    label: "Title",
    align: "left",
    field: "title",
    sortable: true,
  },
  {
    name: "type",
    label: "Type",
    align: "left",
    sortable: true,
  },
  {
    name: "date",
    align: "center",
    label: "Date",
    field: "date",
    sortable: true,
  },
  {
    name: "tools",
  },
];

const newFindTemplate = () => {
  return {
    id: `new-${uid()}`,
    title: "New See/Hear/Read",
    url: "",
    date: formatISO9075(new Date(), { representation: "date" }),
    find_type: store.findTypes.find(({ name }) => name == "read"),
  };
};

const editDialog = ref({
  visible: false,
  find: null,
});

const findPath = computed(() => {
  let path = `/admin/finds?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;
  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

const deleteFind = async (find) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this item?",
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/finds/${find.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            window.location.reload();
          }
        },
      },
    ],
  });
};

watch([filter], async () => {
  pagination.value.page = 1;

  store.admin.finds = await callApi({
    path: findPath.value,
    method: "get",
    useAuth: true,
  });
});

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    store.admin.finds = await callApi({
      path: findPath.value,
      method: "get",
      useAuth: true,
    });
  },
  { deep: true }
);
</script>
