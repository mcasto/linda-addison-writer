<template>
  <div
    class="flex flex-center column no-wrap"
    :style="`height: ${headerHeight}; width: 100%`"
  >
    <div
      class="q-px-md q-uploader__overlay text-bold text-center q-mb-sm"
      :class="Screen.gt.xs ? 'text-h4' : 'text-h6'"
    >
      Linda D. Addison <span v-if="Screen.gt.xs">&ndash;</span> <br v-else />
      Writer / Poet
    </div>

    <life-poem
      v-if="Screen.gt.xs"
      style="width: 100%; max-width: 600px; margin: 0 auto;"
    ></life-poem>
  </div>
</template>

<script setup>
import { Screen } from "quasar";
import LifePoem from "./LifePoem.vue";
import { computed } from "vue";

const headerHeight = computed(() => {
  const heights = [
    {
      min: 0,
      max: 344,
      height: "12vh",
    },
    {
      min: 345,
      max: 539,
      height: "15vh",
    },
    {
      min: 540,
      max: 700,
      height: "20vh",
    },
    {
      min: 701,
      max: 1090,
      height: "30vh",
    },
    {
      min: 1091,
      max: 1150,
      height: "35vh",
    },
    {
      min: 1151,
      max: 1474,
      height: "40vh",
    },
    {
      min: 1475,
      max: 99999,
      height: "42vh",
    },
  ];

  return heights.find(
    ({ min, max }) => min <= Screen.width && Screen.width <= max
  ).height;
});
</script>
