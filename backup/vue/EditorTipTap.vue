<script setup>
import StarterKit from '@tiptap/starter-kit'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import Underline from '@tiptap/extension-underline'

const props = defineProps({
  modelValue: String,
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  onUpdate: ({ editor }) => {
    // console.log(editor.getHTML());
    emit('update:modelValue', editor.getHTML())
  },
  extensions: [Underline, StarterKit],
  editorProps: {
    attributes: {
      class:
        'border border-gray-400 p-4 min-h-[12rem] max-h-[auto] overflow-y-auto',
    },
  },
})

const toggleHeading = level => {
  if (!editor.value) return
  if (editor.value.isActive('heading', { level })) {
    editor.value.chain().focus().setParagraph().run()
  } else {
    editor.value.chain().focus().toggleHeading({ level }).run()
  }
}

const toggleFormat = format => {
  if (!editor.value) return
  editor.value.chain().focus()[format]().run()
}

const toggleList = listType => {
  if (!editor.value) return
  editor.value.chain().focus()[listType]().run()
}

const undo = () => {
  if (!editor.value) return
  editor.value.chain().focus().undo().run()
}

const redo = () => {
  if (!editor.value) return
  editor.value.chain().focus().redo().run()
}

</script>

<template>
  <div class="w-full p-2">
    <section
      v-if="editor"
      class="w-full flex items-center flex-wrap gap-2 p-2 border border-gray-300 rounded-md shadow-sm bg-gray-100"
    >
      <!-- ตัวหนา -->
      <button
        @click="toggleFormat('toggleBold')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('bold') }"
        class="toolbar-btn"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="16"
          height="16"
          fill="currentColor"
          class="bi bi-type-bold"
          viewBox="0 0 16 16"
        >
          <path
            d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 0 0 1.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z"
          />
        </svg>
      </button>

      <!-- ตัวเอียง -->
      <button
        @click="toggleFormat('toggleItalic')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('italic') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-italic.svg" alt="" />
      </button>

      <!-- ขีดเส้นใต้ -->
      <button
        @click="toggleFormat('toggleUnderline')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('underline') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-underline.svg" alt="" />
      </button>

      <!-- Heading -->
      <button
        @click="toggleHeading(1)"
        :class="{
          'bg-gray-400 text-white': editor.isActive('heading', { level: 1 }),
        }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-h1.svg" alt="" />
      </button>
      <button
        @click="toggleHeading(2)"
        :class="{
          'bg-gray-400 text-white': editor.isActive('heading', { level: 2 }),
        }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-h2.svg" alt="" />
      </button>
      <button
        @click="toggleHeading(3)"
        :class="{
          'bg-gray-400 text-white': editor.isActive('heading', { level: 3 }),
        }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-h3.svg" alt="" />
      </button>
      <button
        @click="toggleHeading(4)"
        :class="{
          'bg-gray-400 text-white': editor.isActive('heading', { level: 4 }),
        }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-h4.svg" alt="" />
      </button>
      <button
        @click="toggleHeading(5)"
        :class="{
          'bg-gray-400 text-white': editor.isActive('heading', { level: 5 }),
        }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/type-h5.svg" alt="" />
      </button>

      <!-- รายการ -->
      <button
        @click="toggleList('toggleBulletList')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('bulletList') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/list-ul.svg" alt="" />
      </button>
      <button
        @click="toggleList('toggleOrderedList')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('orderedList') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/list-ol.svg" alt="" />
      </button>

      <!-- โค้ดบล็อก -->
      <button
        @click="toggleFormat('toggleCodeBlock')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('codeBlock') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/code.svg" alt="" />
      </button>

      <!-- Blockquote -->
      <button
        @click="toggleFormat('toggleBlockquote')"
        :class="{ 'bg-gray-400 text-white': editor.isActive('blockquote') }"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/dash-lg.svg" alt="" />
      </button>

      <!-- ย้อนกลับ / ไปข้างหน้า -->
      <button
        @click="undo"
        :disabled="!editor.can().chain().focus().undo().run()"
        class="toolbar-btn"
      >
        <img src="@/assets/icon/reply-fill.svg" alt="" />
      </button>
      <button
        @click="redo"
        :disabled="!editor.can().chain().focus().redo().run()"
        class="toolbar-btn"
      >
        <img
          src="@/assets/icon/reply-fill.svg"
          alt=""
          class="img-redo"
        />
      </button>
    </section>

    <EditorContent :editor="editor" class="editor-content" />
  </div>
</template>

<style>

.toolbar-btn {
  padding: 8px;
  border-radius: 6px;
  background-color: white;
  border: 1px solid #d1d5db;
  transition: all 0.2s ease-in-out;
}

.toolbar-btn:hover {
  background-color: #e5e7eb;
}

.toolbar-btn img,
.toolbar-btn svg {
  width: 18px;
  height: 18px;
}

.toolbar-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.editor-content {
  border: 1px solid #d1d5db;
  padding: 12px;
  min-height: 14rem;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.img-redo {
  transform: scaleX(-1);
}
</style>
