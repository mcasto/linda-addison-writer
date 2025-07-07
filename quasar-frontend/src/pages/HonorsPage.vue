<template>
  <page-container>
    <q-card>
      <q-toolbar class="bg-white">
        <q-toolbar-title>
          Honors
        </q-toolbar-title>
        <div class="text-caption">
          {{ curItem }} of {{ store.honors.length }}
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
          v-for="item of store.honors"
          :key="`review-card-${item.id}`"
        >
          <q-card class="flex flex-center text-black full-height" flat>
            <q-card-section>
              <div v-html="item.contents"></div>
            </q-card-section>
          </q-card>
          <div class="absolute-top-left q-ml-lg q-mt-lg text-black">
            Year: {{ item.year }}
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

const slide = ref(store.honors[0].id);

const curItem = computed(() => {
  return store.honors.findIndex(({ id }) => id == slide.value) + 1;
});
</script>
