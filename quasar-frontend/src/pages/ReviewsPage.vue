<template>
  <page-container>
    <q-card>
      <q-toolbar class="bg-white">
        <q-toolbar-title>
          Reviews & Quotes
        </q-toolbar-title>
        <div class="text-caption">
          {{ curItem }} of {{ store.reviews.length }}
        </div>
      </q-toolbar>
      <q-separator></q-separator>
      <q-carousel
        v-model="slide"
        transition-prev="scale"
        transition-next="scale"
        swipeable
        animated
        control-color="black"
        padding
        arrows
        class="text-white shadow-1 rounded-borders"
      >
        <q-carousel-slide
          :name="item.id"
          v-for="item of store.reviews"
          :key="`review-card-${item.id}`"
        >
          <div class="flex flex-center full-height text-black q-pa-md">
            <div v-html="item.contents"></div>
          </div>
        </q-carousel-slide>
      </q-carousel>
    </q-card>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

const store = useStore();

const slide = ref(null);

const curItem = computed(() => {
  return store.reviews.findIndex(({ id }) => id == slide.value) + 1;
});

onMounted(() => {
  slide.value = store.reviews[0].id;
});
</script>
