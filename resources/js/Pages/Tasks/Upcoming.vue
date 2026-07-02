<script setup>
import { ref, computed } from 'vue'
import EditModal from '@/Components/EditModal.vue'
import DeleteModal from '@/Components/DeleteModal.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Header from '@/Components/Header.vue'

const props = defineProps({
    tasks: Array,
    auth: Object
})

const showEdit = ref(false)
const showDelete = ref(false)
const selectedTask = ref(null)

const openEdit = (task) => {
    selectedTask.value = { ...task }
    showEdit.value = true
}

const openDelete = (task) => {
    selectedTask.value = task
    showDelete.value = true
}

const handleTaskUpdate = (updatedTask) => {
    const index = props.tasks.findIndex(t => t.id === updatedTask.id)
    if (index !== -1) {
        props.tasks[index] = { ...updatedTask }
    }
}

const formatDate = (date) => {
    if (!date) return '-'

    return new Date(date).toLocaleString('en-MY', {
        dateStyle: 'medium',
        timeStyle: 'short'
    })
}

/* ---------- Pagination ---------- */
const currentPage = ref(1)
const perPage = 4

const totalPages = computed(() =>
    Math.max(1, Math.ceil(props.tasks.length / perPage))
)

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return props.tasks.slice(start, start + perPage)
})

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return
    currentPage.value = page
}
</script>

<template>
    <AuthenticatedLayout :user="auth.user">

        <Header title="Upcoming Tasks  " />

        <div class="p-4">

            <div v-for="task in paginatedTasks" :key="task.id"
                class="flex justify-between items-center border-l-4 p-4 mb-3 rounded-lg shadow-sm hover:shadow-md bg-slate-50 hover:bg-white transition-all"
                :class="{
                    'border-l-green-500': task.priority === 'low',
                    'border-l-yellow-500': task.priority === 'medium',
                    'border-l-red-500': task.priority === 'high'
                }">

                <div>
                    <h2 class="font-bold text-lg text-gray-800">{{ task.title }}</h2>
                    <p class="text-gray-600">{{ task.description }}</p>

                    <div class="text-sm text-gray-500 mt-2 space-y-1">
                        <p>📅 Scheduled: {{ formatDate(task.scheduled_time) }}</p>
                        <p>⏳ Complete by: {{ formatDate(task.time_to_complete) }}</p>

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

                <div class="flex gap-2">
                    <button @click="openEdit(task)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1.5 rounded-lg shadow-sm transition">
                        ✏️ Edit
                    </button>

                    <button @click="openDelete(task)"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg shadow-sm transition">
                        🗑 Delete
                    </button>
                </div>
            </div>

            <div v-if="tasks.length === 0" class="text-center text-gray-400 py-8">
                No tasks found
            </div>

            <!-- Pagination controls -->
            <div v-if="tasks.length > perPage" class="flex items-center justify-between mt-4 text-sm text-gray-500">
                <span>Showing {{ paginatedTasks.length }} of {{ tasks.length }} tasks</span>
                <div class="flex gap-1">
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-2 py-1 border rounded disabled:opacity-40">‹</button>
                    <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                        class="px-3 py-1 border rounded"
                        :class="page === currentPage ? 'bg-blue-600 text-white border-blue-600' : ''">
                        {{ page }}
                    </button>
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="px-2 py-1 border rounded disabled:opacity-40">›</button>
                </div>
            </div>

        </div>

        <EditModal :show="showEdit" :task="selectedTask" @close="showEdit = false" @update="handleTaskUpdate" />
        <DeleteModal :show="showDelete" :task="selectedTask" @close="showDelete = false" />

    </AuthenticatedLayout>
</template>