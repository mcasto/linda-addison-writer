<template>
  <q-dialog v-model="model" full-width>
    <q-card>
      <q-card-section>
        <div class="row">
          <q-input
            type="text"
            label="Title"
            v-model="biblio.title"
            dense
            outlined
            stack-label
            class="col"
          ></q-input>
          <q-select
            label="Type"
            stack-label
            outlined
            dense
            v-model="type"
            :options="store.admin.biblioTypes"
            option-label="type"
          ></q-select>
          <q-input
            type="date"
            v-model="biblio.sort_date"
            label="Sort Date"
            stack-label
            dense
            outlined
            hint="Used for ordering the bibliography entries"
          ></q-input>
        </div>
      </q-card-section>

      <q-card-section>
        <q-toolbar>
          <q-toolbar-title>
            Publications
          </q-toolbar-title>
          <q-btn icon="add" color="primary" round @click="addPub"></q-btn>
        </q-toolbar>

        <q-list>
          <template
            v-for="pub of biblio.biblio_pubs.filter(({ deleted }) => !deleted)"
            :key="`biblio-pub-${pub.id}`"
          >
            <q-item>
              <q-item-section>
                <q-item-label>
                  <div>
                    <q-input
                      type="text"
                      label="Publication"
                      v-model="pub.publication"
                      stack-label
                      dense
                      outlined
                    ></q-input>
                  </div>
                  <div class="row">
                    <q-input
                      type="date"
                      v-model="pub.pub_date"
                      dense
                      stack-label
                      outlined
                      label="Date Published"
                      class="col"
                    ></q-input>

                    <q-input
                      type="text"
                      v-model="pub.display_date"
                      dense
                      stack-label
                      outlined
                      label="Display Date"
                      class="col"
                    ></q-input>
                  </div>
                </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-btn
                  icon="delete"
                  color="negative"
                  flat
                  round
                  size="lg"
                  @click="deletePub(pub)"
                ></q-btn>
              </q-item-section>
            </q-item>
          </template>
        </q-list>
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
import { formatISO9075 } from "date-fns";
import { Loading, Notify, uid } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { computed, ref, watch } from "vue";

const model = defineModel();
const props = defineProps(["biblio"]);

const type = computed({
  get() {
    return props.biblio?.biblio_type_id
      ? store.admin.biblioTypes.find(
          (t) => t.id === props.biblio.biblio_type_id
        )
      : null;
  },
  set(newType) {
    if (newType && props.biblio) {
      props.biblio.biblio_type_id = newType.id;
    }
  },
});

const store = useStore();

const closeDialog = () => {
  Loading.show();
  setTimeout(() => {
    window.location.reload();
  }, 500);
};

const addPub = () => {
  props.biblio.biblio_pubs.push({
    id: `new-${uid()}`,
    publication: "New Publication",
    pub_date: formatISO9075(new Date(), { representation: "date" }),
    display_date: formatISO9075(new Date(), { representation: "date" }),
  });
};

const deletePub = (pub) => {
  Notify.create({
    type: "warning",
    html: true,
    timeout: 0,
    message: `<div>Are you sure you want to delete the "${pub.publication}"" publication?</div><div class='text-caption'>This won't actually take place unless you save this updated biblio entry</div>`,
    actions: [
      {
        label: "No",
      },
      {
        label: "Yes",
        handler: () => {
          props.biblio.biblio_pubs = props.biblio.biblio_pubs.map((item) => {
            if (item.id == pub.id) {
              item.deleted = true;
            }
            return item;
          });
        },
      },
    ],
  });
};

const save = async () => {
  Loading.show();
  const response = await callApi({
    path: "/admin/biblio",
    method: "post",
    payload: props.biblio,
    useAuth: true,
  });

  if (response.status == "ok") {
    Notify.create({
      type: "positive",
      message: "Biblio entry saved",
    });

    setTimeout(() => {
      window.location.reload();
    }, 3000);
  }
};
</script>
