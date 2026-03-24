<script setup>
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import CreateModal from '@/Components/CreateTask.vue'
import Header from '@/Components/Header.vue'

const props = defineProps({
    todayTasks: {
        type: Array,
        default: () => []
    },
    upcomingTasks: {
        type: Array,
        default: () => []
    },
    completedTasks: {
        type: Array,
        default: () => []
    }
})

const showCreate = ref(false)

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString('en-MY', {
        dateStyle: 'medium',
        timeStyle: 'short'
    })
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <Header title="Dashboard" />

        <div class="p-4">

            <div class="flex justify-end mb-4">
                <button
                    @click="showCreate = true"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                >
                    + Add Task
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-blue-600">Today</h3>
                        <p class="text-2xl font-bold">{{ todayTasks.length }}</p>
                    </div>

                    <div v-if="todayTasks.length === 0" class="text-gray-500 text-sm">
                        No tasks
                    </div>

                    <div v-for="task in todayTasks.slice(0, 3)" :key="task.id"
                        class="border p-2 mb-2 rounded">
                        <p class="font-semibold text-sm">{{ task.title }}</p>
                        <p class="text-xs text-gray-500">
                            {{ formatDate(task.scheduled_time) }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-yellow-600">Upcoming</h3>
                        <p class="text-2xl font-bold">{{ upcomingTasks.length }}</p>
                    </div>

                    <div v-if="upcomingTasks.length === 0" class="text-gray-500 text-sm">
                        No tasks
                    </div>

                    <div v-for="task in upcomingTasks.slice(0, 3)" :key="task.id"
                        class="border p-2 mb-2 rounded">
                        <p class="font-semibold text-sm">{{ task.title }}</p>
                        <p class="text-xs text-gray-500">
                            {{ formatDate(task.scheduled_time) }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-green-600">Completed</h3>
                        <p class="text-2xl font-bold">{{ completedTasks.length }}</p>
                    </div>

                    <div v-if="completedTasks.length === 0" class="text-gray-500 text-sm">
                        No tasks
                    </div>

                    <div v-for="task in completedTasks.slice(0, 3)" :key="task.id"
                        class="border p-2 mb-2 rounded">
                        <p class="font-semibold text-sm line-through">
                            {{ task.title }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ formatDate(task.time_to_complete) }}
                        </p>
                    </div>
                </div>

            </div>

            <CreateModal
                :show="showCreate"
                @close="showCreate = false"
            />

        </div>

    </AuthenticatedLayout>
</template>