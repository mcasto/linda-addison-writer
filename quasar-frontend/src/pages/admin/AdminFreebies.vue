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
          <q-btn icon="add" round color="primary"></q-btn>
        </q-th>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn icon="delete" round flat color="negative"></q-btn>
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

const store = useStore();

const editDialog = ref({
  visible: false,
  freebie: null,
});

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
