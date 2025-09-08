<template>
  <div>Edit Biblio: {{ route.params.id }}</div>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { useRoute } from "vue-router";

const store = useStore();

const route = useRoute();
</script>
