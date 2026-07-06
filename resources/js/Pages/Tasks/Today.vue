<template>
  <AuthenticatedLayout>
    <Header title="Tasks for Today" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-none mx-auto pt-10 pb-16 px-8 lg:px-12">
        <!-- Page intro -->
        <div class="mb-8 border-b border-gray-200 pb-4">
          <p class="text-sm text-gray-500 mt-1">
            {{ taskList.length }} task{{ taskList.length === 1 ? '' : 's' }} scheduled
          </p>
        </div>

        <!-- Empty state -->
        <div v-if="taskList.length === 0"
          class="text-center text-gray-500 border border-dashed border-gray-300 rounded-lg py-16 bg-white">
          <p class="text-sm font-medium">No tasks scheduled for today</p>
        </div>

        <!-- Task list -->
        <div v-else class="space-y-3">
          <div v-for="task in taskList" :key="task.id"
            class="bg-white border border-gray-200 rounded-md p-5 flex justify-between items-start gap-6 transition-colors hover:border-gray-300">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-1">
                <h2 class="text-base font-semibold text-gray-900 truncate">
                  {{ task.title }}
                </h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wide"
                  :class="{
                    'bg-emerald-50 text-emerald-700 border border-emerald-200': task.priority === 'low',
                    'bg-amber-50 text-amber-700 border border-amber-200': task.priority === 'medium',
                    'bg-red-50 text-red-700 border border-red-200': task.priority === 'high'
                  }">
                  {{ task.priority }}
                </span>
              </div>

              <p class="text-sm text-gray-600 mb-3">{{ task.description }}</p>

              <dl class="grid grid-cols-2 gap-x-6 gap-y-1 text-xs text-gray-500 max-w-md">
                <div class="flex gap-1">
                  <dt class="font-medium text-gray-400">Scheduled:</dt>
                  <dd>{{ formatDate(task.scheduled_time) }}</dd>
                </div>
                <div class="flex gap-1">
                  <dt class="font-medium text-gray-400">Duration:</dt>
                  <dd>{{ formatDate(task.time_to_complete) }}</dd>
                </div>
              </dl>
            </div>

            <div class="shrink-0">
              <button @click="toggleComplete(task)" :disabled="task.is_completed"
                class="text-sm font-medium px-4 py-2 rounded-md border transition-colors" :class="task.is_completed
                  ? 'bg-green-100 text-green-400 border-green-200 cursor-not-allowed'
                  : 'bg-green-700 text-white border-green-700 hover:bg-green-600'">
                {{ task.is_completed ? 'Completed' : 'Mark Complete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router as Inertia } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Header from '@/Components/Header.vue';

const props = defineProps({
  tasks: Array
});

const taskList = ref([...props.tasks]);

const toggleComplete = (task) => {
  Inertia.put(route('tasks.update', task.id), {
    ...task,
    is_completed: true
  }, {
    onSuccess: () => {
      taskList.value = taskList.value.filter(t => t.id !== task.id)
      Inertia.get(route('tasks.completed'))
    }
  });
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleString('en-MY', {
    dateStyle: 'medium',
    timeStyle: 'short'
  });
};
</script>