<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
  task: Object
})

const emit = defineEmits(['close'])

const deleteTask = () => {
  router.delete(route('tasks.destroy', props.task.id), {
  onSuccess: () => {
    emit('close')       
    router.reload()    
  }
})
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
      
      <h2 class="text-xl font-bold mb-4">Delete this task?</h2>
      <p class="text-gray-600 mb-4">Are you sure you want to delete "<span class="font-semibold">{{ props.task.title }}</span>"? This action cannot be undone.</p>

      <div class="flex justify-end gap-2 mt-4">
        <button
          @click="deleteTask"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
        >
          Yes
        </button>
        <button
          @click="$emit('close')"
          class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
        >
          No
        </button>
      </div>

      <button
        @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 font-bold"
      >
        &times;
      </button>
    </div>
  </div>
</template>