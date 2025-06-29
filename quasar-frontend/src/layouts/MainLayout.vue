<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-header class="bg-accent">
        <navigation-handler></navigation-handler>
      </q-header>
      <q-page
        style="
          background-image: url(/images/background-xl.webp);
          background-position: top top;
          background-repeat: no-repeat;
        "
        :style="
          Screen.width < 1370
            ? 'background-size:contain'
            : 'background-size:cover'
        "
      >
        <header-contents></header-contents>
        <router-view />
      </q-page>
      <q-footer class="bg-accent">
        Footer
      </q-footer>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { Screen } from "quasar";

import HeaderContents from "src/components/HeaderContents.vue";
import NavigationHandler from "src/components/NavigationHandler.vue";
</script>
