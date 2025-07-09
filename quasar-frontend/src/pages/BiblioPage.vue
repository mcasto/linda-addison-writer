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
      <div class="flex justify-between items-center full-width q-mb-md q-px-sm">
        <div class="text-caption text-grey-7">
          Showing {{ store.biblio.from }} to {{ store.biblio.to }} of
          {{ store.biblio.total }} entries
          <span v-if="filter" class="text-italic">(filtered)</span>
        </div>
        <div
          class="row items-center q-gutter-x-sm"
          :class="Screen.xs ? 'justify-center column' : ''"
        >
          <q-select
            dense
            outlined
            v-model="pagination.rowsPerPage"
            :options="[5, 10, 15, 20, 25, 50]"
            style="min-width: 100px;"
            class="per-page-select q-pt-md"
            @update:model-value="pagination.page = 1"
            hint="Rows Per Page"
          />
          <q-separator v-if="Screen.xs" class="q-my-sm"></q-separator>
          <q-pagination
            v-model="pagination.page"
            :max="store.biblio.last_page"
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
      <q-separator></q-separator>

      <q-list separator>
        <q-item
          v-for="item of store.biblio.data"
          :key="`biblio-item-${item.id}`"
        >
          <q-item-section side>
            &nbsp;
          </q-item-section>
          <q-item-section>
            <q-item-label>
              {{ item.title }}
            </q-item-label>
          </q-item-section>
          <q-item-section side v-if="item.biblio_pubs.length > 0">
            <div
              class="inline cursor-pointer"
              @click="dialog = { visible: true, biblio: item }"
            >
              Publications
              <q-badge class="q-ml-sm">{{ item.biblio_pubs.length }}</q-badge>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </q-card>

    <biblio-dialog
      v-model="dialog.visible"
      :biblio="dialog.biblio"
    ></biblio-dialog>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";
import BiblioDialog from "src/components/BiblioDialog.vue";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";
import { cloneDeep } from "lodash-es";

const store = useStore();
const filter = ref(null);

const pagination = ref({
  sortBy: "sort_date",
  descending: true,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: store.biblio.total,
});

const dialog = ref({
  visible: false,
  biblio: null,
});

const types = store.biblioTypes.map((item) => {
  return {
    ...item,
    type: item.type.toUpperCase(),
  };
});

const type = ref(types[0]);

const biblioPath = computed(() => {
  let path = `/biblio/${type.value.id}?per_page=${pagination.value.rowsPerPage}&page=${pagination.value.page}`;
  if (filter.value) {
    path += `&search=${filter.value}`;
  }

  return path;
});

console.log(cloneDeep(store.biblio.data));

watch([type, filter], async () => {
  pagination.value.page = 1;

  store.biblio = await callApi({
    path: biblioPath.value,
    method: "get",
  });
});

watch(
  pagination,
  async (newVal, oldVal) => {
    if (newVal.rowsPerPage !== oldVal?.rowsPerPage) {
      pagination.value.page = 1;
    }

    store.biblio = await callApi({
      path: biblioPath.value,
      method: "get",
    });
  },
  { deep: true }
);
</script>
