<template>
  <div>
    {{ store.admin.profile }}
  </div>
</template>

<script setup>
import { useStore } from "src/stores/store";

const store = useStore();
</script>
