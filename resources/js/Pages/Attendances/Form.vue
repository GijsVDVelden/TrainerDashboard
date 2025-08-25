<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    event: Object,
    players: Array,
    attendances: Object,     // keyed by player_id
    statuses: Array,         // Enum waardes (Aanwezig, Afwezig, Laat)
});

// Extra status toevoegen
const allStatuses = [...props.statuses, "Ziek"];

const form = useForm({
    attendances: props.players.map(player => ({
        player_id: player.id,
        // standaard "Aanwezig"
        status: props.attendances[player.id]?.status ?? "Aanwezig",
        notes: props.attendances[player.id]?.notes ?? "",
    })),
});

// Helpers
function setStatus(playerId, status) {
    const row = form.attendances.find(a => a.player_id === playerId);
    if (row) row.status = status;
}

function submit() {
    form.post(route("attendance.store", props.event.id));
}
</script>

<template>
    <Head :title="`Aanwezigheid - ${event.type} ${event.starts_at}`" />
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">
                    Aanwezigheid voor {{ event.type }} {{ new Date(event.starts_at).toLocaleDateString() }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 font-medium text-gray-600">Speler</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Status</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Notities</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="row in form.attendances"
                            :key="row.player_id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-4 py-2">
                                {{ players.find(p => p.id === row.player_id)?.first_name }}
                                {{ players.find(p => p.id === row.player_id)?.last_name }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 flex-wrap">
                                    <button
                                        v-for="status in allStatuses"
                                        :key="status"
                                        type="button"
                                        @click="setStatus(row.player_id, status)"
                                        :class="[
                                            'px-3 py-1.5 rounded-md text-sm font-medium',
                                            row.status === status
                                              ? status === 'Aanwezig'
                                                ? 'bg-green-600 text-white'
                                                : status === 'Afwezig'
                                                  ? 'bg-red-600 text-white'
                                                  : status === 'Laat'
                                                    ? 'bg-yellow-600 text-white'
                                                    : 'bg-blue-600 text-white' // Ziek
                                              : status === 'Aanwezig'
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : status === 'Afwezig'
                                                  ? 'bg-red-100 text-red-700 hover:bg-red-200'
                                                  : status === 'Laat'
                                                    ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                                    : 'bg-blue-100 text-blue-700 hover:bg-blue-200'
                                        ]"
                                    >
                                        {{ status }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <input
                                    type="text"
                                    v-model="row.notes"
                                    placeholder="Optionele notitie"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium">
                        Aanwezigheid opslaan
                    </button>
                    <a
                        href="#"
                        onclick="history.back(); return false;"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium"
                    >
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
