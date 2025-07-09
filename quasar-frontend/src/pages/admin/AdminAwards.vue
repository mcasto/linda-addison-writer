<template>
  <page-container>
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
  </page-container>
</template>

<script setup>
import { clone } from "lodash-es";
import PageContainer from "src/components/PageContainer.vue";
import AwardCard from "src/components/admin/AwardCard.vue";
import { useStore } from "src/stores/store";

const store = useStore();

console.log({ awards: clone(store.admin.awards) });
</script>
