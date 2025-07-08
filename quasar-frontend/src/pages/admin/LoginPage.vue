<template>
  <q-page class="flex flex-center">
    <div class="row full-width">
      <q-card class="col-12 col-sm-6 offset-sm-3" bordered flat>
        <q-card-section>
          <div class="text-h6 q-mb-md q-ml-xs">
            Admin Login
          </div>
          <q-form>
            <div class="column q-gutter-y-sm">
              <q-input
                type="email"
                label="Email"
                stack-label
                dense
                outlined
                v-model="email"
              ></q-input>
              <q-input
                :type="showPassword ? 'text' : 'password'"
                label="Password"
                stack-label
                dense
                outlined
                v-model="password"
              >
                <template #after>
                  <q-btn
                    :icon="showPassword ? 'visibility_off' : 'visibility'"
                    flat
                    round
                    @click="showPassword = !showPassword"
                  ></q-btn>
                </template>
              </q-input>
            </div>
          </q-form>
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn label="Sign In" color="primary" @click="login"></q-btn>
        </q-card-actions>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { ref } from "vue";

const store = useStore();

const email = ref("castoware@gmail.com");
const password = ref("!OrTyTTAC609e$tsWKB@");
const showPassword = ref(false);

const login = async () => {
  const response = await callApi({
    path: "/admin/login",
    method: "post",
    payload: { email: email.value, password: password.value },
  });

  if (response.error) {
    Notify.create({
      type: "negative",
      message: response.error,
    });

    return;
  }

  store.token = response.token;

  store.router.push({ name: "admin-dashboard" });
};
</script>
