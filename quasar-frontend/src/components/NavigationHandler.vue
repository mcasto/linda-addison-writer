<template>
  <div v-if="Screen.gt.sm">
    <q-toolbar>
      <q-tabs align="left">
        <template v-for="item of navList" :key="item.id">
          <q-route-tab :to="item.path" :label="item.label" exact></q-route-tab>
        </template>
      </q-tabs>

      <q-space></q-space>

      <q-btn label="More" :outline="moreActive">
        <q-menu square dark>
          <q-list dark separator>
            <template v-for="item of moreList" :key="item.id">
              <q-item
                clickable
                :to="item.path"
                class="text-white text-uppercase"
              >
                <q-item-section>
                  <q-item-label>
                    {{ item.label }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-list>
        </q-menu>
      </q-btn>
    </q-toolbar>
  </div>

  <div v-else>
    <q-toolbar>
      <q-btn icon="menu" @click="drawer = true"></q-btn>
      <q-toolbar-title>
        {{ pageName }}
      </q-toolbar-title>
    </q-toolbar>
    <q-drawer v-model="drawer" dark>
      <q-list dark separator dense>
        <template v-for="item in navList" :key="item.id">
          <q-item clickable class="text-uppercase">
            <q-item-section>
              <q-item-label>
                {{ item.label }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </q-list>

      <q-separator class="q-my-xl"></q-separator>

      <q-list dark separator dense>
        <q-item>
          <q-item-section>
            <q-item-label>
              MORE
            </q-item-label>
          </q-item-section>
        </q-item>
        <template v-for="item of moreList" :key="item.id">
          <q-item clickable class="text-uppercase">
            <q-item-section side>
              &nbsp;
            </q-item-section>
            <q-item-section>
              <q-item-label>
                {{ item.label }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </q-list>
    </q-drawer>
  </div>
</template>

<script setup>
import { Screen, uid } from "quasar";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

const navList = computed(() => {
  return store.router
    .getRoutes()
    .filter(({ meta }) => !meta.more && meta.visible)
    .map((route) => {
      return {
        id: uid(),
        path: route.path,
        label: route.name,
      };
    });
});

const moreList = computed(() => {
  return store.router
    .getRoutes()
    .filter(({ meta }) => meta.more && meta.visible)
    .map((route) => {
      return {
        id: uid(),
        path: route.path,
        label: route.name,
      };
    });
});

const moreActive = ref(null);

const drawer = ref(false);

const pageName = computed(() => {
  return store.router.currentRoute.value.name;
});

watch(store.router.currentRoute, () => {
  moreActive.value = moreList.value
    .map(({ path }) => path)
    .includes(store.router.currentRoute.value.path);
});

onMounted(() => {
  moreActive.value = moreList.value
    .map(({ path }) => path)
    .includes(store.router.currentRoute.value.path);
});
</script>
