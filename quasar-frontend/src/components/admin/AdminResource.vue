<template>
  <q-dialog v-model="model">
    <q-card>
      <q-card-section>
        <pre>
          {{ resource }}
        </pre>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["resource"]);
</script>
