<template>
  <q-card>
    <q-card-section>
      <div class="text-h6">
        Today's Life Poem
      </div>
      <div
        v-html="store.life_poem.contents"
        class="text-subtitle2 q-mt-md"
      ></div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { useStore } from "src/stores/store";

const store = useStore();
</script>
