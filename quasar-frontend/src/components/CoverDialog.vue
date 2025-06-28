<template>
  <q-dialog v-model="model" full-width>
    <q-card>
      <q-toolbar class="justify-end">
        <q-btn icon="close" flat round v-close-popup></q-btn>
      </q-toolbar>
      <q-card-section>
        <div class="row">
          <div class="col-4">
            <q-img :src="cover.image_url" width="100%" height="100%"></q-img>
          </div>
          <div class="col q-pa-md">
            <div v-html="cover.contents" class="text-subtitle1"></div>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["cover"]);
</script>
