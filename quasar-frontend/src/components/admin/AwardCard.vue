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
    <q-card-actions class="justify-end">
      <q-btn label="Update" @click="update" color="primary"></q-btn>
    </q-card-actions>
  </q-card>
</template>

<script setup>
import callApi from "src/assets/call-api";
import MarkdownEditor from "../MarkdownEditor.vue";
import { Notify } from "quasar";

const props = defineProps(["award"]);

const update = async () => {
  const response = await callApi({
    path: `/admin/awards/${props.award.id}`,
    method: "put",
    payload: props.award,
    useAuth: true,
  });

  if (response.status == "ok") {
    Notify.create({
      type: "positive",
      message: "Award Updated",
    });
  }
};
</script>
