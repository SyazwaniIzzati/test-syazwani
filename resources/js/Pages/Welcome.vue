<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

function handleImageError() {
    document.getElementById('hero-image-container')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome to ToDoList" />
    <div class="font-sans bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#4F46E5] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <svg class="h-12 w-auto text-[#4F46E5] lg:h-16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11L12 14L22 4M21 12V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#4F46E5] dark:text-white dark:hover:text-white/80"
                        >
                            Go to My Lists
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#4F46E5] dark:text-white dark:hover:text-white/80"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#4F46E5] dark:text-white dark:hover:text-white/80"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-black dark:text-white sm:text-6xl">
                            Organize Everything, <br/> 
                            <span class="text-[#4F46E5]">step by step.</span>
                        </h1>
                        <p class="mt-4 text-lg">The simplest way to manage your personal and professional productivity.</p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 transition duration-300 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#4F46E5]/10 sm:size-16">
                                <svg class="size-6 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-sans text-xl font-semibold text-black dark:text-white">Quick Add</h2>
                                <p class="mt-4 text-sm/relaxed">
                                    Never let an idea slip away. Capture tasks instantly with our streamlined interface designed for speed and focus.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 transition duration-300 dark:bg-zinc-900 dark:ring-zinc-800">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#4F46E5]/10 sm:size-16">
                                <svg class="size-6 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white">Smart Categories</h2>
                                <p class="mt-4 text-sm/relaxed">
                                    Group your tasks by project, priority, or due date. Keep your work life and personal life perfectly separated.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 transition duration-300 dark:bg-zinc-900 dark:ring-zinc-800 lg:col-span-2">
                            <div>
                                <h2 class="text-xl font-semibold text-black dark:text-white text-center">Stay on Track</h2>
                                <p class="mt-4 text-sm/relaxed text-center max-w-2xl mx-auto">
                                    Visual progress bars and daily streaks help you stay motivated. Join thousands of users who are crushing their goals every single day.
                                </p>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    &copy; 2026 ToDoList Inc. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>