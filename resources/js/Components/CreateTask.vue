<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close'])

const form = ref({
  title: '',
  description: '',
  scheduled_time: '',
  time_to_complete: '',
  priority: '',
})

const errors = ref({})

const submit = () => {
  router.post(route('tasks.store'), form.value, {
    onSuccess: () => {
      emit('close') 

      form.value = {
        title: '',
        description: '',
        scheduled_time: '',
        time_to_complete: '',
        priority: 'medium',
      }
    },
    onError: (err) => {
      errors.value = err
    }
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">

      <h2 class="text-xl font-bold mb-4">Create Task</h2>

      <input v-model="form.title" placeholder="Title"
        class="border rounded p-2 w-full mb-2" />
      <p class="text-red-500 text-sm">{{ errors.title }}</p>

      <textarea v-model="form.description" placeholder="Description"
        class="border rounded p-2 w-full mb-2"></textarea>

      <input type="datetime-local" v-model="form.scheduled_time"
        class="border rounded p-2 w-full mb-2" />

      <input type="datetime-local" v-model="form.time_to_complete"
        class="border rounded p-2 w-full mb-2" />

      <select v-model="form.priority" class="border rounded p-2 w-full mb-3">
        <option value="low">low</option>
        <option value="medium">medium</option>
        <option value="high">high</option>
      </select>

      <div class="flex justify-end gap-2">
        <button @click="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Create
        </button>

        <button @click="$emit('close')"
          class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
          Cancel
        </button>
      </div>

      <button @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 text-lg">&times;</button>

    </div>
  </div>
</template>