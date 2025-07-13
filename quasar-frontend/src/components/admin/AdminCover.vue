<template>
  <q-dialog v-model="model" v-if="cover" full-width full-height>
    <q-card>
      <q-card-section>
        <div class="row">
          <div class="col-3">
            <q-img :src="cover.image_url" v-if="!showUploader">
              <div class="absolute-top-right">
                <q-btn
                  icon="sync"
                  flat
                  round
                  dense
                  @click="showUploader = true"
                >
                  <q-tooltip class="bg-primary">
                    {{
                      startsWith(cover.id, "new-")
                        ? "Cover Image"
                        : "Replace Cover Image"
                    }}
                  </q-tooltip>
                </q-btn>
              </div>
            </q-img>
            <q-uploader
              v-if="showUploader"
              :url="`/api/admin/covers/upload/${cover.id}`"
              :headers="uploadHeaders"
              field-name="imageFile"
              auto-upload
              accept="image/*"
              max-files="1"
              style="width: 100%;"
            >
              <template v-slot:header="scope">
                <div class="row no-wrap items-center q-pa-sm q-gutter-xs">
                  <q-btn
                    icon="cancel"
                    @click="showUploader = false"
                    round
                    dense
                    flat
                    v-if="!startsWith(cover.id, 'new-')"
                  >
                    <q-tooltip>Cancel</q-tooltip>
                  </q-btn>

                  <q-spinner
                    v-if="scope.isUploading"
                    class="q-uploader__spinner"
                  />
                  <div class="col">
                    <div class="q-uploader__title">
                      {{
                        startsWith(cover.id, "new-")
                          ? "Cover Image"
                          : "Replace Cover Image"
                      }}
                    </div>
                    <div class="q-uploader__subtitle">
                      {{ scope.uploadSizeLabel }} /
                      {{ scope.uploadProgressLabel }}
                    </div>
                  </div>
                  <q-btn
                    v-if="scope.canAddFiles"
                    type="a"
                    icon="add_box"
                    @click="scope.pickFiles"
                    round
                    dense
                    flat
                  >
                    <q-uploader-add-trigger />
                    <q-tooltip>Select Image</q-tooltip>
                  </q-btn>
                  <q-btn
                    v-if="scope.canUpload"
                    icon="cloud_upload"
                    @click="scope.upload"
                    round
                    dense
                    flat
                  >
                    <q-tooltip>Upload Files</q-tooltip>
                  </q-btn>

                  <q-btn
                    v-if="scope.isUploading"
                    icon="clear"
                    @click="scope.abort"
                    round
                    dense
                    flat
                  >
                    <q-tooltip>Abort Upload</q-tooltip>
                  </q-btn>
                </div>
              </template>
            </q-uploader>
          </div>

          <div class="col-6 q-pl-md">
            <q-form>
              <q-input
                type="text"
                label="Title"
                v-model="cover.title"
                stack-label
                dense
                outlined
              ></q-input>
              <q-input
                type="text"
                label="Purchase URL"
                v-model="cover.purchase_url"
                stack-label
                dense
                outlined
                class="q-mt-md"
              >
                <template #after>
                  <q-btn
                    icon="link"
                    flat
                    round
                    color="primary"
                    @click="openLink(cover.purchase_url)"
                  ></q-btn>
                </template>
              </q-input>

              <q-card class="q-mt-lg" bordered>
                <q-card-section>
                  <div class="text-h6">Description</div>
                  <markdown-editor v-model="cover.raw"></markdown-editor>
                </q-card-section>
              </q-card>

              <div class="flex justify-between q-mt-md">
                <q-btn
                  color="warning"
                  class="text-black"
                  @click="closePopup"
                  label="Cancel"
                ></q-btn>
                <q-btn label="Save" color="primary" @click="saveCover"></q-btn>
              </div>
            </q-form>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useStore } from "src/stores/store";
import { onUpdated, ref } from "vue";

import MarkdownEditor from "../MarkdownEditor.vue";
import callApi from "src/assets/call-api";
import { Loading, Notify } from "quasar";
import { startsWith } from "lodash-es";

const model = defineModel();
const props = defineProps(["cover"]);

const store = useStore();

const showUploader = ref(false);

const uploadHeaders = [
  {
    name: "Authorization",
    value: `Bearer ${store.token}`,
  },
];

const closePopup = () => {
  window.location.reload();
};

const openLink = (url) => {
  window.open(url);
};

const saveCover = async () => {
  Loading.show({ delay: 100 });

  const { id, raw, purchase_url, sort_order, title, replaceImage } = {
    ...props.cover,
    replaceImage: showUploader.value,
  };

  const method = startsWith(props.cover.id, "new-") ? "post" : "put";

  const response = await callApi({
    path: `/admin/covers/${props.cover.id}`,
    method,
    payload: { id, raw, purchase_url, sort_order, title, replaceImage },
    useAuth: true,
  });

  if (response.status == "ok") {
    window.location.reload();
    return;
  }

  Notify.create({
    type: "negative",
    message: response.message,
  });
};

onUpdated(() => {
  showUploader.value = startsWith(props.cover.id, "new-") ? true : false;
});
</script>
