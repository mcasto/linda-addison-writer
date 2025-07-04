<template>
  <page-container>
    <div class="row q-gutter-y-md">
      <div
        class="col-12 col-sm-6 col-md-4 cursor-pointe"
        v-for="cover of store.covers"
        :key="`cover-${cover.id}`"
        @click="coverDialog = { show: true, cover }"
      >
        <q-img :src="cover.image_url" height="30vh" fit="contain"></q-img>
        <div class="text-subtitle2 text-center bg-white">
          {{ cover.title }}
        </div>
      </div>
    </div>
    <cover-dialog
      v-model="coverDialog.show"
      :cover="coverDialog.cover"
    ></cover-dialog>
  </page-container>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { ref } from "vue";

import PageContainer from "src/components/PageContainer.vue";
import CoverDialog from "src/components/CoverDialog.vue";

const store = useStore();

const coverDialog = ref({
  show: false,
  cover: null,
});
</script>
