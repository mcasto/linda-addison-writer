<template>
  <q-toolbar class="shadow-1 q-mb-md">
    <q-toolbar-title>
      Manage Publication Types
    </q-toolbar-title>
    <q-btn icon="add" round color="primary">
      <q-popup-edit
        v-model="newType"
        auto-save
        title="New Type"
        buttons
        @save="createType"
        v-slot="scope"
      >
        <q-input
          type="text"
          v-model="scope.value"
          label="New Type"
          dense
          outlined
          autofocus
          :validate="(v) => trim(v) != '' && v !== null"
        ></q-input>
      </q-popup-edit>
    </q-btn>
  </q-toolbar>
  <q-table
    :rows="store.pubTypes"
    :pagination="{ rowsPerPage: 0 }"
    hide-bottom
    flat
  >
    <template #header>
      <q-th class="text-left">
        Name
      </q-th>
      <q-th class="text-right">
        <!--  -->
      </q-th>
    </template>
    <template #body="props">
      <q-tr>
        <q-td class="text-left cursor-pointer">
          <div>
            {{ props.row.name }}
          </div>
          <q-popup-edit
            v-model="props.row.name"
            v-slot="scope"
            auto-save
            @update:model-value="updatePubType(props.row)"
            buttons
          >
            <q-input
              type="text"
              label="Publication Type"
              v-model="scope.value"
              autofocus
              dense
              outlined
            ></q-input>
          </q-popup-edit>
        </q-td>
        <q-td class="text-right">
          <q-btn
            icon="delete"
            flat
            round
            @click="deleteType(props.row.id)"
          ></q-btn>
        </q-td>
      </q-tr>
    </template>
  </q-table>
</template>

<script setup>
import { remove } from "lodash-es";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const newType = ref(null);

const createType = async (type) => {
  const response = await callApi({
    path: "/admin/publications/create-type",
    method: "post",
    payload: { type },
    useAuth: true,
  });

  if (response) {
    store.pubTypes = response.pubTypes;
    newType.value = null;
  }
};

const updatePubType = async (row) => {
  const response = await callApi({
    path: `/admin/publications/update-type/${row.id}`,
    payload: row,
    method: "put",
    useAuth: true,
  });

  if (response) {
    Notify.create({
      type: "positive",
      message: "Publication Type successfully updated.",
    });
  }
};

const deleteType = async (id) => {
  const destroyType = async () => {
    const response = await callApi({
      path: `/admin/publications/destroy-type/${id}`,
      method: "delete",
      useAuth: true,
    });

    if (response) {
      remove(store.pubTypes, (pubType) => pubType.id == id);

      Notify.create({
        type: "positive",
        message: "Publication Type Deleted.",
      });
    }
  };

  const hasPubs =
    store.admin.publications.filter(
      ({ publication_type }) => publication_type.id == id
    ).length > 0;

  if (hasPubs) {
    Notify.create({
      type: "warning",
      timeout: 0,
      message:
        "<div class='text-h6'>Warning!</div><div class='text-subtitle1'>This type has associated publications.</div><div class='text-subtitle1'>If you delete this type, all associated publications will be deleted as well.</div><div>Are you <em><strong>SURE</strong></em> you want to do this?</div>",
      html: true,
      actions: [
        {
          label: "No",
        },
        {
          label: "Yes",
          handler: async () => {
            destroyType();
          },
        },
      ],
    });
  } else {
    destroyType();
  }
};
</script>
