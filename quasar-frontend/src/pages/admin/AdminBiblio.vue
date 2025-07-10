<template>
  <page-container>
    {{ type }}
    <q-separator></q-separator>
    <pre>
    {{ store.admin.biblio }}
    </pre>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, ref } from "vue";

const store = useStore();

const type = ref(store.admin.biblioTypes[0]);
</script>
