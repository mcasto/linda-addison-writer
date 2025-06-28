<template>
  <q-card>
    <q-card-section>
      <div class="text-h6">
        Today's Life Poem
      </div>
      <div class="text-subtitle2 q-mt-md">
        {{ store.life_poem.contents }}
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { useStore } from "src/stores/store";

const store = useStore();
</script>
