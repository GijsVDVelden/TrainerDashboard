<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <Head title="Settings" />
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Instellingen</h1>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
