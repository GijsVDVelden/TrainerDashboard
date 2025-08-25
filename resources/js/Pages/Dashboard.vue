<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    attendanceStats: Array,
    totalTrainings: Number,
});
</script>

<template>
    <AuthenticatedLayout>
    </AuthenticatedLayout>
</template>
