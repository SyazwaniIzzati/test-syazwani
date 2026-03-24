<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';

const sidebarOpen = ref(false);
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">

    <aside class="hidden md:flex md:flex-col w-64 bg-white shadow-md">
      <div class="p-6 flex flex-col h-full">
        <Link :href="route('dashboard')">
          <ApplicationLogo class="h-20 w-auto mb-6" viewBox="0 -10 35 35" />
        </Link>

        <ul class="flex-1 space-y-2">
          <li>
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.today')" :active="route().current('tasks.today')">Today</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.upcoming')" :active="route().current('tasks.upcoming')">Upcoming</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.completed')" :active="route().current('tasks.completed')">Completed</NavLink>
          </li>
        </ul>
      </div>
    </aside>

    <div :class="{ 'block': sidebarOpen, 'hidden': !sidebarOpen }"
      class="fixed inset-0 z-40 md:hidden bg-black bg-opacity-25">
      <div class="absolute left-0 top-0 w-64 bg-white h-full shadow-md p-6 flex flex-col">
        <button @click="sidebarOpen = false" class="mb-6 text-gray-600">Close ✕</button>

        <ul class="flex-1 space-y-2">
          <li>
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
              @click="sidebarOpen = false">Dashboard</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.today')" :active="route().current('tasks.today')"
              @click="sidebarOpen = false">Today</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.upcoming')" :active="route().current('tasks.upcoming')"
              @click="sidebarOpen = false">Upcoming</NavLink>
          </li>
          <li>
            <NavLink :href="route('tasks.completed')" :active="route().current('tasks.completed')"
              @click="sidebarOpen = false">Completed</NavLink>
          </li>
        </ul>
      </div>
    </div>

    <div class="flex-1 flex flex-col">

      <div class="md:hidden bg-white shadow px-3 py-3">
        <button @click="sidebarOpen = true" class="text-gray-700 text-lg">☰ Menu</button>
      </div>

      <main class="p-0 flex-1">
        <slot />
      </main>

      <Footer class="bg-white shadow-inner py-4 text-center text-sm text-gray-500" />

    </div>
  </div>
</template>