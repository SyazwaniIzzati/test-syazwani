<script setup>
import { ref, computed, watch } from 'vue'
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
const perPage = 8

const totalPages = computed(() =>
    Math.max(1, Math.ceil(props.tasks.length / perPage))
)

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return props.tasks.slice(start, start + perPage)
})

const rangeStart = computed(() =>
    props.tasks.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1
)
const rangeEnd = computed(() =>
    Math.min(currentPage.value * perPage, props.tasks.length)
)

watch(() => props.tasks.length, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return
    currentPage.value = page
}
</script>

<template>
    <AuthenticatedLayout :user="auth.user">

        <Header title="Upcoming Tasks" />

        <div class="min-h-screen bg-gray-50">
            <div class="max-w-none mx-auto pt-10 pb-16 px-8 lg:px-12">

                <!-- Page intro -->
                <div class="mb-8 border-b border-gray-200 pb-4">
                    <p class="text-sm text-gray-500 mt-1">
                        {{ tasks.length }} task{{ tasks.length === 1 ? '' : 's' }} scheduled
                    </p>
                </div>

                <!-- Empty state -->
                <div v-if="tasks.length === 0"
                    class="text-center text-gray-500 border border-dashed border-gray-300 rounded-lg py-16 bg-white">
                    <p class="text-sm font-medium">No upcoming tasks found</p>
                </div>

                <!-- Task list -->
                <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <div class="divide-y divide-gray-100">
                        <div v-for="task in paginatedTasks" :key="task.id"
                            class="px-6 py-4 flex justify-between items-start gap-6 hover:bg-gray-50/70 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <h2 class="text-base font-semibold text-gray-900 truncate">
                                        {{ task.title }}
                                    </h2>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wide"
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
                                        <dt class="font-medium text-gray-400">Complete by:</dt>
                                        <dd>{{ formatDate(task.time_to_complete) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="shrink-0 flex gap-2">
                                <button @click="openEdit(task)"
                                    class="text-sm font-medium px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Edit
                                </button>
                                <button @click="openDelete(task)"
                                    class="text-sm font-medium px-4 py-2 rounded-md border border-red-200 text-red-600 bg-white hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1"
                        class="flex items-center justify-between px-6 py-3 border-t border-gray-200 bg-gray-50">
                        <p class="text-xs text-gray-500">
                            Showing {{ rangeStart }}–{{ rangeEnd }} of {{ tasks.length }}
                        </p>

                        <div class="flex items-center gap-1">
                            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-2.5 py-1 text-xs font-medium rounded-md border border-gray-200 text-gray-600 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                Prev
                            </button>

                            <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                                class="w-7 h-7 text-xs font-medium rounded-md transition-colors" :class="page === currentPage
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-gray-600 hover:bg-white border border-transparent hover:border-gray-200'">
                                {{ page }}
                            </button>

                            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-2.5 py-1 text-xs font-medium rounded-md border border-gray-200 text-gray-600 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <EditModal :show="showEdit" :task="selectedTask" @close="showEdit = false" @update="handleTaskUpdate" />
        <DeleteModal :show="showDelete" :task="selectedTask" @close="showDelete = false" />

    </AuthenticatedLayout>
</template>