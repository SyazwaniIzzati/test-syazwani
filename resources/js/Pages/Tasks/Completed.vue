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
    <Header title="Completed Tasks" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-screen-xl mx-auto pt-10 pb-16 px-8 lg:px-12">

        <!-- Page intro -->
        <div class="mb-8 border-b border-gray-200 pb-4">
          <p class="text-sm text-gray-500 mt-1">
            {{ tasks.length }} task{{ tasks.length === 1 ? '' : 's' }} completed
          </p>
        </div>

        <!-- Empty state -->
        <div v-if="tasks.length === 0"
          class="text-center text-gray-500 border border-dashed border-gray-300 rounded-lg py-16 bg-white">
          <p class="text-sm font-medium">No completed tasks yet</p>
        </div>

        <!-- Task list -->
        <div v-else class="space-y-3">
          <div v-for="task in tasks" :key="task.id"
            class="bg-gray-50 border border-gray-200 rounded-md p-5 flex items-start gap-4 transition-colors hover:border-gray-300">
            <span
              class="mt-0.5 shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-gray-900 text-white text-xs">
              ✓
            </span>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-1">
                <h2 class="text-base font-semibold text-gray-500 line-through truncate">
                  {{ task.title }}
                </h2>
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wide opacity-70"
                  :class="{
                    'bg-emerald-50 text-emerald-700 border border-emerald-200': task.priority === 'low',
                    'bg-amber-50 text-amber-700 border border-amber-200': task.priority === 'medium',
                    'bg-red-50 text-red-700 border border-red-200': task.priority === 'high'
                  }">
                  {{ task.priority }}
                </span>
              </div>

              <p class="text-sm text-gray-400 mb-3">{{ task.description }}</p>

              <dl class="grid grid-cols-2 gap-x-6 gap-y-1 text-xs text-gray-400 max-w-md">
                <div class="flex gap-1">
                  <dt class="font-medium text-gray-400">Scheduled:</dt>
                  <dd>{{ formatDate(task.scheduled_time) }}</dd>
                </div>
                <div class="flex gap-1">
                  <dt class="font-medium text-gray-400">Completed by:</dt>
                  <dd>{{ formatDate(task.time_to_complete) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>