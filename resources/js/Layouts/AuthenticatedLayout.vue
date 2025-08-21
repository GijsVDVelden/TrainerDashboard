<script setup>
import { ref } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
import { Home, Calendar, Users, LogOut } from 'lucide-vue-next'

const showingNavigationDropdown = ref(false)
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Top navbar desktop -->
        <nav class="hidden sm:flex border-b border-gray-100 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex h-16 justify-between items-center">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo class="block h-9 w-auto text-blue-600" />
                </Link>

                <!-- Desktop nav links -->
                <div class="space-x-6 flex items-center">
                    <Link :href="route('dashboard')" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-red-600 hover:text-red-800 font-medium">Uitloggen</Link>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Bottom nav mobile -->
        <nav class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg">
            <div class="flex justify-around items-center h-14">
                <Link :href="route('dashboard')" class="flex flex-col items-center text-gray-600 hover:text-blue-600">
                    <Home class="h-5 w-5" />
                    <span class="text-xs">Dashboard</span>
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="flex flex-col items-center text-red-600 hover:text-red-800">
                    <LogOut class="h-5 w-5" />
                    <span class="text-xs">Uitloggen</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
