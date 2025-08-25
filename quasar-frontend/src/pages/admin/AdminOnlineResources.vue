<template>
  <div>
    <q-table
      :rows="store.admin.online_resources.data"
      row-key="id"
      :pagination="pagination"
      :loading="loading"
      hide-pagination
      :columns="columns"
      dense
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
                  editDialog = {
                    visible: true,
                    resource: newResourceTemplate(),
                  }
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
              Showing {{ store.admin.online_resources.from }} to
              {{ store.admin.online_resources.to }} of
              {{ store.admin.online_resources.total }} entries
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
                :max="store.admin.online_resources.last_page"
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

      <template #body-cell-name="props">
        <q-td class="text-left">
          <q-btn
            icon="link"
            round
            flat
            color="primary"
            @click="openLink(props.row.url)"
          ></q-btn>
          {{ props.row.name }}
        </q-td>
      </template>

      <template #body-cell-category="props">
        <q-td class="text-left">
          {{ props.row.online_resource.header }}
        </q-td>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn
            icon="delete"
            color="negative"
            round
            flat
            @click="deleteResource(props.row)"
          ></q-btn>
          <q-btn
            icon="edit"
            color="primary"
            round
            flat
            @click="editDialog = { visible: true, resource: props.row }"
          ></q-btn>
        </q-td>
      </template>
    </q-table>

    <admin-resource
      v-model="editDialog.visible"
      :resource="editDialog.resource"
    ></admin-resource>
  </div>
</template>

<script setup>
import { clone } from "lodash-es";
import { Notify, uid } from "quasar";
import callApi from "src/assets/call-api";
import AdminResource from "src/components/admin/AdminResource.vue";

import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

console.log(store.admin.online_resources.data[0]);
console.log(store.resourceTypes);

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
    name: "name",
    label: "Name",
    align: "left",
    field: "name",
    sortable: true,
  },
  {
    name: "category",
    label: "Category",
    align: "left",
    sortable: true,
  },
  {
    name: "tools",
  },
];

const newResourceTemplate = () => {
  return {
    id: `new-${uid()}`,
    online_resource_id: clone(store.resourceTypes).shift().id,
    name: "New Online Resource",
    url: "",
  };
};

const editDialog = ref({
  visible: false,
  resource: null,
});

const resourcePath = computed(() => {
  let path = `/admin/online-resources?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;
  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

const deleteResource = async (resource) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this item?",
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          console.log({ delete: resource });
          // const response = await callApi({
          //   path: `/admin/online-resources/${resource.id}`,
          //   method: "delete",
          //   useAuth: true,
          // });

          // if (response.status == "ok") {
          //   window.location.reload();
          // }
        },
      },
    ],
  });
};

const openLink = (url) => {
  window.open(url);
};

watch([filter], async () => {
  pagination.value.page = 1;

  store.admin.online_resources = await callApi({
    path: resourcePath.value,
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

    store.admin.online_resources = await callApi({
      path: resourcePath.value,
      method: "get",
      useAuth: true,
    });
  },
  { deep: true }
);
</script>
