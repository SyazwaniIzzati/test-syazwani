<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Header from '@/Components/Header.vue'

const props = defineProps({
    tasks: Array,
    auth: Object
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString('en-MY', {
        dateStyle: 'medium',
        timeStyle: 'short'
    })
}
</script>

<template>
  <AuthenticatedLayout :user="auth.user">
    <Header title="Completed Tasks " />

    <div class="p-4">
      <div v-for="task in tasks" :key="task.id" class="border p-4 mb-3 rounded-lg shadow-sm hover:shadow-md bg-white">
        <h2 class="font-bold text-lg">{{ task.title }}</h2>
        <p class="text-gray-600">{{ task.description }}</p>

        <div class="text-sm text-gray-500 mt-2 space-y-1">
          <p>📅 Scheduled: {{ formatDate(task.scheduled_time) }}</p>
          <p>⏳ Completed by: {{ formatDate(task.time_to_complete) }}</p>

          <p>
            ⚡ Priority:
            <span :class="{
                'text-green-600 font-semibold': task.priority === 'low',
                'text-yellow-600 font-semibold': task.priority === 'medium',
                'text-red-600 font-semibold': task.priority === 'high'
            }">
              {{ task.priority }}
            </span>
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>