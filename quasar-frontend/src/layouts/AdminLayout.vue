<template>
  <q-layout view="hHh Lpr lFf">
    <q-page-container>
      <q-header class="bg-accent">
        <q-toolbar>
          <q-toolbar-title>
            Admin
          </q-toolbar-title>
          <q-btn icon="logout" flat round @click="logout"></q-btn>
        </q-toolbar>
      </q-header>

      <q-drawer persistent :model-value="true" mini bordered>
        <q-tabs vertical>
          <template v-for="tab of tabs" :key="`tab-${tab.name}`">
            <q-route-tab
              :icon="tab.meta.icon"
              :name="tab.name"
              :to="{ name: tab.name }"
            >
              <q-tooltip anchor="center right" self="center left">
                {{ tab.meta.tip }}
              </q-tooltip>
            </q-route-tab>
          </template>
        </q-tabs>
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

const logout = async () => {
  const response = await callApi({
    path: "/admin/logout",
    method: "post",
    useAuth: true,
  });

  store.token = null;
  store.router.push("/admin/login");
};
</script>
