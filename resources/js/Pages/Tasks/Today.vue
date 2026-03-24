<template>
  <AuthenticatedLayout>
    <Header title="Tasks for Today" />

    <div class="max-w-7xl mx-auto mt-8 space-y-6 px-6">
      <div v-if="taskList.length === 0" class="text-center text-gray-500">
        No tasks for today!
      </div>

      <div v-for="task in taskList" :key="task.id" class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-semibold">{{ task.title }}</h2>
          <p class="text-gray-600">{{ task.description }}</p>
          <p class="text-gray-500 text-sm">Scheduled: {{ formatDate(task.scheduled_time) }}</p>
          <p class="text-gray-500 text-sm">Time to complete: {{ formatDate(task.time_to_complete) }}</p>
          <p class="text-gray-500 text-sm">Priority: {{ task.priority }}</p>
        </div>

        <div>
          <button 
            @click="toggleComplete(task)"
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"
          >
            {{ task.is_completed ? 'Completed' : 'Mark Complete' }}
          </button>
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