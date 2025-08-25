<!-- resources/js/Components/PageHeader.vue -->
<script setup>
import { Link } from "@inertiajs/vue3"
import { ArrowLeft } from "lucide-vue-next"

const props = defineProps({
    title: String,
    backRoute: {
        type: String,
        required: false,
        default: null
    }
})
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 flex items-center gap-4">
        <!-- Back button (optioneel) -->
        <Link
            v-if="backRoute"
            :href="backRoute"
            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200"
        >
            <ArrowLeft class="w-5 h-5" />
        </Link>

        <!-- Title -->
        <h1 class="text-2xl font-semibold text-gray-900">
            {{ title }}
        </h1>
    </div>
</template>
