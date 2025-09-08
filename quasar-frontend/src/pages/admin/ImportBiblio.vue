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
      v-if="store.admin.biblioImported?.status == 'error'"
      class="q-mt-xl q-pa-md bg-red-3 text-h6"
    >
      <span class="text-h5">Error:</span>
      {{ store.admin.biblioImported.message }}
    </div>

    <div v-if="store.admin.biblioImported" class="q-mt-xl">
      <q-card>
        <q-toolbar class="bg-grey-5">
          <q-toolbar-title>
            Data For Import
          </q-toolbar-title>
          <q-btn icon="mdi-import" color="accent" @click="finishImport">
            <q-tooltip>
              Finish Import
            </q-tooltip>
          </q-btn>
        </q-toolbar>
        <q-separator></q-separator>
        <q-card-section>
          <q-table :columns="columns" :rows="store.admin.biblioImported">
            <template #body-cell-biblioType="props">
              <q-td class="text-center">
                <q-icon :name="typeIcon(props.value)"></q-icon>
                <q-tooltip>
                  {{ props.value }}
                </q-tooltip>
              </q-td>
            </template>
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
                <q-btn
                  icon="delete"
                  flat
                  round
                  @click="deleteRec(props.row)"
                ></q-btn>
                <q-btn
                  icon="edit"
                  flat
                  round
                  :to="`/admin/edit-biblio/${props.row.id}`"
                ></q-btn>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </div>
  </page-container>
</template>

<script setup>
import { cloneDeep } from "lodash-es";
import { remove } from "lodash-es";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const uploadHeaders = [
  {
    name: "Authorization",
    value: `Bearer ${store.token}`,
  },
];

const typeIcon = (type) => {
  const icons = {
    poetry: "fa-solid fa-feather-pointed",
    fiction: "fa-solid fa-jedi",
    nonfiction: "fa-solid fa-book-atlas",
  };

  return icons[type];
};

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

const displayResponse = ({ xhr }) => {
  store.admin.biblioImported = JSON.parse(xhr.response);
};

const deleteRec = (rec) => {
  Notify.create({
    type: "warning",
    message: `Are you sure you want to delete<br />${rec.biblio.title}`,
    html: true,
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: () => {
          remove(store.admin.biblioImported, ({ id }) => id == rec.id);
        },
      },
    ],
  });
};

const finishImport = async () => {
  const payload = cloneDeep(store.admin.biblioImported);

  const response = await callApi({
    path: "/admin/finish-import-biblio-text",
    method: "post",
    payload,
    useAuth: true,
  });

  if (response.status == "ok") {
    store.admin.biblioImported = null;
    Notify.create({
      type: "positive",
      message: "Data successfully imported",
    });
  }
};
</script>
