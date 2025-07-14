<template>
  <page-container>
    <q-table
      :columns="columns"
      :rows="store.admin.honors"
      hide-bottom
      :pagination="{ rowsPerPage: -1 }"
    >
      <template #header-cell-tools>
        <q-th class="text-right">
          <q-btn
            icon="add"
            round
            color="primary"
            @click="editDialog = { visible: true, honor: newHonorTemplate() }"
          ></q-btn>
        </q-th>
      </template>

      <template #body-cell-contents="props">
        <q-td class="text-left">
          <div v-html="props.row.contents"></div>
        </q-td>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn
            icon="delete"
            flat
            round
            color="negative"
            @click="deleteHonor(props.row)"
          ></q-btn>
          <q-btn
            icon="edit"
            flat
            round
            color="primary"
            @click="editDialog = { visible: true, honor: props.row }"
          ></q-btn>
        </q-td>
      </template>
    </q-table>

    <admin-honor
      v-model="editDialog.visible"
      :honor="editDialog.honor"
    ></admin-honor>
  </page-container>
</template>

<script setup>
import { Loading, Notify, uid } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import AdminHonor from "src/components/admin/AdminHonor.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const columns = [
  {
    label: "Year",
    name: "year",
    field: "year",
    align: "left",
    sortable: true,
  },
  {
    label: "Contents",
    name: "contents",
    align: "left",
    sortable: true,
  },
  {
    name: "tools",
  },
];

const editDialog = ref({
  visible: false,
  honor: null,
});

const newHonorTemplate = () => {
  return {
    id: `new-${uid()}`,
    year: new Date().getFullYear(),
    num: 1,
    raw: "",
  };
};

const deleteHonor = async (honor) => {
  Notify.create({
    type: "warning",
    message: `Are you sure you want to delete ${honor.title} from the Honors?`,
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/honors/${honor.id}`,
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
</script>
