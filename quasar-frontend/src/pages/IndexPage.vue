<template>
  <div class="q-px-xl q-ml-xl q-mt-md">
    <div class="row q-gutter-y-md">
      <div
        class="col-12 col-md-4 cursor-pointer"
        v-for="cover of store.covers"
        :key="`cover-${cover.id}`"
        @click="coverDialog = { show: true, cover }"
      >
        <q-img :src="cover.image_url" height="30vh" fit="contain"></q-img>
        <div class="text-subtitle2 text-center">
          {{ cover.title }}
        </div>
      </div>
    </div>
    <cover-dialog
      v-model="coverDialog.show"
      :cover="coverDialog.cover"
    ></cover-dialog>
  </div>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { ref } from "vue";

import CoverDialog from "src/components/CoverDialog.vue";

const store = useStore();

const coverDialog = ref({
  show: false,
  cover: null,
});
</script>
