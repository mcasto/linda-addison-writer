<template>
  <page-container>
    <q-table
      :rows="store.admin.events.data"
      :columns="columns"
      row-key="id"
      :pagination="pagination"
      :loading="loading"
      hide-pagination
      dense
    >
      <template #top>
        <div
          class="flex justify-between items-center full-width q-mt-md q-px-sm"
        >
          <q-btn
            round
            color="primary"
            icon="add"
            @click="editDialog = { visible: true, event: newEventTemplate() }"
          ></q-btn>
          <div class="text-caption text-grey-7">
            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} entries
          </div>
          <div
            class="row items-center q-gutter-x-sm"
            :class="Screen.xs ? 'justify-center column' : ''"
          >
            <q-select
              dense
              outlined
              v-model="pagination.rowsPerPage"
              :options="[5, 10, 15, 20, 25, 50]"
              style="min-width: 100px;"
              class="per-page-select q-pt-md"
              @update:model-value="pagination.page = 1"
              hint="Rows Per Page"
            />
            <q-separator v-if="Screen.xs" class="q-my-sm"></q-separator>
            <q-pagination
              v-model="pagination.page"
              :max="meta.last_page"
              :max-pages="4"
              direction-links
              boundary-links
              color="grey-8"
              active-color="primary"
              active-text-color="white"
              :class="Screen.xs ? 'q-pb-xs' : ''"
            />
          </div>
        </div>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-right">
          <q-btn
            icon="delete"
            flat
            round
            color="negative"
            @click="deleteEvent(props.row)"
          ></q-btn>
          <q-btn
            icon="edit"
            flat
            round
            color="primary"
            @click="editDialog = { visible: true, event: props.row }"
          ></q-btn>
        </q-td>
      </template>
    </q-table>

    <admin-event
      v-model="editDialog.visible"
      :event="editDialog.event"
    ></admin-event>
  </page-container>
</template>

<script setup>
import { formatISO9075 } from "date-fns";
import { Notify, Screen, uid } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import AdminEvent from "src/components/admin/AdminEvent.vue";
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";

const store = useStore();

const loading = ref(false);

const editDialog = ref({
  visible: false,
  event: null,
});

const meta = computed(() => {
  const { from, to, total, last_page } = store.admin.events;
  return { from, to, total, last_page };
});

const pagination = ref({
  sortBy: "start_date",
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.pubs.total,
});

const newEventTemplate = () => {
  const today = formatISO9075(new Date(), { representation: "date" });
  return {
    id: `new-${uid()}`,
    end_date: today,
    end_time: null,
    name: "New Event",
    raw: "",
    schedule: "",
    start_date: today,
    start_time: null,
    tz: null,
    url: null,
  };
};

const deleteEvent = async (event) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this event?",
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/events/${event.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            window.location.reload();
          }
        },
      },
    ],
  });
};

const columns = [
  {
    name: "name",
    required: true,
    label: "Name",
    align: "left",
    field: "name",
    sortable: true,
  },
  {
    name: "start_date",
    align: "left",
    label: "Start Date",
    field: "start_date",
    sortable: true,
  },
  {
    name: "end_date",
    align: "left",
    label: "End Date",
    field: "end_date",
    sortable: true,
  },
  {
    name: "tools",
  },
];

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    const path = `/admin/events?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;

    const response = await callApi({
      path,
      method: "get",
      useAuth: true,
    });

    store.admin.events = response;
  },
  { deep: true }
);
</script>
