<template>
  <q-dialog v-model="model" full-width>
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="save">
          <div class="row q-gutter-y-sm">
            <div class="col-10 q-px-sm">
              <q-input
                type="text"
                label="Title"
                v-model="find.title"
                dense
                outlined
                rquired
              ></q-input>
            </div>
            <div class="col-2 q-px-sm">
              <q-select
                :options="store.findTypes"
                v-model="find.find_type"
                option-label="name"
                dense
                outlined
                stack-label
                label="Type"
                required
              ></q-select>
            </div>
            <div class="col-10 q-px-sm">
              <q-input
                type="text"
                label="URL"
                v-model="find.url"
                outlined
                dense
                stack-label
                required
              >
                <template #after>
                  <q-btn
                    icon="link"
                    @click="openLink(find.url)"
                    color="primary"
                    round
                    flat
                  ></q-btn>
                </template>
              </q-input>
            </div>
            <div class="col-2 q-px-sm">
              <q-input
                type="date"
                v-model="find.date"
                label="Date"
                outlined
                stack-label
                dense
                required
              ></q-input>
            </div>
            <div class="col-12 q-px-sm">
              <markdown-editor v-model="find.raw"></markdown-editor>
            </div>
          </div>

          <div class="flex justify-between">
            <q-btn
              color="warning"
              class="text-black"
              label="Cancel"
              @click="closeDialog"
            ></q-btn>

            <q-btn type="submit" color="primary" label="Save"></q-btn>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useStore } from "src/stores/store";
import MarkdownEditor from "../MarkdownEditor.vue";
import callApi from "src/assets/call-api";
import { Loading } from "quasar";
import { startsWith } from "lodash-es";

const model = defineModel();
const props = defineProps(["find"]);

const store = useStore();

const closeDialog = () => {
  window.location.reload();
};

const openLink = (url) => {
  window.open(url);
};

const save = async () => {
  Loading.show();

  const method = startsWith(props.find.id, "new-") ? "post" : "put";

  const response = await callApi({
    path: `/admin/finds/${props.find.id}`,
    method,
    payload: props.find,
    useAuth: true,
  });

  if (response.status == "ok") {
    Loading.hide();
    window.location.reload();
    return;
  }
};
</script>
