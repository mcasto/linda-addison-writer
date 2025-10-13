<template>
  <page-container>
    <q-form @submit.prevent="updateUser">
      <div class="row q-gutter-x-sm">
        <q-input
          type="text"
          label="Name"
          v-model="store.admin.profile.name"
          dense
          outlined
          class="col"
          required
        ></q-input>
        <q-input
          type="email"
          label="Email"
          v-model="store.admin.profile.email"
          dense
          outlined
          class="col"
          required
        ></q-input>
      </div>

      <div class="q-mt-md row q-gutter-x-sm">
        <div class="col text-center">
          <q-btn
            label="Change Password"
            icon="mdi-lock-reset"
            color="primary"
            @click="
              passwordDialog = {
                visible: true,
                password: null,
                confirm: null,
                row: store.admin.profile,
              }
            "
          ></q-btn>
        </div>
      </div>

      <q-separator spaced></q-separator>

      <div class="flex justify-center">
        <q-btn color="positive" label="Update" @click="updateUser"></q-btn>
      </div>
    </q-form>

    <admin-user-password
      v-model="passwordDialog"
      @update="updatePassword"
    ></admin-user-password>
  </page-container>
</template>

<script setup>
import { useStore } from "src/stores/store";
import PageContainer from "src/components/PageContainer.vue";
import AdminUserPassword from "src/components/admin/AdminUserPassword.vue";
import { ref } from "vue";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { clone } from "lodash-es";

const store = useStore();

const passwordDialog = ref({
  visible: false,
  password: null,
  confirm: null,
  row: null,
});

const updateUser = async () => {
  const response = await callApi({
    path: `/admin/users/${store.admin.profile.id}`,
    method: "put",
    payload: clone(store.admin.profile),
    useAuth: true,
  });

  if (response.status == "ok") {
    Notify.create({
      type: "positive",
      message: "Account Updated",
    });
  }
};

const updatePassword = (row) => {
  store.admin.profile = row;
  passwordDialog.visible = false;

  Notify.create({
    type: "positive",
    message:
      "Password updated locally, confirm change by pressing the UPDATE button.",
  });

  passwordDialog.value.visible = false;
};
</script>
