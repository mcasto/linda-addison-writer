<template>
  <div
    :class="Screen.gt.sm ? 'q-px-xl q-ml-xl q-mt-md' : ''"
    style="max-height: 300px;"
  >
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
import { Screen } from "quasar";
</script>
