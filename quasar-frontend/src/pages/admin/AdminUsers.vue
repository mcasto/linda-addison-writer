<template>
  <page-container>
    <q-table
      :columns="columns"
      :rows="store.admin.users"
      :pagination="{ rowsPerPage: 0 }"
      hide-bottom
    >
      <template #top-right>
        <q-btn icon="add" round color="primary" size="sm"></q-btn>
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
              type="text"
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
    </q-table>

    <admin-user-password
      v-model="passwordDialog"
      @update="updateUser"
    ></admin-user-password>
  </page-container>
</template>

<script setup>
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import AdminUserPassword from "src/components/admin/AdminUserPassword.vue";
import { ref } from "vue";

const store = useStore();

const passwordDialog = ref({
  visible: false,
  password: null,
  confirm: null,
  row: null,
});

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
];

const updateUser = async (row = null) => {
  row = row || {
    ...passwordDialog.value.row,
    password: passwordDialog.value.password,
  };

  const response = await callApi({
    path: `/admin/users/${row.id}`,
    method: "put",
    payload: row,
  });

  passwordDialog.value.visible = false;
};
</script>
