<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    events: Object, // Laravel paginator
});

// Datum formatter
function fmtDate(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return d.toLocaleDateString(); // bijv. 22-8-2025
}

// Tijd formatter
function fmtTime(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // bijv. 19:30
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Trainingen</h1>

                <!-- Optioneel: knop om nieuwe training aan te maken; weglaten als je dat nog niet hebt -->
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
                        <!-- GEEN Acties-kolom -->
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="ev in events.data" :key="ev.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ fmtDate(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.ends_at) }}</td>
                        <td class="px-4 py-2">{{ ev.location ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="text-gray-600">{{ ev.notes || '—' }}</span>
                        </td>
                    </tr>

                    <tr v-if="events.data.length === 0">
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Nog geen trainingen aangemaakt.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Eenvoudige pagination -->
            <div class="flex items-center justify-between" v-if="events.links && events.links.length > 0">
                <div class="text-sm text-gray-600">
                    Pagina {{ events.current_page }} van {{ events.last_page }}
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="(link, i) in events.links"
                        :key="i"
                        :href="link.url || ''"
                        v-html="link.label"
                        :class="[
              'px-3 py-1.5 rounded-md border',
              link.active
                ? 'bg-blue-600 text-white border-blue-600'
                : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200',
              !link.url ? 'pointer-events-none opacity-50' : ''
            ]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
