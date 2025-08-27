<template>
  <q-dialog v-model="model.visible" persistent full-width>
    <q-card>
      <q-form @submit.prevent="submitForm">
        <q-card-section>
          <q-input
            v-model="model.form.year"
            label="Year"
            type="number"
            required
            dense
            outlined
            class="q-mb-md"
          />
          <q-input
            v-model="model.form.title"
            label="Title"
            required
            dense
            outlined
            class="q-mb-md"
          />
          <q-select
            v-model="model.form.publication_type"
            :options="store.admin.pubTypes"
            label="Publication Type"
            required
            dense
            outlined
            class="q-mb-md"
            option-label="name"
          >
            <template #after>
              <q-btn label="New Type" color="primary">
                <q-popup-edit
                  v-model="newType"
                  auto-save
                  title="New Type"
                  buttons
                  @save="createType"
                  v-slot="scope"
                >
                  <q-input
                    type="text"
                    v-model="scope.value"
                    label="New Type"
                    dense
                    outlined
                    autofocus
                    :validate="(v) => trim(v) != '' && v !== null"
                  ></q-input>
                </q-popup-edit>
              </q-btn>
            </template>
          </q-select>

          <q-input
            v-model="model.form.url"
            label="URL"
            required
            dense
            outlined
            class="q-mb-md"
          />

          <markdown-editor v-model="model.form.contents" />
        </q-card-section>

        <q-card-actions class="justify-end">
          <q-btn
            type="button"
            label="Cancel"
            @click="model.visible = false"
            color="negative"
          />
          <q-btn type="submit" label="Submit" color="positive" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useStore } from "src/stores/store";
import MarkdownEditor from "../MarkdownEditor.vue";
import callApi from "src/assets/call-api";
import { Notify, uid } from "quasar";
import { ref } from "vue";
import { trim } from "lodash-es";
import { sortBy } from "lodash-es";

const model = defineModel();

const store = useStore();

const newType = ref(null);

const createType = async (type) => {
  if (!model.value.form.id) {
    const newTypeObj = { name: type, id: uid() };
    store.pubTypes.push(newTypeObj);
    store.pubTypes = sortBy(store.pubTypes, "name");

    model.value.form.publication_type = newTypeObj;

    return;
  }

  const response = await callApi({
    path: "/admin/publications/create-type",
    method: "post",
    payload: { pub: model.value.form, type },
    useAuth: true,
  });

  if (response) {
    store.pubTypes = response.pubTypes;
    model.value.form = response.pub;
  }

  newType.value = null;
};

const submitForm = async () => {
  // format for submission
  const sub = {
    year: model.value.form.year,
    title: model.value.form.title,
    publication_type_id: model.value.form.publication_type.id,
    publication_type: model.value.form.publication_type,
    url: model.value.form.url,
    md: model.value.form.contents,
  };

  let response;
  if (model.value.form.id) {
    sub.id = model.value.form.id;

    response = await callApi({
      path: `/admin/publications/${sub.id}`,
      method: "put",
      payload: sub,
      useAuth: true,
    });

    if (response.status == "ok") {
      Notify.create({
        type: "positive",
        message: "Publication updated successfully",
      });

      const updated = await callApi({
        path: "/admin/publications",
        method: "get",
        useAuth: true,
      });

      store.admin.pubTypes = updated.pubTypes;
      store.admin.publications = updated.publications;

      model.value.visible = false;
    }
  } else {
    response = await callApi({
      path: "/admin/publications",
      method: "post",
      payload: sub,
      useAuth: true,
    });

    if (response.status == "ok") {
      Notify.create({
        type: "positive",
        message: "Publication created successfully",
      });

      const updated = await callApi({
        path: "/admin/publications",
        method: "get",
        useAuth: true,
      });

      store.admin.pubTypes = updated.pubTypes;
      store.admin.publications = updated.publications;

      model.value.visible = false;
    }
  }

  console.log("Form submitted:", response);
};
</script>
