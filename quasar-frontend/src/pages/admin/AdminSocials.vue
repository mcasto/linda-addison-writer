<template>
  <page-container>
    <q-table
      :rows="store.admin.socials"
      hide-bottom
      :pagination="{ rowsPerPage: 0 }"
      :columns="columns"
    >
      <template #header-cell-handle>
        <q-th></q-th>
      </template>

      <template #header-cell-tools>
        <q-th class="text-right">
          <q-btn
            icon="add"
            color="primary"
            round
            size="sm"
            @click="
              showDialog = {
                visible: true,
                social: { icon: null, url: null },
              }
            "
          ></q-btn>
        </q-th>
      </template>

      <template #body="props">
        <q-tr
          :draggable="dragEnabled"
          class="social-row"
          :class="{ 'social-row--dragging': draggedIndex === props.rowIndex }"
          @dragstart="onDragStart(props.rowIndex)"
          @dragover.prevent
          @dragenter.prevent
          @drop="onDrop(props.rowIndex)"
          @dragend="onDragEnd"
        >
          <q-td class="text-center" style="width: 1px">
            <q-icon
              name="drag_indicator"
              size="sm"
              class="drag-handle"
              @mousedown="dragEnabled = true"
              @mouseup="dragEnabled = false"
            ></q-icon>
          </q-td>
          <q-td class="text-center">
            <q-icon :name="props.row.icon" size="sm"></q-icon>
          </q-td>
          <q-td class="text-center">
            <q-btn icon="link" round flat></q-btn>
          </q-td>
          <q-td class="text-right">
            <q-btn
              flat
              icon="edit"
              @click="showDialog = { visible: true, social: props.row }"
            ></q-btn>
            <q-btn flat icon="delete" @click="deleteSocial(props.row)"></q-btn>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <social-dialog v-model="showDialog"></social-dialog>
  </page-container>
</template>

<script setup>
import PageContainer from "src/components/PageContainer.vue";
import { useStore } from "src/stores/store";
import { ref } from "vue";
import SocialDialog from "src/components/admin/SocialDialog.vue";
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { clone, remove } from "lodash-es";

const store = useStore();

const showDialog = ref({
  visible: false,
  social: null,
});

const dragEnabled = ref(false);
const draggedIndex = ref(null);

const columns = [
  {
    name: "handle",
    align: "center",
  },
  {
    label: "Icon",
    name: "icon",
    align: "center",
  },
  {
    label: "Link",
    name: "link",
    align: "center",
  },
  {
    name: "tools",
    align: "right",
  },
];

const onDragStart = (index) => {
  draggedIndex.value = index;
};

const onDragEnd = () => {
  dragEnabled.value = false;
  draggedIndex.value = null;
};

const onDrop = async (index) => {
  if (draggedIndex.value === null || draggedIndex.value === index) {
    return;
  }

  const socials = store.admin.socials;
  const [moved] = socials.splice(draggedIndex.value, 1);
  socials.splice(index, 0, moved);
  draggedIndex.value = null;

  for (let idx = 0; idx < socials.length; idx++) {
    socials[idx].sort_order = idx;
    const payload = clone(socials[idx]);
    await callApi({
      path: `/admin/socials/${payload.id}`,
      method: "put",
      payload,
      useAuth: true,
    });
  }
};

const deleteSocial = async (item) => {
  Notify.create({
    type: "warning",
    message: "Are you sure you want to delete this?",
    actions: [
      { label: "No" },
      {
        label: "Yes",
        handler: async () => {
          const response = await callApi({
            path: `/admin/socials/${item.id}`,
            method: "delete",
            useAuth: true,
          });

          if (response.item) {
            remove(store.admin.socials, ({ id }) => id == item.id);
          }
        },
      },
    ],
  });
};
</script>

<style scoped>
.drag-handle {
  cursor: grab;
}

.social-row--dragging {
  opacity: 0.4;
}
</style>
