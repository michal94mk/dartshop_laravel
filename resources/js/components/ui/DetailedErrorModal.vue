<template>
  <div v-if="show" class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ title || 'Błąd' }}
              </h3>
              
              <div class="mt-2">
                <pre class="text-sm text-red-600 whitespace-pre-wrap bg-red-50 p-4 rounded">{{ formattedMessage }}</pre>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="$emit('close')"
            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Zamknij
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DetailedErrorModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    message: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    }
  },
  computed: {
    formattedMessage() {
      // If the message is empty, return a default message
      if (!this.message) {
        return 'Wystąpił nieznany błąd.';
      }
      
      console.log('Original message:', this.message);
      console.log('Message type:', typeof this.message);
      
      // Special case for newlines in the message
      let formatted = this.message;
      
      // Replace literal "\n" strings with actual newlines
      formatted = formatted.replace(/\\n/g, '\n');
      
      // Replace PHP_EOL placeholders with actual newlines
      formatted = formatted.replace(/PHP_EOL/g, '\n');
      
      // Additional check to handle JSON-encoded strings that contain escaped newlines
      try {
        // Check if this looks like a JSON-encoded string with escape sequences
        if (formatted.includes('\\n') || formatted.includes('\\r') || formatted.includes('\\t')) {
          // Try to parse and re-stringify to properly handle escape sequences
          const temp = JSON.parse(`"${formatted.replace(/"/g, '\\"')}"`);
          if (temp) formatted = temp;
        }
      } catch (e) {
        console.log('Failed to process escaped characters:', e);
      }
      
      // If the message looks like a JSON string, try to parse it
      if (formatted.startsWith('{') && formatted.endsWith('}')) {
        try {
          const parsed = JSON.parse(formatted);
          return parsed.message || formatted;
        } catch (e) {
          // Not valid JSON, just return the original message
          console.log('Failed to parse JSON:', e);
        }
      }
      
      console.log('Formatted message:', formatted);
      return formatted;
    }
  },
  emits: ['close']
}
</script> 