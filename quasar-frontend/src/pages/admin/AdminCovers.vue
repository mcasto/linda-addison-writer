<template>
  <page-container>
    <q-table
      :rows="store.admin.covers"
      :columns="columns"
      :pagination="{ rowsPerPage: 0 }"
      hide-bottom
    >
      <template #body-cell-image="props">
        <q-td width="20%">
          <q-img :src="props.row.image_url"></q-img>
        </q-td>
      </template>
      <template #body-cell-details="props">
        <q-td>
          <div class="text-h6 q-mb-sm">
            {{ props.row.title }}
            <a :href="props.row.purchase_url" target="_blank">
              <q-btn icon="link" flat round color="blue-9"></q-btn>
            </a>
          </div>
          <div
            v-html="props.row.contents"
            class="text-wrap overflow-hidden"
            style="word-wrap: break-word; white-space: normal;"
          ></div>
        </q-td>
      </template>
      <template #body-cell-tools="props">
        <q-td>
          <div>
            <q-btn
              icon="fa-solid fa-circle-up"
              flat
              round
              color="green"
              @click="reorder(props.row, -1)"
              :disable="props.rowIndex == 0"
            ></q-btn>
            <q-btn
              icon="fa-solid fa-circle-down"
              flat
              round
              color="green"
              @click="reorder(props.row, 1)"
              :disable="props.rowIndex == store.admin.covers.length - 1"
            ></q-btn>
          </div>
          <div>
            <q-btn
              icon="edit"
              flat
              round
              color="primary"
              @click="editDialog = { visible: true, cover: props.row }"
            ></q-btn>
            <q-btn icon="delete" flat round color="negative"></q-btn>
          </div>
        </q-td>
      </template>
    </q-table>

    <admin-cover
      v-model="editDialog.visible"
      :cover="editDialog.cover"
    ></admin-cover>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import AdminCover from "src/components/admin/AdminCover.vue";
import { ref } from "vue";
import { uid } from "quasar";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";

const store = useStore();

const editDialog = ref({
  visible: false,
  cover: null,
});

const columns = [
  {
    label: "Image",
    name: "image",
    align: "left",
  },
  {
    label: "Details",
    name: "details",
    align: "left",
  },
  {
    label: "",
    name: "tools",
  },
];

const newCoverTemplate = () => {
  return {
    id: `new-${uid()}`,
    title: "New Cover",
    purchase_url: "",
    image: "",
    sort_order: 0,
    contents: "",
    image_url: "http://linda-addison-writer.test/storage/new-cover.webp",
  };
};

const reorder = async (cover, direction) => {
  // find index of cover
  const currentIndex = store.admin.covers.findIndex(
    (item) => item.id === cover.id
  );

  // calculate new index
  const newIndex = currentIndex + direction;

  // swap elements
  [store.admin.covers[currentIndex], store.admin.covers[newIndex]] = [
    store.admin.covers[newIndex],
    store.admin.covers[currentIndex],
  ];

  // reset sort_order
  for (let idx = 0; idx < store.admin.covers.length; idx++) {
    store.admin.covers[idx].sort_order = idx + 1;
    const payload = clone(store.admin.covers[idx]);
    const response = await callApi({
      path: `/admin/covers/${payload.id}`,
      method: "put",
      payload,
      useAuth: true,
    });
  }
};
</script>
