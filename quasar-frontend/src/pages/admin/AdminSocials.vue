<template>
  <page-container>
    <q-table
      :rows="store.admin.socials"
      hide-bottom
      :pagination="{ rowsPerPage: 0 }"
      :columns="columns"
    >
      <template #header-cell-tools>
        <q-th class="text-right">
          <q-btn
            icon="add"
            color="primary"
            round
            size="sm"
            @click="
              showDialog = {
                visible: true,
                social: { icon: null, url: null },
              }
            "
          ></q-btn>
        </q-th>
      </template>

      <template #body="props">
        <q-tr>
          <q-td class="text-center">
            <q-icon :name="props.row.icon" size="sm"></q-icon>
          </q-td>
          <q-td class="text-center">
            <q-btn icon="link" round flat></q-btn>
          </q-td>
          <q-td class="text-right">
            <q-btn
              flat
              icon="edit"
              @click="showDialog = { visible: true, social: props.row }"
            ></q-btn>
            <q-btn flat icon="delete" @click="deleteSocial(props.row)"></q-btn>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <social-dialog v-model="showDialog"></social-dialog>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";
import SocialDialog from "src/components/admin/SocialDialog.vue";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { remove } from "lodash-es";

const store = useStore();

const showDialog = ref({
  visible: false,
  social: null,
});

const columns = [
  {
    label: "Icon",
    name: "icon",
    align: "center",
  },
  {
    label: "Link",
    name: "link",
    align: "center",
  },
  {
    name: "tools",
    align: "right",
  },
];

const deleteSocial = async (item) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this?",
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/socials/${item.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.item) {
            remove(store.admin.socials, ({ id }) => id == item.id);
          }
        },
      },
    ],
  });
};
</script>
