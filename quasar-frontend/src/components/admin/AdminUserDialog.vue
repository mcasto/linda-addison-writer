<template>
  <q-dialog v-model="model.visible">
    <q-card style="width: 80vw;">
      <q-form @submit.prevent="createUser">
        <q-card-section class="column q-gutter-y-sm">
          <div class="text-h6 q-mb-sm">
            New User
          </div>
          <q-input
            type="text"
            label="Name"
            v-model="model.row.name"
            dense
            outlined
            required
            autofocus
          ></q-input>
          <q-input
            type="email"
            label="Email"
            v-model="model.row.email"
            dense
            outlined
            required
          ></q-input>
          <q-input
            :type="showPass ? 'text' : 'password'"
            label="Password"
            v-model="model.row.password"
            dense
            outlined
            required
          >
            <template #after>
              <q-btn
                :icon="showPass ? 'visibility_off' : 'visibility'"
                @click="showPass = !showPass"
              ></q-btn>
            </template>
          </q-input>
          <q-input
            :type="showPass ? 'text' : 'password'"
            label="Confirm Password"
            v-model="model.confirm"
            dense
            outlined
            required
          ></q-input>
          <q-checkbox
            v-model="model.row.permissions"
            label="Permissions"
          ></q-checkbox>
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn
            color="negative"
            label="Cancel"
            @click="model.visible = false"
          ></q-btn>
          <q-btn type="submit" color="positive" label="Create"></q-btn>
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { Notify } from "quasar";
import { ref } from "vue";

const model = defineModel();
const emits = defineEmits(["update"]);

const showPass = ref(false);

const createUser = () => {
  if (model.value.row.password !== model.value.confirm) {
    Notify.create({
      type: "negative",
      message: "Password and confirmation must match.",
    });

    return;
  }

  emits("update", model.value.row);
};
</script>
