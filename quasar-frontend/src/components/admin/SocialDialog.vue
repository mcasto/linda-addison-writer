<template>
  <q-dialog v-model="model.visible" persistent>
    <q-card style="width: 100vw;">
      <q-form @submit.prevent="updateSocial">
        <q-card-section>
          <q-input
            type="text"
            label="Icon"
            v-model="model.social.icon"
            style="width: 100%;"
            debounce="300"
          >
            <template #after>
              <q-icon :name="model.social.icon"></q-icon>
            </template>
          </q-input>
          <q-input
            type="text"
            label="Link"
            v-model="model.social.url"
            style="width: 100%;"
          >
            <template #after>
              <q-btn icon="link" @click="openWindow" round flat></q-btn>
            </template>
          </q-input>
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn
            type="button"
            label="Cancel"
            color="negative"
            @click="model.visible = false"
          ></q-btn>
          <q-btn type="submit" label="Continue" color="primary"></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";

const store = useStore();

const model = defineModel();

const updateSocial = async () => {
  const basePath = "/admin/socials";
  const path = model.value.social.id
    ? `${basePath}/${model.value.social.id}`
    : basePath;
  const method = model.value.social.id ? "put" : "post";

  const response = await callApi({
    path,
    method,
    payload: model.value.social,
    useAuth: true,
  });

  if (response.social) {
    store.admin.socials.push(response.social);
    model.value.visible = false;
  }

  if (response.existing) {
    model.value.visible = false;
  }
};

const openWindow = () => {
  window.open(model.value.social.url);
};
</script>
