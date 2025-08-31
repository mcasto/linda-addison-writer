<template>
  <q-dialog v-model="model.visible">
    <q-card>
      <q-form @submit.prevent="updatePassword">
        <q-card-section class="column q-gutter-y-sm">
          <div class="text-h6 q-mb-sm">
            Change Password
          </div>

          <q-input
            :type="showPass ? 'text' : 'password'"
            v-model="model.password"
            label="New Password"
            dense
            outlined
            required
          ></q-input>

          <q-input
            :type="showPass ? 'text' : 'password'"
            v-model="model.confirm"
            label="Confirm Password"
            dense
            outlined
            required
          ></q-input>
        </q-card-section>

        <q-card-actions class="justify-end">
          <q-btn
            color="negative"
            label="Cancel"
            @click="model.visible = false"
          ></q-btn>
          <q-btn type="submit" color="positive" label="Update"></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { Notify } from "quasar";
import { ref } from "vue";

const model = defineModel();
const showPass = ref(false);
const emits = defineEmits(["update"]);

const updatePassword = () => {
  if (model.value.password !== model.value.confirm) {
    Notify.create({
      type: "negative",
      message: "Password and confirmation must match",
    });

    return;
  }

  emits("update");
};
</script>
