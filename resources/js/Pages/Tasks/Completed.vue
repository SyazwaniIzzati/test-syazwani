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

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto pt-8 pb-12 px-6 space-y-3">

        <div v-if="tasks.length === 0" class="text-center text-gray-500">
          No completed tasks yet.
        </div>

        <div v-for="task in tasks" :key="task.id" :class="[
          'bg-slate-50 hover:bg-white border-l-4 shadow-sm hover:shadow-md rounded-lg p-4 transition-all flex items-start gap-3',
          task.priority === 'low' ? 'border-l-green-500' :
            task.priority === 'medium' ? 'border-l-yellow-500' :
              'border-l-red-500'
        ]">
          <span class="mt-1 text-green-500 text-xl leading-none">✓</span>

          <div class="flex-1">
            <h2 class="font-bold text-lg text-gray-500 line-through">{{ task.title }}</h2>
            <p class="text-gray-400">{{ task.description }}</p>

            <div class="text-sm text-gray-400 mt-2 space-y-1">
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

      </div>
    </div>
  </AuthenticatedLayout>
</template>