<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-header class="bg-accent">
        <q-toolbar>
          <q-toolbar-title>
            Admin
          </q-toolbar-title>
          <q-btn icon="logout" flat round @click="logout"></q-btn>
        </q-toolbar>
      </q-header>
      <q-page>
        <router-view />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";

const store = useStore();

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
