<template>
  <page-container>
    <q-uploader
      accept=".txt"
      auto-upload
      hide-upload-btn
      label="Upload Biblio Text File"
      no-thumbnails
      color="accent"
      url="/api/admin/import-biblio-text"
      method="post"
      field-name="textFile"
      :headers="uploadHeaders"
      @uploaded="displayResponse"
    ></q-uploader>

    <div
      v-if="uploadResponse?.status == 'error'"
      class="q-mt-xl q-pa-md bg-red-3 text-h6"
    >
      <span class="text-h5">Error:</span> {{ uploadResponse.message }}
    </div>

    <div v-if="uploadResponse" class="q-mt-xl">
      <q-card>
        <q-toolbar class="bg-grey-5">
          <q-toolbar-title>
            Data For Import
          </q-toolbar-title>
        </q-toolbar>
        <q-separator></q-separator>
        <q-card-section>
          <q-table :columns="columns" :rows="uploadResponse">
            <template #body-cell-title="props">
              <q-td style="max-width: 15vw;" class="ellipsis">
                {{ props.value }}
                <q-tooltip>
                  {{ props.value }}
                </q-tooltip>
              </q-td>
            </template>
            <template #body-cell-publication="props">
              <q-td style="max-width: 15vw;" class="ellipsis">
                {{ props.value }}
                <q-tooltip>
                  {{ props.value }}
                </q-tooltip>
              </q-td>
            </template>
            <template #body-cell-tools="props">
              <q-td>
                <q-btn icon="delete" flat round></q-btn>
                <q-btn icon="edit" flat round></q-btn>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </div>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const uploadResponse = ref(null);

const uploadHeaders = [
  {
    name: "Authorization",
    value: `Bearer ${store.token}`,
  },
];

const columns = [
  {
    label: "Type",
    name: "biblioType",
    field: (row) => row.biblio.type,
    align: "center",
  },
  {
    label: "Title",
    name: "title",
    field: (row) => row.biblio.title,
    align: "left",
  },
  {
    label: "Sort Date",
    name: "sortDate",
    field: (row) => row.biblio.sort_date,
    align: "center",
  },
  {
    label: "Publication",
    name: "publication",
    field: (row) => row.biblioPub.publication,
    align: "left",
  },
  {
    label: "Published Date",
    name: "pubDate",
    field: (row) => row.biblioPub.pub_date,
    align: "center",
  },
  {
    label: "Display Date",
    name: "displayDate",
    field: (row) => row.biblioPub.display_date,
    align: "center",
  },
  {
    name: "tools",
  },
];
const rows = ref([]);

const displayResponse = ({ xhr }) => {
  const data = JSON.parse(xhr.response);

  uploadResponse.value = data;
};
</script>
