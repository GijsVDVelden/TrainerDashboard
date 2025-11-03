<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    event: Object,
    players: Array,
    attendances: Object, // keyed by player_id
    reasons: Array       // ["Ziekte", "Vakantie", "Blessure", "Anders"]
});

const form = useForm({
    attendances: props.players.map(player => ({
        player_id: player.id,
        status: props.attendances[player.id]?.status ?? "Aanwezig",
        late: props.attendances[player.id]?.late ?? false,
        reason: props.attendances[player.id]?.reason ?? null,
        notes: props.attendances[player.id]?.notes ?? "",
    })),
});

// Helpers
function setStatus(playerId, status) {
    const row = form.attendances.find(a => a.player_id === playerId);
    if (row) {
        row.status = status;
        if (status === "Aanwezig") {
            row.reason = null;
        } else {
            row.late = false;
            row.reason = props.reasons[0] ?? null;
        }
    }
}

function toggleLate(playerId) {
    const row = form.attendances.find(a => a.player_id === playerId);
    if (row && row.status === "Aanwezig") {
        row.late = !row.late;
    }
}

function setReason(playerId, reason) {
    const row = form.attendances.find(a => a.player_id === playerId);
    if (row && row.status === "Afwezig") {
        row.reason = reason;
    }
}

function submit() {
    form.post(route("attendance.store", props.event.id));
}
</script>

<template>
    <Head :title="`Aanwezigheid - ${event.type} ${event.starts_at}`" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">
                        Aanwezigheid voor {{ event.type }}
                        {{ new Date(event.starts_at).toLocaleDateString("nl-NL") }}
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 font-medium text-gray-600">Speler</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Status</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Te laat</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Afwezigheidsreden</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Notities</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="row in form.attendances"
                            :key="row.player_id"
                            class="hover:bg-gray-50"
                        >
                            <!-- Naam -->
                            <td class="px-4 py-2">
                                {{ players.find(p => p.id === row.player_id)?.first_name }}
                                {{ players.find(p => p.id === row.player_id)?.last_name }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="setStatus(row.player_id, 'Aanwezig')"
                                        :class="[
                                                'px-3 py-1.5 rounded-md text-sm font-medium',
                                                row.status === 'Aanwezig'
                                                    ? 'bg-green-600 text-white'
                                                    : 'bg-green-100 text-green-700 hover:bg-green-200'
                                            ]"
                                    >
                                        Aanwezig
                                    </button>
                                    <button
                                        type="button"
                                        @click="setStatus(row.player_id, 'Afwezig')"
                                        :class="[
                                                'px-3 py-1.5 rounded-md text-sm font-medium',
                                                row.status === 'Afwezig'
                                                    ? 'bg-red-600 text-white'
                                                    : 'bg-red-100 text-red-700 hover:bg-red-200'
                                            ]"
                                    >
                                        Afwezig
                                    </button>
                                </div>
                            </td>

                            <!-- Te laat -->
                            <td class="px-4 py-2">
                                <button
                                    type="button"
                                    @click="toggleLate(row.player_id)"
                                    :disabled="row.status === 'Afwezig'"
                                    :class="[
                                            'px-3 py-1.5 rounded-md text-sm font-medium',
                                            row.status === 'Afwezig'
                                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                : row.late
                                                    ? 'bg-yellow-600 text-white'
                                                    : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                        ]"
                                >
                                    Te laat
                                </button>
                            </td>

                            <!-- Reden (alleen bij afwezig) -->
                            <td class="px-4 py-2">
                                <div v-if="row.status === 'Afwezig'" class="flex gap-2 flex-wrap">
                                    <button
                                        v-for="reason in reasons"
                                        :key="reason"
                                        type="button"
                                        @click="setReason(row.player_id, reason)"
                                        :class="[
                                                'px-3 py-1.5 rounded-md text-sm font-medium',
                                                row.reason === reason
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-blue-100 text-blue-700 hover:bg-blue-200'
                                            ]"
                                    >
                                        {{ reason }}
                                    </button>
                                </div>
                                <div v-else class="text-gray-400 text-sm italic">

                                </div>
                            </td>

                            <!-- Notes -->
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
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                    >
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
        </div>
    </AuthenticatedLayout>
</template>
