<template>
  <q-dialog v-model="model">
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="save">
          <div class="row q-gutter-y-sm">
            <div class="col-6 q-px-sm">
              <q-input
                type="text"
                label="Name"
                v-model="event.name"
                stack-label
                outlined
                dense
                required
              ></q-input>
            </div>

            <div class="col-6 q-px-sm">
              <q-input
                type="text"
                label="Schedule"
                v-model="event.schedule"
                stack-label
                outlined
                dense
                required
              ></q-input>
            </div>

            <div class="col-6 q-px-sm">
              <q-input
                type="date"
                label="Start Date"
                v-model="event.start_date"
                stack-label
                outlined
                dense
                required
              ></q-input>
            </div>

            <div class="col-6 q-px-sm">
              <q-input
                type="time"
                label="Start Time"
                v-model="event.start_time"
                stack-label
                outlined
                dense
                clearable
              ></q-input>
            </div>

            <div class="col-6 q-px-sm">
              <q-input
                type="date"
                label="End Date"
                v-model="event.end_date"
                stack-label
                outlined
                dense
              ></q-input>
            </div>

            <div class="col-6 q-px-sm">
              <q-input
                type="time"
                label="End Time"
                v-model="event.end_time"
                stack-label
                outlined
                dense
                clearable
              ></q-input>
            </div>

            <div class="col-12 q-px-sm">
              <q-select
                :options="options"
                outlined
                dense
                v-model="event.tz"
                label="Time Zone"
                stack-label
                use-input
                hide-selected
                fill-input
                @filter="tzFilter"
                clearable
              >
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-grey">
                      No results
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>

            <div class="col-12 q-px-sm">
              <q-input
                type="text"
                label="URL"
                v-model="event.url"
                stack-label
                outlined
                dense
              ></q-input>
            </div>

            <q-card bordered flat class="col-12 q-px-sm">
              <markdown-editor v-model="event.raw"></markdown-editor>
            </q-card>

            <div class="col-12 flex justify-between">
              <q-btn
                color="warning"
                class="text-black"
                label="Cancel"
                @click="closeDialog"
              ></q-btn>

              <q-btn type="submit" color="primary" label="Save"></q-btn>
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["event"]);
import { ref } from "vue";

import MarkdownEditor from "../MarkdownEditor.vue";

import tzList from "src/assets/tz-list";
import { Loading } from "quasar";
import { startsWith } from "lodash-es";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";
const options = ref(tzList);

const tzFilter = (val, update, abort) => {
  update(() => {
    const needle = val.toLowerCase();
    options.value = tzList.filter((v) => v.toLowerCase().indexOf(needle) > -1);
  });
};

const closeDialog = () => {
  window.location.reload();
};

const save = async () => {
  Loading.show({ delay: 100 });

  const method = startsWith(props.event.id, "new-") ? "post" : "put";

  const response = await callApi({
    path: `/admin/events/${props.event.id}`,
    method,
    payload: clone(props.event),
    useAuth: true,
  });

  if (response.status == "ok") {
    Loading.hide();
    window.location.reload();
    return;
  }

  Loading.hide();
};
</script>
