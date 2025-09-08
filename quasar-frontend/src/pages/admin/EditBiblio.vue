<template>
  <div class="q-ma-md">
    <q-form @submit.prevent="saveEntry">
      <q-card>
        <q-card-section>
          <div class="row q-gutter-y-md">
            <q-select
              :options="store.admin.biblioTypes"
              option-label="type"
              option-value="id"
              v-model="bib.biblio.biblio_type_id"
              emit-value
              map-options
              label="Type"
              class="col-1"
              dense
              outlined
            ></q-select>

            <q-input
              type="text"
              label="Title"
              v-model="bib.biblio.title"
              class="col-10 offset-1"
              dense
              outlined
            ></q-input>

            <q-date
              v-model="bib.biblioPub.pub_date"
              mask="YYYY-MM-DD"
              subtitle="Publication Date"
              title=" "
              landscape
              color="accent"
              class="col-5"
            ></q-date>

            <div class="col-5 offset-1 column">
              <q-input
                type="text"
                label="Publication"
                v-model="bib.biblioPub.publication"
                dense
                outlined
                class="q-mb-md"
              ></q-input>

              <q-date
                v-model="bib.biblio.sort_date"
                mask="YYYY-MM-DD"
                subtitle="Sort Date"
                title=" "
                landscape
                color="accent"
              ></q-date>
            </div>

            <q-input
              type="text"
              label="Display Date"
              v-model="bib.biblioPub.display_date"
              class="col-4"
              dense
              outlined
            ></q-input>
          </div>
        </q-card-section>
        <q-card-actions class="justify-center">
          <q-btn
            color="negative"
            label="Cancel"
            to="/admin/import-biblio"
          ></q-btn>
          <q-btn color="accent" label="Update" type="submit"></q-btn>
        </q-card-actions>
      </q-card>
    </q-form>
  </div>
</template>

<script setup>
import { cloneDeep } from "lodash-es";
import { useStore } from "src/stores/store";
import { ref } from "vue";
import { useRoute } from "vue-router";

const store = useStore();

const route = useRoute();

const bib = ref(
  cloneDeep(store.admin.biblioImported.find(({ id }) => (id = route.params.id)))
);

const saveEntry = () => {
  store.admin.biblioImported = store.admin.biblioImported.map((entry) => {
    return entry.id == bib.value.id ? bib.value : entry;
  });

  store.router.push("/admin/import-biblio");
};
</script>
