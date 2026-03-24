<script setup>
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  task: Object
})

const emit = defineEmits(['close'])

const localTask = ref({})

watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      localTask.value = { ...newTask } 
    }
  },
  { immediate: true }
)

const updateTask = () => {
  router.put(route('tasks.update', localTask.value.id), localTask.value, {
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
      <h2 class="text-xl font-bold mb-4">Edit Task</h2>

      <input
        v-model="localTask.title"
        placeholder="Task Title"
        class="border rounded p-2 w-full mb-3"
      />

      <textarea
        v-model="localTask.description"
        placeholder="Task Description"
        class="border rounded p-2 w-full mb-3"
      ></textarea>

      <select v-model="localTask.priority" class="border rounded p-2 w-full mb-3">
        <option value="low">low</option>
        <option value="medium">medium</option>
        <option value="high">high</option>
      </select>

      <label class="block mb-1 font-semibold">Schedule Time</label>
      <input
        type="datetime-local"
        v-model="localTask.scheduled_time"
        class="border rounded p-2 w-full mb-3"
      />

      <label class="block mb-1 font-semibold">Time to Complete</label>
      <input
        type="datetime-local"
        v-model="localTask.time_to_complete"
        min="1"
        class="border rounded p-2 w-full mb-3"
      />

      <div class="flex justify-end gap-2 mt-4">
        <button
          @click="updateTask"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Save
        </button>
        <button
          @click="$emit('close')"
          class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
        >
          Cancel
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