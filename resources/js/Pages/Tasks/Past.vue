<template>
    <AuthenticatedLayout>
        <Header title="Past Tasks" />

        <div class="bg-slate-50">
            <div class="max-w-7xl mx-auto pt-8 pb-12 px-8">

                <!-- Empty state -->
                <div v-if="taskList.length === 0"
                    class="text-center text-slate-400 text-sm py-16 bg-white border border-slate-200 rounded-lg">
                    Nothing here — no tasks scheduled before today.
                </div>

                <!-- Table -->
                <div v-else class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-200 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">
                                <th class="px-6 py-3">Task</th>
                                <th class="px-6 py-3">Scheduled</th>
                                <th class="px-6 py-3">Priority</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="task in paginatedTasks" :key="task.id"
                                class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 align-top">
                                    <div class="font-medium text-slate-800">{{ task.title }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5 line-clamp-1">{{ task.description }}</div>
                                </td>

                                <td class="px-6 py-4 align-top whitespace-nowrap text-slate-600">
                                    {{ formatDate(task.scheduled_time) }}
                                </td>

                                <td class="px-6 py-4 align-top">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="{
                                            'bg-emerald-500': task.priority === 'low',
                                            'bg-amber-500': task.priority === 'medium',
                                            'bg-rose-500': task.priority === 'high'
                                        }"></span>
                                        {{ capitalize(task.priority) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 align-top">
                                    <span class="inline-flex text-xs font-medium px-2 py-0.5 rounded-full" :class="task.is_completed
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'">
                                        {{ task.is_completed ? 'Completed' : 'Missed' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1"
                        class="flex items-center justify-between px-6 py-3 border-t border-slate-200 bg-slate-50">
                        <p class="text-xs text-slate-500">
                            Showing {{ rangeStart }}–{{ rangeEnd }} of {{ taskList.length }}
                        </p>

                        <div class="flex items-center gap-1">
                            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-2.5 py-1 text-xs font-medium rounded-md border border-slate-200 text-slate-600 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                Prev
                            </button>

                            <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                                class="w-7 h-7 text-xs font-medium rounded-md transition-colors"
                                :class="page === currentPage
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-slate-600 hover:bg-white border border-transparent hover:border-slate-200'">
                                {{ page }}
                            </button>

                            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-2.5 py-1 text-xs font-medium rounded-md border border-slate-200 text-slate-600 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Header from '@/Components/Header.vue';

const props = defineProps({
    tasks: Array
});

const taskList = ref([...props.tasks]);

const perPage = 8;
const currentPage = ref(1);

const totalPages = computed(() => Math.ceil(taskList.value.length / perPage) || 1);

const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return taskList.value.slice(start, start + perPage);
});

const rangeStart = computed(() => taskList.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1);
const rangeEnd = computed(() => Math.min(currentPage.value * perPage, taskList.value.length));

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return;
    currentPage.value = page;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleString('en-MY', {
        dateStyle: 'medium',
        timeStyle: 'short'
    });
};

const capitalize = (str) => {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
};
</script>