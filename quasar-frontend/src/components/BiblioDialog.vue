<template>
  <q-dialog v-model="model">
    <q-card>
      <q-toolbar>
        <q-toolbar-title>
          {{ biblio.title }}
        </q-toolbar-title>
        <q-btn icon="close" flat v-close-popup round></q-btn>
      </q-toolbar>
      <q-separator></q-separator>
      <q-card-section>
        <div class="text-h6">
          Publications
        </div>
        <q-list dense separator>
          <q-item
            v-for="pub of biblio.biblio_pubs"
            :key="`biblio-pub-${pub.id}`"
          >
            <q-item-section>
              <q-item-label>
                {{ pub.publication }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              {{ pub.display_date }}
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["biblio"]);
</script>
