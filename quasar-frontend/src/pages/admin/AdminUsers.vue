<template>
  <page-container>
    <q-table
      :columns="columns"
      :rows="store.admin.users"
      :pagination="{ rowsPerPage: 0 }"
      hide-bottom
    >
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
          <q-btn icon="mdi-lock-reset" round flat></q-btn>
        </q-td>
      </template>

      <template #body-cell-permissions="props">
        <q-td class="text-center">
          <q-checkbox
            v-model="props.row.permissions"
            :true-value="1"
            :false-value="0"
          ></q-checkbox>
        </q-td>
      </template>
    </q-table>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";

const store = useStore();

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

const updateUser = async (row) => {
  console.log({ update: row });
};
</script>
