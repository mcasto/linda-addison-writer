<template>
  <q-dialog v-model="model" @show="onDialogShow">
    <q-card>
      <q-editor
        v-model="review"
        :toolbar="toolbar"
        :definitions="definitions"
        ref="editorRef"
      ></q-editor>
      <q-card-actions class="justify-end">
        <q-btn
          label="Cancel"
          color="negative"
          @click="
            review = '';
            $emit('cancel');
          "
        ></q-btn>
        <q-btn label="Submit" color="primary" @click="createReview"></q-btn>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { Notify } from "quasar";
import callApi from "src/assets/call-api";
import { useStore } from "src/stores/store";
import { ref, onMounted } from "vue";

const store = useStore();

const model = defineModel();
const review = ref("");

const editorRef = ref(null);
const isEditorReady = ref(false);

const emits = defineEmits(["close", "cancel"]);

const toolbar = [
  ["bold", "italic", "strike", "underline", "subscript", "superscript"],
  ["mdash", "hr", "link"],
  ["undo", "redo"],
  ["viewsource"],
];

const insertMdash = () => {
  if (!isEditorReady.value || !editorRef.value) {
    console.error("Editor not ready");
    return;
  }

  try {
    // Use the editor instance directly
    const editor = editorRef.value;

    // Focus the editor first
    editor.focus();

    // Use the QEditor's API to insert content
    editor.runCmd("insertHTML", "&mdash;");
  } catch (error) {
    console.error("Error inserting mdash:", error);

    // Fallback method using browser API
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
      const range = selection.getRangeAt(0);
      range.deleteContents();
      const textNode = document.createTextNode("—"); // actual mdash character
      range.insertNode(textNode);
      range.setStartAfter(textNode);
      range.setEndAfter(textNode);
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }
};

// This function runs when the dialog is shown
const onDialogShow = () => {
  // Use nextTick to ensure the editor is rendered
  setTimeout(() => {
    isEditorReady.value = true;
    console.log("Editor should be ready now:", editorRef.value);
  }, 100);
};

const definitions = {
  mdash: {
    tip: "Insert mdash (—)",
    icon: "mdi-line-scan", // Using a more appropriate icon

    handler: insertMdash,
  },
};

const createReview = async () => {
  if (!!!review.value) {
    Notify.create({
      type: "negative",
      message: "Review cannot be empty.",
    });
    return;
  }
  const response = await callApi({
    path: "/admin/reviews-quotes",
    method: "post",
    payload: { review: review.value },
    useAuth: true,
  });

  if (response) {
    store.admin.reviews.push(response.review);
    emits("close");
  }
};
</script>
