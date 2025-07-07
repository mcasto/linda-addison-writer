<template>
  <page-container>
    <q-card>
      <q-toolbar class="bg-white">
        <q-toolbar-title v-if="Screen.gt.xs">
          Online Resources
        </q-toolbar-title>
        <div>
          <q-select
            :options="store.resourceTypes"
            option-label="header"
            v-model="section"
            outlined
            dense
            label="Resource Type"
          ></q-select>

          <div
            class="flex items-center q-mt-sm"
            v-if="store.resourceLinks.last_page > 1"
          >
            <div class="text-caption text-grey-7 q-mr-md">
              Showing {{ store.resourceLinks.from }} to
              {{ store.resourceLinks.to }} of
              {{ store.resourceLinks.total }} entries
              <span v-if="filter" class="text-italic">(filtered)</span>
            </div>

            <q-pagination
              v-model="pagination.page"
              :max="store.resourceLinks.last_page"
              :max-pages="4"
              direction-links
              boundary-links
              color="grey-8"
              active-color="primary"
              active-text-color="white"
              :class="Screen.xs ? 'q-pb-xs' : ''"
            />
          </div>
        </div>
      </q-toolbar>

      <q-separator></q-separator>

      <div>
        <q-list separator>
          <q-item v-for="resource of store.resourceLinks.data">
            <q-item-section side>
              &nbsp;
            </q-item-section>
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
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, onMounted, ref, watch } from "vue";

const store = useStore();

const section = ref(store.resourceTypes[0]);
const filter = ref(null);

const pagination = ref({
  sortBy: "sort_order",
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.pubs.total,
});

const resourcePath = computed(() => {
  let path = `/online-resources/${section.value.id}?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;
  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

watch([section, filter], async () => {
  pagination.value.page = 1;

  store.resourceLinks = await callApi({
    path: resourcePath.value,
    method: "get",
  });
});

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    store.resourceLinks = await callApi({
      path: resourcePath.value,
      method: "get",
    });
  },
  { deep: true }
);
</script>
