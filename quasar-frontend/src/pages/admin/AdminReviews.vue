<template>
  <div class="flex justify-center q-mt-md">
    <div
      class="flex fixed justify-between bg-white"
      :style="`z-index: ${showDialog ? '0' : '9999'};`"
    >
      <div class="q-mx-md">
        <q-btn
          icon="add"
          size="sm"
          round
          color="primary"
          @click="showDialog = true"
          class="q-mr-sm"
        ></q-btn>

        Reviews & Quotes
      </div>
      <div class="q-mx-md">
        <q-input
          type="text"
          v-model="filter"
          label="Search"
          dense
          outlined
          clearable
          @keydown.esc="filter = null"
          debounce="200"
          @update:model-value="resetPagination"
        ></q-input>
        Total Items: {{ filteredResults.length }}
      </div>
      <div class="q-mx-md">
        <q-pagination
          v-model="pagination.page"
          color="black"
          :max="pagination.max"
          :max-pages="pagination.maxPages"
          boundary-numbers
          direction-links
        />
      </div>
    </div>

    <page-container>
      <q-table
        :rows="paginatedResults"
        grid
        :pagination="pagination"
        hide-bottom
        class="q-pt-xl"
      >
        <template #item="props">
          <q-card class="q-my-md full-width">
            <q-card-actions class="justify-end items-center bg-grey-5">
              <div class="q-mr-md text-subtitle2">Reorder</div>
              <q-btn
                icon="mdi-arrow-up-bold"
                flat
                @click="reorderReviews(props.row, -1)"
                :disable="disableUp(props.row)"
              ></q-btn>
              <q-btn
                icon="mdi-arrow-down-bold"
                flat
                @click="reorderReviews(props.row, +1)"
                :disable="disableDown(props.row)"
              ></q-btn>
            </q-card-actions>
            <q-separator spaced></q-separator>
            <q-card-section>
              <div v-html="props.row.contents"></div>
            </q-card-section>
            <q-separator spaced></q-separator>
            <q-card-actions class="justify-end items-center bg-grey-5">
              <q-btn icon="delete" flat @click="deleteItem(props.row)"></q-btn>
            </q-card-actions>
          </q-card>
        </template>
      </q-table>
    </page-container>

    <review-dialog
      v-model="showDialog"
      @cancel="showDialog = false"
      @close="addReview"
    ></review-dialog>
  </div>
</template>

<script setup>
import { remove } from "lodash-es";
import { cloneDeep } from "lodash-es";
import { sortBy } from "lodash-es";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { computed, ref } from "vue";
import ReviewDialog from "src/components/admin/ReviewDialog.vue";

const store = useStore();

const filter = ref(null);
const showDialog = ref(false);

const pagination = ref({
  rowsPerPage: 2,
  page: 1,
  max: Math.ceil(store.admin.reviews.length / 2),
  maxPages: 3,
});

const filteredResults = computed(() => {
  return (
    store.admin.reviews.filter((review) => {
      if (!filter.value) {
        return true;
      }

      return review.contents.toLowerCase().includes(filter.value.toLowerCase());
    }) || []
  );
});

const updateDatabase = async () => {
  const response = await callApi({
    path: "/admin/reviews-quotes",
    method: "put",
    payload: cloneDeep(store.admin.reviews),
    useAuth: true,
  });
};

const resetPagination = () => {
  pagination.value.page = 1;
  pagination.value.max = Math.ceil(
    filteredResults.value.length / pagination.value.rowsPerPage
  );
};

const resetSortOrder = (array) => {
  return array.map((item, index) => {
    return {
      ...item,
      sort_order: index,
    };
  });
};

const paginatedResults = computed(() => {
  const reviews = filteredResults.value;
  const currentPage = pagination.value.page;
  const itemsPerPage = pagination.value.rowsPerPage;

  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;

  return reviews.slice(startIndex, endIndex);
});

const disableUp = (item) => {
  const firstItem = [...filteredResults.value].shift();
  return item.sort_order == firstItem.sort_order;
};

const disableDown = (item) => {
  const lastItem = [...filteredResults.value].pop();
  return item.sort_order == lastItem.sort_order;
};

const deleteItem = async (item) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this item?",
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: async () => {
          remove(store.admin.reviews, ({ id }) => id == item.id);
          store.admin.reviews = resetSortOrder(store.admin.reviews);

          updateDatabase();
        },
      },
    ],
  });
};

const addReview = () => {
  filter.value = null;
  pagination.value.max = Math.ceil(
    store.admin.reviews.length / pagination.value.rowsPerPage
  );
  pagination.value.page = pagination.value.max;

  showDialog.value = false;
};

const reorderReviews = (item, direction) => {
  const oldSortOrder = item.sort_order;
  const newSortOrder = oldSortOrder + direction;
  const otherItem = store.admin.reviews.find(
    ({ sort_order }) => sort_order == newSortOrder
  );
  item.sort_order = newSortOrder;
  otherItem.sort_order = oldSortOrder;

  store.admin.reviews = sortBy(
    store.admin.reviews.map((review) => {
      if (review.id == item.id) {
        return item;
      }
      if (review.id == otherItem.id) {
        return otherItem;
      }

      return review;
    }),
    "sort_order"
  );

  store.admin.reviews = resetSortOrder(store.admin.reviews);

  updateDatabase();
};
</script>
