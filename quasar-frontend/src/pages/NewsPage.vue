<template>
  <page-container>
    <q-card>
      <q-toolbar class="bg-white shadow-1 q-mb-xs">
        <q-toolbar-title>
          Latest News
        </q-toolbar-title>
      </q-toolbar>
      <q-separator></q-separator>
      <q-carousel
        v-model="slide"
        transition-prev="slide-right"
        transition-next="slide-left"
        swipeable
        animated
        control-color="black"
        navigation
        padding
        arrows
        height="300px"
        class="shadow-1"
      >
        <template v-for="slide of slides" :key="`news-slide-${slide.id}`">
          <q-carousel-slide :name="slide.id">
            <q-card flat>
              <q-card-section>
                <div class="text-h6 q-mb-md">
                  {{ slide.date }}
                </div>
                <div v-html="slide.contents"></div>
              </q-card-section>
            </q-card>
          </q-carousel-slide>
        </template>
      </q-carousel>
    </q-card>
  </page-container>
</template>

<script setup>
import { format, parseISO } from "date-fns";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { onMounted, ref } from "vue";

const store = useStore();

const slides = store.latest_news.map(({ id, date, contents }) => {
  return {
    id,
    date: format(parseISO(date), "PP"),
    contents,
  };
});

const slide = ref(null);

onMounted(() => {
  slide.value = slides[0].id;
});
</script>
