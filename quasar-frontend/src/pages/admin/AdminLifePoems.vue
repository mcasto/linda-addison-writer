<template>
  <page-container>
    <q-table
      :columns="columns"
      :rows="store.admin.life_poems.data"
      row-key="id"
      :pagination="pagination"
      :loading="loading"
      hide-pagination
      dense
    >
      <template #top>
        <div class="flex justify-between items-center q-px-xs full-width">
          <div>
            <q-input
              type="text"
              label="Search"
              v-model="filter"
              dense
              outlined
              clearable
              debounce="300"
            >
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>

          <div>
            <q-btn icon="add" round color="primary" @click="addPoem"></q-btn>
          </div>
        </div>
        <div class="flex justify-between items-center full-width q-px-sm">
          <div class="text-caption text-grey-7">
            Showing {{ store.admin.life_poems.from }} to
            {{ store.admin.life_poems.to }} of
            {{ store.admin.life_poems.total }} entries
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
              :max="store.admin.life_poems.last_page"
              :max-pages="4"
              direction-links
              boundary-links
              color="grey-8"
              active-color="primary"
              active-text-color="white"
            />
          </div>
        </div>
      </template>

      <template #body-cell-poem="props">
        <q-td class="text-left cursor-pointer">
          {{ props.row.raw }}
          <q-popup-edit v-model="props.row" v-slot="scope" @save="save">
            <q-input
              v-model="scope.value.raw"
              dense
              autofocus
              @keyup.enter="scope.set"
            />
          </q-popup-edit>
        </q-td>
      </template>

      <template #body-cell-tools="props">
        <q-td>
          <q-btn
            icon="delete"
            color="negative"
            round
            flat
            @click="deletePoem(props.row)"
          ></q-btn>
        </q-td>
      </template>
    </q-table>
  </page-container>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";

import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import MarkdownEditor from "src/components/MarkdownEditor.vue";
import { Notify, uid } from "quasar";
import { startsWith } from "lodash-es";
import { remove } from "lodash-es";
import { clone } from "lodash-es";

const store = useStore();

const columns = [
  {
    label: "Poem",
    name: "poem",
    align: "left",
  },
  {
    name: "tools",
  },
];

const filter = ref(null);
const loading = ref(null);

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.admin.life_poems.total,
});

const poemPath = computed(() => {
  let path = `/admin/life-poems?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;

  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

const addPoem = () => {
  store.admin.life_poems.data.unshift({
    id: `new-${uid()}`,
    raw: "New Life Poem",
    contents: "New Life Poem",
  });
};

const save = async (poem) => {
  const method = startsWith(poem.id, "new-") ? "post" : "put";

  const response = await callApi({
    path: `/admin/life-poems/${poem.id}`,
    method,
    payload: clone(poem),
    useAuth: true,
  });

  if (response.status == "ok") {
    window.location.reload();
    return;
  }

  console.log({ response });
};

const deletePoem = async (poem) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this poem?",
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: async () => {
          if (startsWith(poem.id, "new-")) {
            remove(store.admin.life_poems.data, ({ id }) => id == poem.id);
            return;
          }

          const response = await callApi({
            path: `/admin/life-poems/${poem.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            window.location.reload();
            return;
          }
        },
      },
    ],
  });
};

watch(filter, async () => {
  pagination.value.page = 1;

  store.admin.life_poems = await callApi({
    path: poemPath.value,
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

    store.admin.life_poems = await callApi({
      path: poemPath.value,
      method: "get",
      useAuth: true,
    });
  },
  { deep: true }
);
</script>
