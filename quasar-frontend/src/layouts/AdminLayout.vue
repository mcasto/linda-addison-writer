<template>
  <q-layout view="hHh Lpr lFf">
    <q-page-container>
      <q-header class="bg-accent">
        <q-toolbar>
          <q-toolbar-title> Admin&mdash;{{ pageName }} </q-toolbar-title>
          <q-btn icon="home" flat round to="/"></q-btn>
          <q-btn icon="logout" flat round @click="logout"></q-btn>
        </q-toolbar>
      </q-header>

      <q-drawer persistent :model-value="true" bordered>
        <q-list separator class="q-mb-xl">
          <q-item
            v-for="tab of tabs"
            :key="`tab-${tab.name}`"
            clickable
            active-class="text-black bg-light-blue-2"
            :active="isActive(tab)"
            :to="{ name: tab.name }"
          >
            <q-item-section side>
              <q-icon :name="tab.meta.icon" color="black" size="xs"></q-icon>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                {{ tab.meta.tip }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-drawer>

      <q-page>
        <router-view />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { computed, ref } from "vue";

const store = useStore();

const tab = ref("admin-dashboard");

const tabs = computed(() => {
  return store.router
    .getRoutes()
    .filter(({ path }) => path.includes("/admin"))
    .filter(({ name }) => !["admin-section", "admin-login"].includes(name))
    .sort((a, b) => (a.meta.order > b.meta.order ? 1 : -1));
});

const pageName = computed(() => {
  return store.router.currentRoute.value.meta.tip;
});

const logout = async () => {
  const response = await callApi({
    path: "/admin/logout",
    method: "post",
    useAuth: true,
  });

  store.token = null;
  store.router.push("/admin/login");
};

const isActive = (tab) => {
  return store.router.currentRoute.value.name == tab.name;
};
</script>
