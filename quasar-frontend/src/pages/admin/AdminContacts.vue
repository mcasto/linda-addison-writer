<template>
  <page-container>
    <q-table :columns="columns" :rows="store.admin.contacts">
      <template #body="props">
        <q-tr>
          <q-td>
            <q-btn
              color="primary"
              icon="add"
              @click="props.expand = !props.expand"
              size="sm"
              dense
            ></q-btn>
          </q-td>
          <q-td>
            {{ props.row.first_name }}
          </q-td>
          <q-td>
            {{ props.row.last_name }}
          </q-td>
          <q-td>
            {{ props.row.email }}
          </q-td>
          <q-td>
            {{
              formatISO9075(parseISO(props.row.created_at), {
                representation: "date",
              })
            }}
          </q-td>
          <q-td>
            <q-btn
              :icon="
                props.row.status == 'unread'
                  ? 'fa-solid fa-envelope'
                  : 'fa-solid fa-envelope-open'
              "
              flat
              round
              color="primary"
              class="cursor-pointer"
              @click="toggleRead(props.row)"
            >
              <q-tooltip class="bg-primary">
                {{ startCase(props.row.status) }}
              </q-tooltip>
            </q-btn>
          </q-td>

          <q-td>
            <q-btn
              :icon="props.row.replied == 1 ? 'mdi-reply' : 'mdi-close-outline'"
              flat
              round
              @click="toggleReplied(props.row)"
            >
              <q-tooltip class="bg-primary">
                {{ props.row.replied == 1 ? "Replied" : "Not Replied" }}
              </q-tooltip>
            </q-btn>
          </q-td>

          <q-td>
            <q-btn
              icon="delete"
              color="negative"
              class="cursor-pointer"
              flat
              round
              @click="deleteContact(props.row)"
            ></q-btn>
          </q-td>
        </q-tr>
        <q-tr v-if="props.expand">
          <q-td colspan="100%">
            <div
              class="text-wrap overflow-hidden"
              style="white-space: normal; word-break: break-word;"
              v-html="props.row.mdMessage"
            ></div>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </page-container>
</template>

<script setup>
import { formatISO9075, parseISO } from "date-fns";
import { startCase } from "lodash-es";
import { Loading, Notify } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";

const store = useStore();

const columns = [
  {
    label: "",
    name: "expand",
  },
  {
    label: "First Name",
    name: "first_name",
    field: "first_name",
    sortable: true,
    align: "left",
  },
  {
    label: "Last Name",
    name: "last_name",
    field: "last_name",
    sortable: true,
    align: "left",
  },
  {
    label: "Email",
    name: "email",
    field: "email",
    sortable: true,
    align: "left",
  },
  {
    label: "Date",
    name: "created_at",
    field: "created_at",
    sortable: true,
    align: "left",
  },
  {
    label: "Status",
    name: "status",
    field: "status",
    align: "center",
    sortable: true,
  },
  {
    label: "Replied",
    name: "replied",
    field: "replied",
    align: "center",
    sortable: true,
  },
  {
    label: "",
    name: "tools",
    align: "center",
  },
];

const updateRec = async (item) => {
  await callApi({
    path: `/admin/contact/${item.id}`,
    method: "put",
    useAuth: true,
    payload: item,
  });
};

const toggleRead = async (item) => {
  const newStatus = item.status == "read" ? "unread" : "read";
  item.status = newStatus;
  await updateRec(item);
};

const toggleReplied = async (item) => {
  item.replied = item.replied ? 0 : 1;
  await updateRec(item);
};

const deleteContact = async (item) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this contact?",
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/contact/${item.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            Loading.show();
            setTimeout(() => {
              window.location.reload();
            }, 300);
          }
        },
      },
    ],
  });
};
</script>
