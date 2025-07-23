<template>
  <div class="rich-text-editor">
    <div ref="editor" class="editor-container"></div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import axios from 'axios'

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
        ['link', 'image'],
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

    // Image upload handler
    const imageHandler = () => {
      const input = document.createElement('input')
      input.setAttribute('type', 'file')
      input.setAttribute('accept', 'image/*')
      input.click()

      input.onchange = async () => {
        const file = input.files[0]
        if (file) {
          try {
            const formData = new FormData()
            formData.append('image', file)
            
            const response = await axios.post('/api/admin/upload/content-image', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            })
            
            const range = quill.getSelection()
            quill.insertEmbed(range.index, 'image', response.data.url)
          } catch (error) {
            console.error('Error uploading image:', error)
            alert('Nie udało się przesłać obrazka. Sprawdź czy plik nie jest za duży (max 2MB) i czy ma poprawny format.')
          }
        }
      }
    }

    // Set min-height
    onMounted(() => {
      if (editor.value) {
        editor.value.style.minHeight = props.minHeight;
      }

      quill = new Quill(editor.value, {
        modules: {
          toolbar: {
            container: props.toolbar,
            handlers: {
              image: imageHandler
            }
          }
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