<template>
  <page-container>
    <q-table
      :rows="store.admin.freebies"
      :columns="columns"
      hide-bottom
      :pagination="{ rowsPerPage: -1 }"
    >
      <template #header-cell-tools>
        <q-th class="text-right">
          <q-btn
            icon="add"
            round
            color="primary"
            @click="
              editDialog = { visible: true, freebie: newFreebieTemplate() }
            "
          ></q-btn>
        </q-th>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn
            icon="delete"
            round
            flat
            color="negative"
            @click="deleteFreebie(props.row)"
          ></q-btn>
          <q-btn
            icon="edit"
            round
            flat
            color="primary"
            @click="editDialog = { visible: true, freebie: props.row }"
          ></q-btn>
        </q-td>
      </template>
    </q-table>

    <admin-freebie
      v-model="editDialog.visible"
      :freebie="editDialog.freebie"
    ></admin-freebie>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import AdminFreebie from "src/components/admin/AdminFreebie.vue";
import { ref } from "vue";
import { Notify, uid } from "quasar";
import callApi from "src/assets/call-api";

const store = useStore();

const newFreebieTemplate = () => {
  return {
    id: `new-${uid()}`,
    title: "New Freebie",
    note: "",
    sub: "",
    raw: "",
  };
};

const editDialog = ref({
  visible: false,
  freebie: null,
});

const deleteFreebie = async (freebie) => {
  Notify.create({
    type: "warning",
    message: `Are you sure you want to delete ${freebie.title} from the Freebies?`,
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/freebies/${freebie.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            window.location.reload();
            return;
          }

          console.log({ response });

          Loading.hide();

          Notify.create({
            type: "negative",
            message: "Unable to delete record.",
          });
        },
      },
    ],
  });
};

const columns = [
  {
    name: "title",
    field: "title",
    label: "Title",
    align: "left",
    sortable: true,
  },
  {
    name: "tools",
  },
];
</script>
