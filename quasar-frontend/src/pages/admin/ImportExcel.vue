<template>
  <page-container>
    <q-uploader
      accept=".xlsx"
      auto-upload
      hide-upload-btn
      label="Upload Excel Spreadsheet"
      no-thumbnails
      color="accent"
      url="/api/admin/import-excel"
      method="post"
      field-name="excelFile"
      :headers="uploadHeaders"
      @uploaded="displayResponse"
    ></q-uploader>

    <div
      v-if="uploadResponse?.status == 'error'"
      class="q-mt-xl q-pa-md bg-red-3 text-h6"
    >
      <span class="text-h5">Error:</span> {{ uploadResponse.message }}
    </div>

    <div v-if="uploadResponse?.data" class="q-mt-xl">
      <q-card>
        <q-toolbar class="bg-grey-5">
          <q-toolbar-title>
            Summary of Imported Data
          </q-toolbar-title>
        </q-toolbar>
        <q-separator></q-separator>
        <q-card-section>
          <template v-for="key of Object.keys(uploadResponse.data)" :key="key">
            <div>{{ key }}: {{ uploadResponse.data[key].length }}</div>
          </template>
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

const displayResponse = ({ xhr }) => {
  uploadResponse.value = JSON.parse(xhr.response);
};
</script>
