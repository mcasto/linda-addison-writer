<template>
  <page-container>
    <q-toolbar class="justify-end">
      <q-btn
        icon="add"
        round
        color="primary"
        @click="editDialog = { visible: true, honor: newHonorTemplate() }"
      ></q-btn>
    </q-toolbar>
    <div class="row">
      <div
        class="col-4 q-pa-sm"
        v-for="honor of store.admin.honors"
        :key="`honor-${honor.id}`"
      >
        <q-card class="full-height">
          <q-card-actions class="justify-between">
            {{ honor.year }} :: {{ honor.num }} won
            <div class="flex">
              <q-btn
                icon="delete"
                flat
                round
                color="negative"
                @click="deleteHonor(honor)"
              ></q-btn>
              <q-btn
                icon="edit"
                flat
                round
                color="primary"
                @click="editDialog = { visible: true, honor }"
              ></q-btn>
            </div>
          </q-card-actions>
          <q-separator></q-separator>
          <q-card-section>
            <div v-html="honor.contents"></div>
          </q-card-section>
        </q-card>
      </div>
    </div>

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
            setTimeout(() => {
              Loading.hide();
              window.location.reload();
              return;
            }, 500);
          } else {
            Notify.create({
              type: "negative",
              message: "Unable to delete record.",
            });
          }
        },
      },
    ],
  });
};
</script>
