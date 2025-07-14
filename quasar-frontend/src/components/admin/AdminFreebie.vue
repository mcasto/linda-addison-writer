<template>
  <q-dialog v-model="model" full-width full-height>
    <q-card>
      <q-form @submit.prevent="save">
        <q-card-section class="q-gutter-y-sm">
          <q-input
            type="text"
            label="Title"
            v-model="freebie.title"
            stack-label
            dense
            outlined
          ></q-input>

          <div class="row q-gutter-x-sm">
            <q-input
              type="text"
              label="Sub"
              v-model="freebie.sub"
              stack-label
              dense
              outlined
              class="col"
            ></q-input>

            <q-input
              type="text"
              label="Note"
              v-model="freebie.note"
              stack-label
              dense
              outlined
              class="col"
            ></q-input>
          </div>

          <q-card bordered flat>
            <q-card-section>
              <div class="text-h6">
                Contents
              </div>
              <markdown-editor
                v-model="freebie.raw"
                rows="10"
              ></markdown-editor>
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
const model = defineModel();
const props = defineProps(["freebie"]);
import callApi from "src/assets/call-api";
import MarkdownEditor from "../MarkdownEditor.vue";
import { clone } from "lodash-es";
import { startsWith } from "lodash-es";
import { Loading, Notify } from "quasar";

const closeDialog = () => {
  window.location.reload();
};

const save = async () => {
  Loading.show();

  const method = startsWith(props.freebie.id, "new-") ? "post" : "put";

  const response = await callApi({
    path: `/admin/freebies/${props.freebie.id}`,
    method,
    payload: clone(props.freebie),
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
