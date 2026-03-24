<script setup>
import { ref } from 'vue'
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
</script>

<template>
    <AuthenticatedLayout :user="auth.user">

        <Header title="Upcoming Tasks  " />

        <div class="p-4">

            <div v-for="task in tasks" :key="task.id"
                class="flex justify-between items-center border p-4 mb-3 rounded-lg shadow-sm hover:shadow-md bg-white">

                <div>
                    <h2 class="font-bold text-lg">{{ task.title }}</h2>
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

        </div>

        <EditModal :show="showEdit" :task="selectedTask" @close="showEdit = false" @update="handleTaskUpdate"/>
        <DeleteModal :show="showDelete" :task="selectedTask" @close="showDelete = false" />

    </AuthenticatedLayout>
</template>