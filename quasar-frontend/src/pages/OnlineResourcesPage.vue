<template>
  <page-container>
    <q-card>
      <q-toolbar class="bg-white">
        <q-toolbar-title v-if="Screen.gt.xs">
          Online Resources
        </q-toolbar-title>
        <q-select
          :options="store.online_resources"
          option-label="header"
          v-model="section"
          outlined
          dense
          label="Resource Type"
        ></q-select>
      </q-toolbar>

      <q-separator></q-separator>

      <div>
        <q-list separator>
          <q-item v-for="resource of resources">
            <q-item-section>
              <q-item-label>
                <a :href="resource.url" target="_blank">
                  {{ resource.name }}
                </a>
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </q-card>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref } from "vue";

const store = useStore();

const section = ref(null);

const resources = computed(() => {
  if (!section.value) {
    return [];
  }

  const selected = store.online_resources.find(
    ({ id }) => id == section.value?.id
  );

  if (!selected) {
    return [];
  }

  return selected.online_resource_links.map(({ name, url }) => ({ name, url }));
});

onMounted(() => {
  section.value = store.online_resources[0];
});
</script>
