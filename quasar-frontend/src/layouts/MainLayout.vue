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
          background-size: contain;
        "
      >
        <header-contents></header-contents>
        <router-view />
      </q-page>
      <q-footer class="bg-accent">
        <div class="flex justify-between items-center">
          <div>
            <q-btn
              :icon="store.design.icon"
              flat
              round
              @click="showDesignCredits = true"
            >
              <q-tooltip>
                Design Credits
              </q-tooltip>
            </q-btn>
          </div>

          <div>
            <router-link to="admin" class="text-white">
              <q-icon name="settings"></q-icon>
            </router-link>
          </div>

          <div>
            <a
              v-for="social of store.socials"
              :key="`social-${social.id}`"
              :href="social.url"
              target="_blank"
              class="text-white"
            >
              <q-btn :icon="social.icon" flat round></q-btn>
            </a>
          </div>
        </div>
      </q-footer>

      <design-credits v-model="showDesignCredits"></design-credits>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { Screen } from "quasar";

import HeaderContents from "src/components/HeaderContents.vue";
import NavigationHandler from "src/components/NavigationHandler.vue";
import DesignCredits from "src/components/DesignCredits.vue";

import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const showDesignCredits = ref(false);
</script>
