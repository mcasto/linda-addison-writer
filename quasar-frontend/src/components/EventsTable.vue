<template>
  <div class="q-pb-xl">
    <q-table
      :title="title"
      :rows="events"
      :pagination="{ rowsPerPage: 0 }"
      class="q-ma-sm"
      hide-bottom
      :columns="columns"
    >
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td auto-width>
            <q-btn
              color="primary"
              dense
              @click="props.expand = !props.expand"
              :icon="props.expand ? 'remove' : 'add'"
              size="sm"
            />
          </q-td>
          <q-td key="name" :props="props">
            <a
              :href="props.row.url"
              target="_blank"
              class="text-primary text-subtitle1"
            >
              {{ props.row.name }}
            </a>
          </q-td>
          <q-td key="schedule" :props="props" class="text-subtitle1">
            {{ props.row.schedule }}
          </q-td>
        </q-tr>
        <q-tr v-show="props.expand" :props="props">
          <q-td colspan="100%">
            <div class="expanded-content">
              <div v-html="props.row.contents"></div>
            </div>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script setup>
const props = defineProps(["title", "events"]);

const columns = [
  {
    name: "expand",
    label: "",
    field: "",
    align: "left",
  },
  {
    name: "name",
    required: true,
    label: "Name",
    align: "left",
    field: "name",
    sortable: true,
  },
  {
    name: "schedule",
    align: "center",
    label: "Schedule",
    field: "schedule",
    sortable: true,
  },
];
</script>
