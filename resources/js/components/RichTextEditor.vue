<template>
  <div class="rich-text-editor">
    <div ref="editor" class="editor-container"></div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

export default {
  name: 'RichTextEditor',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Wpisz tekst...'
    },
    toolbar: {
      type: Array,
      default: () => [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link'],
        ['clean']
      ]
    },
    minHeight: {
      type: String,
      default: '200px'
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const editor = ref(null)
    let quill = null

    // Set min-height
    onMounted(() => {
      if (editor.value) {
        editor.value.style.minHeight = props.minHeight;
      }

      quill = new Quill(editor.value, {
        modules: {
          toolbar: props.toolbar
        },
        placeholder: props.placeholder,
        theme: 'snow'
      })

      // Set initial content
      if (props.modelValue) {
        quill.clipboard.dangerouslyPasteHTML(props.modelValue)
      }

      // Handle content changes
      quill.on('text-change', () => {
        const html = editor.value.querySelector('.ql-editor').innerHTML
        emit('update:modelValue', html)
      })
    })

    // Watch for props changes and update editor content
    watch(() => props.modelValue, (newVal) => {
      if (quill && newVal !== quill.root.innerHTML) {
        quill.clipboard.dangerouslyPasteHTML(newVal)
      }
    })

    // Clean up on component unmount
    onBeforeUnmount(() => {
      if (quill) {
        quill = null
      }
    })

    return {
      editor
    }
  }
}
</script>

<style>
.rich-text-editor .editor-container {
  border-radius: 0.375rem;
}

.rich-text-editor .ql-toolbar.ql-snow {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
  border-color: rgb(209, 213, 219);
}

.rich-text-editor .ql-container.ql-snow {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
  border-color: rgb(209, 213, 219);
}
</style> 