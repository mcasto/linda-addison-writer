<template>
  <q-dialog v-model="model">
    <q-card>
      <q-form @submit.prevent="submitResource">
        <q-card-section class="column q-gutter-y-sm">
          <q-select
            :options="store.resourceTypes"
            option-label="header"
            v-model="resource.online_resource"
            dense
            outlined
            label="Category"
          ></q-select>
          <q-input
            type="text"
            label="Name"
            v-model="resource.name"
            dense
            outlined
            required
          ></q-input>
          <q-input
            type="text"
            label="URL"
            v-model="resource.url"
            dense
            outlined
            required
          >
            <template #append>
              <q-btn icon="link" flat round @click="openURL"></q-btn>
            </template>
          </q-input>
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn color="negative" label="Cancel" @click="model = false"></q-btn>
          <q-btn type="submit" color="primary" label="Submit"></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { startsWith } from "lodash-es";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const model = defineModel();
const props = defineProps(["resource"]);
const store = useStore();

const submitResource = async () => {
  if (startsWith(props.resource.id, "new-")) {
    const response = await callApi({
      path: "/admin/online-resources",
      method: "post",
      payload: props.resource,
      useAuth: true,
    });

    if (response.status == "ok") {
      window.location.reload();
    }

    return;
  }

  const response = await callApi({
    path: `/admin/online-resources/${props.resource.id}`,
    method: "put",
    payload: props.resource,
    useAuth: true,
  });

  if (response.status == "ok") {
    window.location.reload();
  }
};

const openURL = () => {
  window.open(props.resource.url, "_blank");
};
</script>
