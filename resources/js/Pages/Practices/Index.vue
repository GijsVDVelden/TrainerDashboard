<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    events: Array,   // gewone array, geen paginator meer
    filter: String,  // 'upcoming' | 'past' (of undefined)
});

// Datum formatter
function fmtDate(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return d.toLocaleDateString();
}

// Tijd formatter
function fmtTime(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
}

// Helper voor actieve tab
function isActive(val) {
    return (props.filter ?? "upcoming") === val;
}
</script>

<template>
    <Head title="Trainingen" />
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-semibold">Trainingen</h1>

                <!-- Filter tabs -->
                <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                    <Link
                        :href="route('practices.index', { filter: 'upcoming' })"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium',
                            isActive('upcoming') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Gepland
                    </Link>
                    <Link
                        :href="route('practices.index', { filter: 'past' })"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium border-l border-gray-200',
                            isActive('past') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Verlopen
                    </Link>
                </div>

                <!-- Nieuwe training -->
                <Link
                    :href="route('practices.create', { type: 'training' })"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium"
                >
                    Nieuwe training
                </Link>
            </div>

            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="w-full">
                    <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-2 font-medium text-gray-600">Datum</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Start</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Einde</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Locatie</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Notities</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Acties</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="ev in events" :key="ev.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ fmtDate(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.ends_at) }}</td>
                        <td class="px-4 py-2">{{ ev.location ?? "-" }}</td>
                        <td class="px-4 py-2">
                            <span class="text-gray-600">{{ ev.notes || "-" }}</span>
                        </td>
                        <td class="px-4 py-2">
                            <Link
                                :href="route('attendance.edit', ev.id)"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                            >
                                Aanwezigheid
                            </Link>
                        </td>
                    </tr>

                    <tr v-if="events.length === 0">
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Geen trainingen gevonden.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
