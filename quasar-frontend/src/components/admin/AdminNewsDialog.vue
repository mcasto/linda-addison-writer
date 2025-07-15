<template>
  <q-dialog v-model="model" full-width>
    <q-card>
      <q-form @submit.prevent="save">
        <q-card-section>
          <div class="flex justify-end">
            <q-input
              type="date"
              v-model="news.date"
              outlined
              dense
              stack-label
              label="Date"
            ></q-input>
          </div>
          <markdown-editor v-model="news.raw"></markdown-editor>
        </q-card-section>
        <q-card-actions class="justify-between">
          <q-btn
            label="Cancel"
            color="warning"
            class="text-black"
            @click="closeDialog"
          ></q-btn>

          <q-btn type="submit" label="Save" color="primary"></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["news"]);

import { Loading, Notify } from "quasar";
import MarkdownEditor from "../MarkdownEditor.vue";
import { startsWith } from "lodash-es";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";

const closeDialog = () => {
  window.location.reload();
};

const save = async () => {
  Loading.show();
  const method = startsWith(props.news.id, "new-") ? "post" : "put";
  const response = await callApi({
    path: `/admin/news/${props.news.id}`,
    method,
    payload: clone(props.news),
    useAuth: true,
  });
  if (response.status == "ok") {
    window.location.reload();
    return;
  }
  console.log({ response });
  Loading.hide();
  Notify.create({
    type: "negative",
    message: "Unable to save changes.",
  });
};
</script>
