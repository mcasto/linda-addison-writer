<template>
  <page-container>
    <q-table
      :columns="columns"
      :rows="store.admin.users"
      :pagination="{ rowsPerPage: 0 }"
      hide-bottom
    >
      <template #top-right>
        <q-btn
          icon="add"
          round
          color="primary"
          size="sm"
          @click="
            userDialog = {
              visible: true,
              row: {
                name: null,
                email: null,
                password: null,
                permissions: false,
              },
              confirm: null,
            }
          "
        ></q-btn>
      </template>

      <template #body-cell-name="props">
        <q-td class="text-left cursor-pointer">
          {{ props.row.name }}
          <q-popup-edit
            v-model="props.row.name"
            v-slot="scope"
            buttons
            @update:model-value="updateUser(props.row)"
          >
            <q-input
              autofocus
              type="text"
              label="Name"
              v-model="scope.value"
            ></q-input>
          </q-popup-edit>
        </q-td>
      </template>

      <template #body-cell-email="props">
        <q-td class="text-left cursor-pointer">
          {{ props.row.email }}
          <q-popup-edit
            v-model="props.row.email"
            v-slot="scope"
            buttons
            @update:model-value="updateUser(props.row)"
          >
            <q-input
              autofocus
              type="email"
              label="Email"
              v-model="scope.value"
            ></q-input>
          </q-popup-edit>
        </q-td>
      </template>

      <template #body-cell-password="props">
        <q-td class="text-center">
          <q-btn
            icon="mdi-lock-reset"
            round
            flat
            @click="
              passwordDialog = {
                visible: true,
                password: null,
                confirm: null,
                row: props.row,
              }
            "
          ></q-btn>
        </q-td>
      </template>

      <template #body-cell-permissions="props">
        <q-td class="text-center">
          <q-checkbox
            v-model="props.row.permissions"
            :true-value="1"
            :false-value="0"
            @update:model-value="updateUser(props.row)"
          ></q-checkbox>
        </q-td>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn
            icon="delete"
            flat
            round
            @click="deleteUser(props.row)"
          ></q-btn>
        </q-td>
      </template>
    </q-table>

    <admin-user-password
      v-model="passwordDialog"
      @update="updateUser"
    ></admin-user-password>

    <admin-user-dialog
      v-model="userDialog"
      @update="updateUser"
    ></admin-user-dialog>
  </page-container>
</template>

<script setup>
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import AdminUserPassword from "src/components/admin/AdminUserPassword.vue";
import AdminUserDialog from "src/components/admin/AdminUserDialog.vue";
import { ref } from "vue";
import { Notify } from "quasar";
import { remove } from "lodash-es";
import { sortBy } from "lodash-es";

const store = useStore();

const passwordDialog = ref({
  visible: false,
  password: null,
  confirm: null,
  row: null,
});

const userDialog = ref({ visible: false, row: null });

const columns = [
  {
    label: "Name",
    name: "name",
    field: "name",
    align: "left",
  },
  {
    label: "Email",
    name: "email",
    field: "email",
    align: "left",
  },
  {
    label: "Password",
    name: "password",
    align: "center",
  },
  {
    label: "Permissions",
    name: "permissions",
    field: "permissions",
    align: "center",
  },
  {
    name: "tools",
  },
];

const updateUser = async (row) => {
  const basePath = "/admin/users";
  const method = row.id ? "put" : "post";
  const path = row.id ? `${basePath}/${row.id}` : basePath;

  const response = await callApi({
    path,
    method,
    payload: row,
  });

  if (response.user) {
    store.admin.users.push(response.user);
    store.admin.users = sortBy(store.admin.users, "name");
  }

  passwordDialog.value.visible = false;
  userDialog.value.visible = false;
};

const deleteUser = async (row) => {
  Notify.create({
    type: "warning",
    message: `Are you sure you want to delete ${row.name}?`,
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/users/${row.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            remove(store.admin.users, ({ id }) => id == row.id);
          }
        },
      },
    ],
  });
};
</script>
