<template>
  <q-dialog v-model="model" full-width full-height>
    <q-card>
      <q-form @submit.prevent="save">
        <q-card-section class="q-gutter-y-sm">
          <div class="flex justify-between">
            <q-input
              type="number"
              label="Year"
              v-model="honor.year"
              stack-label
              dense
              outlined
            ></q-input>

            <q-input
              type="number"
              label="Number Won"
              v-model="honor.num"
              stack-label
              dense
              outlined
            ></q-input>
          </div>

          <q-card bordered flat>
            <q-card-section>
              <div class="text-h6">
                Contents
              </div>
              <markdown-editor v-model="honor.raw" rows="10"></markdown-editor>
            </q-card-section>
          </q-card>
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
import callApi from "src/assets/call-api";
import MarkdownEditor from "../MarkdownEditor.vue";
import { clone } from "lodash-es";
import { startsWith } from "lodash-es";
import { Loading, Notify } from "quasar";

const model = defineModel();
const props = defineProps(["honor"]);

const closeDialog = () => {
  window.location.reload();
};

const save = async () => {
  Loading.show();
  const method = startsWith(props.honor.id, "new-") ? "post" : "put";
  const response = await callApi({
    path: `/admin/honors/${props.honor.id}`,
    method,
    payload: clone(props.honor),
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
