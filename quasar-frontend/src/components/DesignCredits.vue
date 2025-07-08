<template>
  <q-dialog v-model="model">
    <q-card>
      <q-card-section>
        <div class="text-h6">
          Design Credits
        </div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section>
        <div v-html="store.design.contents"></div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useStore } from "src/stores/store";

const model = defineModel();

const store = useStore();
</script>
