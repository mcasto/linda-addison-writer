<template>
  <page-container>
    <q-card>
      <q-toolbar>
        <q-toolbar-title v-if="Screen.gt.xs">
          Bibliography
        </q-toolbar-title>
        <q-select
          label="Type"
          :options="types"
          option-label="type"
          v-model="type"
          outlined
          dense
          style="width: 10rem;"
        ></q-select>
      </q-toolbar>

      <q-separator></q-separator>

      <q-list>
        <q-item v-for="item of list" :key="`biblio-item-${item.id}`">
          <q-item-section>
            <q-item-label>
              {{ item.title }}
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-card>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

const store = useStore();

const type = ref(null);

const types = computed(() => {
  if (!store.biblio) {
    return [];
  }

  return store.biblio.map(({ id, type }) => ({
    id,
    type: type.toUpperCase(),
  }));
});

const list = computed(() => {
  if (!type.value) {
    return [];
  }

  const items = store.biblio.find(({ id }) => id == type.value.id).biblios;

  return items;
});

onMounted(() => {
  const first = store.biblio[0];
  first.type = first.type.toUpperCase();
  type.value = first;
});
</script>
