<template>
  <q-card>
    <q-card-section>
      <markdown-editor v-model="award.raw"></markdown-editor>
    </q-card-section>
    <q-card-section>
      <div class="flex justify-end">
        <q-input
          type="number"
          v-model="award.year"
          label="Year"
          stack-label
          outlined
          style="width: 5rem;"
          dense
        ></q-input>
      </div>
    </q-card-section>
    <q-card-actions class="justify-between">
      <q-btn icon="delete" flat color="negative" round @click="destroy"></q-btn>
      <q-btn :label="buttonLabel" @click="handleClick" color="primary"></q-btn>
    </q-card-actions>
  </q-card>
</template>

<script setup>
import callApi from "src/assets/call-api";
import MarkdownEditor from "../MarkdownEditor.vue";
import { Notify } from "quasar";
import { computed } from "vue";
import { useStore } from "src/stores/store";
const store = useStore();

const props = defineProps(["award"]);

const handleClick = async () => {
  let response;

  if (props.award.id) {
    response = await callApi({
      path: `/admin/awards/${props.award.id}`,
      method: "put",
      payload: props.award,
      useAuth: true,
    });
  } else {
    response = await callApi({
      path: "/admin/awards",
      method: "post",
      payload: props.award,
      useAuth: true,
    });

    window.location.reload();
  }

  if (response.status == "ok") {
    Notify.create({
      type: "positive",
      message: `Award ${buttonLabel.value}d`,
    });
  }
};

const buttonLabel = computed(() => {
  return props.award.id ? "Update" : "Create";
});

const destroy = async () => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this award?",
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/awards/${props.award.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.status == "ok") {
            window.location.reload();
          }
        },
      },
    ],
  });
};
</script>
