<template>
  <div>
    <q-form @submit.prevent="saveEntry">
      <!--
        display date
      -->
      <q-select
        :options="store.admin.biblioTypes"
        option-label="type"
        option-value="id"
        v-model="bib.biblio.biblio_type_id"
        emit-value
        map-options
        label="Type"
      ></q-select>

      <q-input type="text" label="Title" v-model="bib.biblio.title"></q-input>

      <q-date
        v-model="bib.biblio.sort_date"
        mask="YYYY-MM-DD"
        subtitle="Sort Date"
        title=" "
        landscape
      ></q-date>

      <q-input
        type="text"
        label="Publication"
        v-model="bib.biblioPub.publication"
      ></q-input>

      <q-date
        v-model="bib.biblioPub.pub_date"
        mask="YYYY-MM-DD"
        subtitle="Publication Date"
        title=" "
        landscape
      ></q-date>

      <q-input
        type="text"
        label="Display Date"
        v-model="bib.biblioPub.display_date"
      ></q-input>
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

console.log({ bib: bib.value });
console.log(store.admin.biblioTypes);

const saveEntry = () => {
  //
};
</script>
