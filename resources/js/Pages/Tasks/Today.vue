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
                <span v-if="isExpired(task) && !task.is_completed"
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wide bg-gray-100 text-gray-500 border border-gray-200">
                  Expired
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

            <div class="shrink-0 flex flex-col gap-2 items-stretch">
              <button @click="toggleComplete(task)" :disabled="task.is_completed || isExpired(task)"
                class="text-sm font-medium px-4 py-2 rounded-md border transition-colors" :class="(task.is_completed || isExpired(task))
                  ? 'bg-green-100 text-green-400 border-green-200 cursor-not-allowed'
                  : 'bg-green-700 text-white border-green-700 hover:bg-green-600'">
                {{ task.is_completed ? 'Completed' : (isExpired(task) ? 'Mark Complete' : 'Mark Complete') }}
              </button>

              <div class="flex gap-2">
                <button @click="openEditModal(task)"
                  class="text-sm font-medium px-4 py-2 rounded-md border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                  Edit
                </button>
                <button @click="openDeleteModal(task)"
                  class="text-sm font-medium px-4 py-2 rounded-md border transition-colors bg-white text-red-600 border-red-200 hover:bg-red-50">
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <EditModal
      :show="showEditModal"
      :task="selectedTask"
      @close="showEditModal = false"
      @update="handleTaskUpdate"
    />

    <DeleteModal
      :show="showDeleteModal"
      :task="selectedTask"
      @close="showDeleteModal = false"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router as Inertia } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Header from '@/Components/Header.vue';
import EditModal from '@/Components/EditModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';

const props = defineProps({
  tasks: Array
});

const taskList = ref([...props.tasks]);

const handleTaskUpdate = (updatedTask) => {
    const index = taskList.value.findIndex(t => t.id === updatedTask.id)
    if (index !== -1) {
        taskList.value[index] = { ...updatedTask }
    }
}

// Reactive "now" so expired state updates live without a page refresh
const now = ref(new Date());
let clockInterval = null;

watch(() => props.tasks, (newTasks) => {
  taskList.value = [...newTasks];
});

onMounted(() => {
  clockInterval = setInterval(() => {
    now.value = new Date();
  }, 30000); // check every 30s
});

onUnmounted(() => {
  if (clockInterval) clearInterval(clockInterval);
});

const isExpired = (task) => {
  if (!task.time_to_complete) return false;
  return new Date(task.time_to_complete) < now.value;
};

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

// Modal state
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedTask = ref(null);

const openEditModal = (task) => {
  selectedTask.value = task;
  showEditModal.value = true;
};

const openDeleteModal = (task) => {
  selectedTask.value = task;
  showDeleteModal.value = true;
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleString('en-MY', {
    dateStyle: 'medium',
    timeStyle: 'short'
  });
};
</script>