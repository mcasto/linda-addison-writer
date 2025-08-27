<template>
  <div>
    <q-table
      :columns="columns"
      :rows="rows"
      :pagination="{ rowsPerPage: 10 }"
      dense
    >
      <template #top>
        <div class="row full-width q-gutter-x-xs items-center q-mb-md">
          <div class="col-3">
            <q-input
              type="text"
              label="Search"
              v-model="filter"
              outlined
              dense
              clearable
              @keydown.esc="filter = null"
              class="full-width"
            >
              <template #append> <q-icon name="search"></q-icon> </template>
            </q-input>
          </div>

          <div class="col-3 q-px-md">
            <q-select
              v-model="selectedType"
              :options="store.admin.pubTypes"
              label="Publication Type"
              outlined
              dense
              option-label="name"
              clearable
            >
              <template #before>
                <q-btn
                  icon="mdi-cogs"
                  flat
                  round
                  :to="{ name: 'admin-manage-pub-types' }"
                >
                  <q-tooltip>
                    Manage Publication Types
                  </q-tooltip>
                </q-btn>
              </template>
            </q-select>
          </div>

          <div class="col-3">
            <q-select
              v-model="selectedYear"
              :options="years"
              label="Year"
              outlined
              dense
              clearable
            />
          </div>

          <div class="col text-right">
            <q-btn
              icon="mdi-new-box"
              color="primary"
              flat
              size="lg"
              @click="newPublication"
            />
          </div>
        </div>
      </template>

      <template #body-cell-tools="props">
        <q-td class="text-center">
          <q-btn
            flat
            round
            icon="mdi-pencil"
            @click="editPublication(props.row)"
            size="small"
          />
          <q-btn
            flat
            round
            icon="mdi-delete"
            @click="deletePublication(props.row)"
            size="small"
          />
        </q-td>
      </template>
      <template #body-cell-url="props">
        <q-td class="text-center">
          <q-btn flat icon="mdi-link" @click="openUrl(props.row.url)" />
        </q-td>
      </template>
    </q-table>

    <pub-form v-model="showForm"></pub-form>
  </div>
</template>

<script setup>
import { uniq, uniqBy } from "lodash-es";
import { useStore } from "src/stores/store";
import { computed, ref } from "vue";
import PubForm from "src/components/admin/PubForm.vue";
import callApi from "src/assets/call-api";
import { Notify } from "quasar";

const store = useStore();

console.log({ publications: store.admin.publications });

const filter = ref(null);
const selectedType = ref(null);
const selectedYear = ref(null);
const showForm = ref({
  visible: false,
  form: null,
});

const newPublication = () => {
  showForm.value = {
    visible: true,
    form: {
      year: new Date().getFullYear(),
      title: null,
      contents: "",
      url: null,
      publication_type: null,
    },
  };
};

const editPublication = (pub) => {
  console.log({ pub });

  showForm.value = {
    visible: true,
    form: { ...pub },
  };
};

const deletePublication = async (pub) => {
  Notify.create({
    type: "warning",
    message: `Are you sure you want to delete ${pub.title}?`,
    actions: [
      {
        label: "Cancel",
        color: "negative",
        handler: () => {
          Notify.create({
            type: "info",
            message: "Publication deletion canceled",
          });
        },
      },
      {
        label: "Delete",
        color: "positive",
        handler: async () => {
          const response = await callApi({
            path: `/admin/publications/${pub.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            Notify.create({
              type: "positive",
              message: "Publication deleted successfully",
            });

            const updated = await callApi({
              path: "/admin/publications",
              method: "get",
              useAuth: true,
            });

            store.admin.pubTypes = updated.pubTypes;
            store.admin.publications = updated.publications;
          }
        },
      },
    ],
  });
};

const columns = [
  { name: "tools", align: "center" },
  {
    label: "Publication Type",
    field: (rec) => rec.publication_type.name,
    align: "left",
  },
  {
    label: "Year",
    field: "year",
    align: "left",
  },
  {
    label: "Title",
    field: "title",
    align: "left",
  },
  { name: "url", label: "URL", field: "url", align: "center" },
];

const rows = computed(() => {
  return store.admin.publications.filter((pub) => {
    const searchVal = !filter.value
      ? true
      : pub.title.toLowerCase().includes(filter.value.toLowerCase());

    const typeVal =
      !selectedType.value || pub.publication_type.id === selectedType.value.id;

    const yearVal = !selectedYear.value || pub.year === selectedYear.value;

    return searchVal && typeVal && yearVal;
  });
});

const years = computed(() => {
  return uniq(store.admin.publications.map((pub) => pub.year)).sort();
});

const openUrl = (url) => {
  window.open(url, "_blank");
};
</script>
