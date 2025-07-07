<template>
  <q-table
    grid
    :rows="store.awards"
    :pagination="{ rowsPerPage: -1 }"
    hide-bottom
    class="bg-white q-mx-xl"
  >
    <template #top>
      <q-toolbar>
        <q-toolbar-title>
          Awards
        </q-toolbar-title>
      </q-toolbar>
    </template>

    <template #item="props">
      <div class="col-12 col-sm-6 col-lg-4 q-pa-sm">
        <q-card class="full-height" bordered flat>
          <q-card-section>
            <div v-html="props.row.contents"></div>
          </q-card-section>
          <q-card-actions class="justify-end">
            {{ props.row.year }}
          </q-card-actions>
        </q-card>
      </div>
    </template>
  </q-table>
</template>

<script setup>
import { useStore } from "src/stores/store";

const store = useStore();
</script>
