<template>
  <q-dialog v-model="model" full-width full-height>
    <q-card>
      <q-card-section>
        <pre>
      {{ freebie }}
    </pre
        >
      </q-card-section>

      <q-card-actions class="justify-between">
        <q-btn
          label="Cancel"
          color="warning"
          class="text-black"
          @click="closeDialog"
        ></q-btn>

        <q-btn label="Save" color="primary" @click="save"></q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
const model = defineModel();
const props = defineProps(["freebie"]);

const closeDialog = () => {
  window.location.reload();
};

const save = async () => {
  console.log({ save: props.freebie });
};
</script>
