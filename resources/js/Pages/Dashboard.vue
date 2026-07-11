<script setup>
import { ref, computed, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import CreateModal from '@/Components/CreateTask.vue'
import Header from '@/Components/Header.vue'

const props = defineProps({
    todayTasks: { type: Array, default: () => [] },
    upcomingTasks: { type: Array, default: () => [] },
    completedTasks: { type: Array, default: () => [] },
    pastTasks: { type: Array, default: () => [] },
})

const showCreate = ref(false)

const totalTasks = computed(() => {
    const ids = new Set()
        ;[...props.todayTasks, ...props.upcomingTasks, ...props.completedTasks, ...props.pastTasks]
            .forEach(t => ids.add(t.id))
    return ids.size
})

const completionPercent = computed(() => {
    if (totalTasks.value === 0) return 0
    return Math.round((props.completedTasks.length / totalTasks.value) * 100)
})

const isToday = (date) => {
    if (!date) return false
    const d = new Date(date)
    const now = new Date()
    return d.getFullYear() === now.getFullYear() &&
        d.getMonth() === now.getMonth() &&
        d.getDate() === now.getDate()
}

// Determine date bucket independent of status (Today / Upcoming / Past)
const getDateCategory = (date) => {
    if (!date) return 'Other'
    if (isToday(date)) return 'Today'
    return new Date(date) > new Date() ? 'Upcoming' : 'Past'
}

const allTasks = computed(() => {
    const today = props.todayTasks.map(t => ({ ...t, status: 'Today', dateCategory: 'Today' }))
    const upcoming = props.upcomingTasks.map(t => ({ ...t, status: 'Upcoming', dateCategory: getDateCategory(t.scheduled_time) }))
    const completed = props.completedTasks.map(t => ({ ...t, status: 'Completed', dateCategory: getDateCategory(t.scheduled_time) }))
    const missed = props.pastTasks.map(t => ({ ...t, status: 'Missed', dateCategory: getDateCategory(t.scheduled_time) }))
    return [...today, ...upcoming, ...completed, ...missed]
})

const search = ref('')
const statusFilter = ref('All')

const filteredTasks = computed(() => {
    return allTasks.value.filter(task => {
        const matchesSearch = task.title.toLowerCase().startsWith(search.value.toLowerCase())

        let matchesFilter = true
        if (statusFilter.value === 'Today') {
            matchesFilter = task.dateCategory === 'Today'
        } else if (statusFilter.value === 'Upcoming') {
            matchesFilter = task.dateCategory === 'Upcoming'
        } else if (statusFilter.value === 'Completed') {
            matchesFilter = task.status === 'Completed'
        } else if (statusFilter.value === 'Missed') {
            matchesFilter = task.status === 'Missed'
        }
        // 'All' keeps matchesFilter = true

        return matchesSearch && matchesFilter
    })
})

const currentPage = ref(1)
const perPage = 8

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filteredTasks.value.length / perPage))
)

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return filteredTasks.value.slice(start, start + perPage)
})

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return
    currentPage.value = page
}

watch([search, statusFilter], () => { currentPage.value = 1 })

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString('en-MY', { dateStyle: 'medium', timeStyle: 'short' })
}

const statusBadge = (status) => ({
    Today: 'bg-purple-100 text-purple-700',
    Upcoming: 'bg-orange-100 text-orange-700',
    Completed: 'bg-green-100 text-green-600',
    Missed: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-600')

const priorityBadge = (priority) => ({
    low: 'bg-teal-100 text-teal-700',
    medium: 'bg-amber-100 text-amber-700',
    high: 'bg-pink-100 text-pink-700',
}[priority] || 'bg-gray-100 text-gray-600')

const radius = 60
const circumference = 2 * Math.PI * radius
const dashOffset = computed(() =>
    circumference - (completionPercent.value / 100) * circumference
)

const completedToday = computed(() =>
    props.completedTasks.filter(t => isToday(t.scheduled_time))
)

const missedToday = computed(() =>
    props.pastTasks.filter(t => isToday(t.scheduled_time))
)

const todayTotal = computed(() => props.todayTasks.length + completedToday.value.length + missedToday.value.length)

const todayCompletionPercent = computed(() => {
    if (todayTotal.value === 0) return 0
    return Math.round((completedToday.value.length / todayTotal.value) * 100)
})

const radius2 = 60
const circumference2 = 2 * Math.PI * radius2
const dashOffset2 = computed(() =>
    circumference2 - (todayCompletionPercent.value / 100) * circumference2
)

const setFilter = (status) => {
    statusFilter.value = status
    currentPage.value = 1
}
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <Header title="Dashboard" />

        <div class="p-4 space-y-6">

            <div class="flex justify-end">
                <button @click="showCreate = true"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Add Task
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">

                <div @click="setFilter('All')"
                    class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex items-center gap-4 cursor-pointer"
                    :class="{ 'ring-2 ring-blue-500': statusFilter === 'All' }">
                    <div class="bg-blue-100 text-blue-600 rounded-lg p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-500">Total Tasks</h3>
                        <p class="text-2xl font-bold">{{ totalTasks }}</p>
                        <p class="text-xs text-gray-400">All tasks in total</p>
                    </div>
                </div>

                <div @click="setFilter('Today')"
                    class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex items-center gap-4 cursor-pointer"
                    :class="{ 'ring-2 ring-purple-500': statusFilter === 'Today' }">
                    <div class="bg-purple-100 text-purple-600 rounded-lg p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-500">Today</h3>
                        <p class="text-2xl font-bold">{{ todayTotal }}</p>
                        <p class="text-xs text-gray-400">Tasks for today</p>
                    </div>
                </div>

                <div @click="setFilter('Upcoming')"
                    class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex items-center gap-4 cursor-pointer"
                    :class="{ 'ring-2 ring-orange-500': statusFilter === 'Upcoming' }">
                    <div class="bg-orange-100 text-orange-600 rounded-lg p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-500">Upcoming</h3>
                        <p class="text-2xl font-bold">{{ upcomingTasks.length }}</p>
                        <p class="text-xs text-gray-400">Tasks in upcoming</p>
                    </div>
                </div>

                <div @click="setFilter('Completed')"
                    class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex items-center gap-4 cursor-pointer"
                    :class="{ 'ring-2 ring-green-500': statusFilter === 'Completed' }">
                    <div class="bg-green-100 text-green-600 rounded-lg p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-500">Completed</h3>
                        <p class="text-2xl font-bold">{{ completedTasks.length }}</p>
                        <p class="text-xs text-gray-400">Tasks completed</p>
                    </div>
                </div>

                <div @click="setFilter('Missed')"
                    class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition flex items-center gap-4 cursor-pointer"
                    :class="{ 'ring-2 ring-red-500': statusFilter === 'Missed' }">
                    <div class="bg-red-100 text-red-600 rounded-lg p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-500">Missed</h3>
                        <p class="text-2xl font-bold">{{ pastTasks.length }}</p>
                        <p class="text-xs text-gray-400">Tasks missed</p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white rounded-lg shadow p-4 lg:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <h3 class="text-lg font-bold flex items-center gap-2">All Tasks</h3>
                        <div class="flex gap-2">
                            <select v-model="statusFilter" class="border rounded px-2 py-1 text-sm">
                                <option>All</option>
                                <option>Today</option>
                                <option>Upcoming</option>
                                <option>Completed</option>
                                <option>Missed</option>
                            </select>
                            <input v-model="search" type="text" placeholder="Search tasks..."
                                class="border rounded px-2 py-1 text-sm" />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2">Task</th>
                                    <th class="py-2">Priority</th>
                                    <th class="py-2">Due Date &amp; Time</th>
                                    <th class="py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="paginatedTasks.length === 0">
                                    <td colspan="4" class="py-6 text-center text-gray-400">No tasks found</td>
                                </tr>
                                <tr v-for="task in paginatedTasks" :key="task.id" class="border-b last:border-0">
                                    <td class="py-2 font-medium"
                                        :class="{ 'line-through text-gray-400': task.status === 'Completed' }">
                                        {{ task.title }}
                                    </td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 rounded text-xs" :class="priorityBadge(task.priority)">
                                            {{ task.priority }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-gray-500">
                                        {{ formatDate(task.scheduled_time) }}
                                    </td>
                                    <td class="py-2">
                                        <span class="px-2 py-0.5 rounded text-xs" :class="statusBadge(task.status)">
                                            {{ task.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
                        <span>Showing {{ paginatedTasks.length }} of {{ filteredTasks.length }} tasks</span>
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

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-bold mb-4">Progress Overview</h3>

                    <div class="flex justify-center gap-4">
                        <!-- Overall -->
                        <div class="flex flex-col items-center">
                            <svg class="w-full h-auto" viewBox="0 0 160 160">
                                <circle cx="80" cy="80" :r="radius" fill="none" stroke="#E5E7EB" stroke-width="14" />
                                <circle cx="80" cy="80" :r="radius" fill="none" stroke="#16A34A" stroke-width="14"
                                    stroke-linecap="round" :stroke-dasharray="circumference"
                                    :stroke-dashoffset="dashOffset" transform="rotate(-90 80 80)" />
                                <text x="80" y="76" text-anchor="middle" font-size="28" font-weight="bold"
                                    fill="#111827">
                                    {{ completionPercent }}%
                                </text>
                                <text x="80" y="96" text-anchor="middle" font-size="11" fill="#6B7280">Overall</text>
                            </svg>
                        </div>

                        <!-- Today -->
                        <div class="flex flex-col items-center">
                            <svg class="w-full h-auto" viewBox="0 0 160 160">
                                <circle cx="80" cy="80" :r="radius2" fill="none" stroke="#E5E7EB" stroke-width="14" />
                                <circle cx="80" cy="80" :r="radius2" fill="none" stroke="#2563EB" stroke-width="14"
                                    stroke-linecap="round" :stroke-dasharray="circumference2"
                                    :stroke-dashoffset="dashOffset2" transform="rotate(-90 80 80)" />
                                <text x="80" y="76" text-anchor="middle" font-size="28" font-weight="bold"
                                    fill="#111827">
                                    {{ todayCompletionPercent }}%
                                </text>
                                <text x="80" y="96" text-anchor="middle" font-size="11" fill="#6B7280">Today</text>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2"><span
                                    class="w-2 h-2 rounded-full bg-green-600"></span>Completed All</span>
                            <span>{{ completedTasks.length }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2"><span
                                    class="w-2 h-2 rounded-full bg-blue-600"></span>Completed Today</span>
                            <span>{{ completedToday.length }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span>Missed Tasks
                            </span>
                            <span>{{ pastTasks.length }}</span>
                        </div>
                    </div>

                    <div class="mt-4 bg-gray-50 rounded-lg p-3 text-sm text-gray-600">
                        🚀 You've completed <strong>{{ completionPercent }}%</strong> of your tasks. Keep it up!
                    </div>
                </div>

            </div>

            <CreateModal :show="showCreate" @close="showCreate = false" />
        </div>
    </AuthenticatedLayout>
</template>