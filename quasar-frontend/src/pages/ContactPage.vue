<template>
  <page-container>
    <div class="bg-white">
      <q-toolbar>
        <q-toolbar-title>
          Contact Linda
        </q-toolbar-title>
      </q-toolbar>
      <q-separator></q-separator>
      <q-form>
        <div class="row">
          <div class="col-12 col-sm-4 q-pa-xs">
            <q-input
              type="text"
              label="First Name"
              outlined
              dense
              stack-label
              v-model="fields.first_name"
              required
              name="first_name"
            ></q-input>
          </div>

          <div class="col-12 col-sm-4 q-pa-xs">
            <q-input
              type="text"
              label="Last Name"
              outlined
              dense
              stack-label
              v-model="fields.last_name"
              required
              name="last_name"
            ></q-input>
          </div>

          <div class="col-12 col-sm-4 q-pa-xs">
            <q-input
              type="email"
              label="Email"
              outlined
              dense
              stack-label
              v-model="fields.email"
              required
              name="email"
            ></q-input>
          </div>

          <div class="col-12 q-pa-xs">
            <q-input
              type="textarea"
              label="Message"
              outlined
              dense
              stack-label
              v-model="fields.message"
              required
              name="message"
              input-style="height:15rem"
            ></q-input>
          </div>
        </div>

        <div class="flex justify-between">
          <a href="/api/download/press-kit">
            <q-btn
              type="button"
              icon="download"
              label="Download Linda's Press Kit"
              :size="Screen.xs ? 'xs' : ''"
              color="positive"
            ></q-btn>
          </a>

          <q-btn
            type="button"
            label="Send"
            :size="Screen.xs ? 'xs' : ''"
            color="black"
            @click="store.sendContact(fields)"
          ></q-btn>
        </div>
      </q-form>
    </div>
  </page-container>
</template>

<script setup>
import { Screen } from "quasar";
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const fields = ref({
  first_name: null,
  last_name: null,
  email: null,
  message: null,
});
</script>
