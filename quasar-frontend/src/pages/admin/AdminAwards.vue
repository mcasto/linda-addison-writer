<template>
  <page-container>
    <div class="flex justify-end q-mb-sm">
      <q-btn icon="add" round color="primary" @click="addAward"></q-btn>
    </div>
    <div class="row q-gutter-y-sm">
      <template
        v-for="award of store.admin.awards"
        :key="`award-card-${award.id}`"
      >
        <div class="col-12">
          <award-card :award="award"></award-card>
        </div>
      </template>
    </div>

    <q-dialog v-model="awardDialog" full-width>
      <award-card :award="newAward"></award-card>
    </q-dialog>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import AwardCard from "src/components/admin/AwardCard.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();
const newAward = ref(null);
const awardDialog = ref(false);

const addAward = () => {
  newAward.value = {
    contents: "",
    raw: "",
    year: new Date().getFullYear(),
  };

  awardDialog.value = true;
};
</script>
