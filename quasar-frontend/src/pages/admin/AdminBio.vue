<template>
  <page-container>
    <q-toolbar>
      <q-toolbar-title>
        Bio
      </q-toolbar-title>
    </q-toolbar>
    <q-separator></q-separator>
    <q-tabs v-model="tab">
      <q-tab name="shortest">
        Shortest
      </q-tab>
      <q-tab name="short">
        Short
      </q-tab>
      <q-tab name="longer">
        Longer
      </q-tab>
    </q-tabs>
    <q-separator></q-separator>

    <q-tab-panels v-model="tab">
      <q-tab-panel v-for="key in tabKeys" :key="key" :name="key">
        <q-editor
          v-model="contents[key]"
          :toolbar="toolbar"
          min-height="20rem"
        ></q-editor>
        <div class="flex justify-end q-mt-md">
          <q-btn
            label="Save"
            color="primary"
            @click="save(key)"
          ></q-btn>
        </div>
      </q-tab-panel>
    </q-tab-panels>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref, reactive } from "vue";
import { Loading, Notify } from "quasar";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";

const store = useStore();

const tab = ref("shortest");
const tabKeys = ["shortest", "short", "longer"];

const contents = reactive(clone(store.admin.bio));

const toolbar = [
  ["left", "center", "right", "justify"],
  ["bold", "italic", "underline", "strike"],
  ["unordered"],
  ["link"],
  ["undo", "redo"],
];

const save = async (key) => {
  Loading.show();

  const response = await callApi({
    path: `/admin/bio/${key}`,
    method: "put",
    payload: { contents: contents[key] },
    useAuth: true,
  });

  Loading.hide();

  if (response.status == "ok") {
    store.admin.bio[key] = contents[key];
    Notify.create({ type: "positive", message: "Bio saved." });
    return;
  }

  Notify.create({ type: "negative", message: "Unable to save changes." });
};
</script>
