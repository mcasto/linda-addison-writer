<template>
  <page-container>
    <q-toolbar class="justify-end">
      <q-btn
        icon="add"
        round
        color="primary"
        @click="editDialog = { visible: true, news: newNewsTemplate() }"
      ></q-btn>
    </q-toolbar>
    <div class="row">
      <div
        class="col-4 q-pa-sm"
        v-for="news of store.admin.latest_news"
        :key="`news-${news.id}`"
      >
        <q-card class="full-height">
          <q-card-actions class="justify-between">
            {{ news.date }}
            <div>
              <q-btn
                icon="delete"
                flat
                round
                color="negative"
                @click="deleteNews(news)"
              ></q-btn>
              <q-btn
                icon="edit"
                flat
                round
                color="primary"
                @click="editDialog = { visible: true, news }"
              ></q-btn>
            </div>
          </q-card-actions>
          <q-separator></q-separator>
          <q-card-section>
            <div v-html="news.contents"></div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <admin-news-dialog
      v-model="editDialog.visible"
      :news="editDialog.news"
    ></admin-news-dialog>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import AdminNewsDialog from "src/components/admin/AdminNewsDialog.vue";

import { formatISO9075 } from "date-fns";
import { Notify, uid } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

console.log(store.admin.latest_news);

const editDialog = ref({
  visible: false,
  news: null,
});

const newNewsTemplate = () => {
  return {
    id: `new-${uid()}`,
    date: formatISO9075(new Date(), { representation: "date" }),
    raw: "New News Item",
  };
};

const deleteNews = async (news) => {
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
          const response = await callApi({
            path: `/admin/news/${news.id}`,
            method: "delete",
            useAuth: true,
          });

          console.log({ response });

          if (response.status == "ok") {
            window.location.reload();
          }
        },
      },
    ],
  });
};
</script>
