<template>
  <page-container>
    <q-tabs v-model="tableName">
      <q-tab
        v-for="name of tableList"
        :key="`broken-link-table-tab-${name}`"
        :label="name"
        :name="name"
      ></q-tab>
    </q-tabs>

    <q-table
      :rows="linksForTable"
      hide-bottom
      :pagination="{ rowsPerPage: 0 }"
      :columns="columns[tableName]"
    >
      <template #body-cell-icon="props">
        <q-td class="text-center">
          <q-icon :name="props.value" size="sm"></q-icon>
        </q-td>
      </template>

      <template #body-cell-link="props">
        <q-td class="text-center">
          <q-btn
            icon="mdi-open-in-new"
            flat
            round
            @click="openLink(props.value, props.row)"
          ></q-btn>
          <q-btn icon="edit" flat round>
            <q-popup-edit
              v-slot="scope"
              v-model="props.row.updated_link"
              @update:model-value="updateRec(props.row, 'link')"
              buttons
            >
              <q-input
                type="text"
                label="New URL"
                v-model="scope.value"
                style="width: 40vw;"
                autofocus
                outlined
                dense
              ></q-input>
            </q-popup-edit>
          </q-btn>
        </q-td>
      </template>

      <template #body-cell-status="props">
        <q-td class="text-left">
          <q-btn-group push>
            <q-btn
              :color="!!props.row.confirmed_broken ? 'red' : 'grey'"
              icon="mdi-link-off"
              @click="updateRec(props.row, 'broken')"
            ></q-btn>
            <q-btn
              :color="
                !!!props.row.confirmed_broken && !!!props.row.confirmed_working
                  ? 'blue'
                  : 'grey'
              "
              icon="mdi-help-circle"
              @click="updateRec(props.row, 'unknown')"
            ></q-btn>
            <q-btn
              :color="!!props.row.confirmed_working ? 'green' : 'grey'"
              icon="mdi-check-decagram-outline"
              @click="updateRec(props.row, 'working')"
            ></q-btn>
          </q-btn-group>
        </q-td>
      </template>
    </q-table>
  </page-container>
</template>

<script setup>
import { formatISO9075 } from "date-fns";
import { snakeCase } from "lodash-es";
import { startCase } from "lodash-es";
import { clone } from "lodash-es";
import { remove } from "lodash-es";
import { uniq } from "lodash-es";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

const store = useStore();

const tableName = ref(null);

const universalColumns = [
  { label: "Status", name: "status", align: "left" },
  {
    label: "Date Confirmed",
    name: "confirmed_date",
    field: (row) => row.confirmed_date || "N/A",
    align: "left",
  },
];

const columns = {
  covers: [
    {
      label: "Title",
      name: "name",
      align: "left",
      field: (row) => row.linkable.title,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.purchase_url,
    },
    ...universalColumns,
  ],
  events: [
    {
      label: "Name",
      name: "name",
      align: "left",
      field: (row) => row.linkable.name,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.url,
    },
    ...universalColumns,
  ],
  finds: [
    {
      label: "Title",
      name: "name",
      align: "left",
      field: (row) => row.linkable.title,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.url,
    },
    ...universalColumns,
  ],
  "online resource links": [
    {
      label: "Name",
      name: "name",
      align: "left",
      field: (row) => row.linkable.name,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.url,
    },
    ...universalColumns,
  ],
  publications: [
    {
      label: "Title",
      name: "name",
      align: "left",
      field: (row) => row.linkable.title,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.url,
    },
    ...universalColumns,
  ],
  socials: [
    {
      label: "Icon",
      name: "icon",
      align: "center",
      field: (row) => row.linkable.icon,
    },
    {
      label: "Link",
      name: "link",
      align: "center",
      field: (row) => row.linkable.url,
    },
    ...universalColumns,
  ],
};

const tableList = computed(() => {
  const list = uniq(store.admin.brokenLinks.map(({ table_name }) => table_name))
    .map((name) => startCase(name).toLowerCase())
    .sort();

  return list;
});

const linksForTable = computed(() => {
  return store.admin.brokenLinks.filter(
    ({ table_name }) => table_name == snakeCase(tableName.value)
  );
});

const openLink = (url, row) => {
  url = row.updated_link || url;
  window.open(url, "_blank");
};

const updateRec = async (row, setting) => {
  switch (setting) {
    case "broken":
      row.confirmed_broken = 1;
      row.confirmed_working = 0;
      break;

    case "working":
      row.confirmed_broken = 0;
      row.confirmed_working = 1;
      break;

    case "unknown":
      row.confirmed_broken = 0;
      row.confirmed_working = 0;
      break;

    case "link":
      row.confirmed_broken = 0;
      row.confirmed_working = 0;
      break;
  }

  row.confirmed_date =
    !!row.confirmed_broken || !!row.confirmed_working
      ? formatISO9075(new Date(), { representation: "date" })
      : null;

  await callApi({
    path: `/admin/broken-links/${row.id}`,
    method: "put",
    payload: clone(row),
    useAuth: true,
  });
};

onMounted(() => {
  if (tableList.value.length > 0) {
    tableName.value = tableList.value[0];
  } else {
    tableName.value = null;
  }
});
</script>
