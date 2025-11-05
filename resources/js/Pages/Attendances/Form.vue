<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Save, X } from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    event: Object,
    players: Array,
    attendances: Object, // keyed by player_id
    reasons: Array,      // ["Ziekte", "Vakantie", "Blessure", "Anders"]
    from: String,        // 'dashboard' of null
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

// Bepaal de back route op basis van waar je vandaan komt
const backRoute = computed(() => {
    if (props.from === 'dashboard') {
        return route('dashboard');
    }
    // Anders ga terug naar de event lijst (games of practices)
    return props.event.type === 'match' ? route('games.index') : route('practices.index');
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
        <PageHeader
            :title="'Aanwezigheid: ' + event.title"
            :back-route="backRoute"
        ></PageHeader>            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="space-y-4">
                    
                    <!-- Mobile Cards (hidden on lg+) -->
                    <div class="lg:hidden space-y-4">
                        <div
                            v-for="row in form.attendances"
                            :key="row.player_id"
                            class="border border-gray-200 rounded-lg p-4 space-y-3"
                        >
                            <h3 class="font-semibold">
                                {{ players.find(p => p.id === row.player_id)?.first_name }}
                                {{ players.find(p => p.id === row.player_id)?.last_name }}
                            </h3>
                            
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="setStatus(row.player_id, 'Aanwezig')"
                                        :class="[
                                            'flex-1 px-3 py-2 rounded-md text-sm font-medium',
                                            row.status === 'Aanwezig'
                                                ? 'bg-green-600 text-white'
                                                : 'bg-green-100 text-green-700'
                                        ]"
                                    >
                                        Aanwezig
                                    </button>
                                    <button
                                        type="button"
                                        @click="setStatus(row.player_id, 'Afwezig')"
                                        :class="[
                                            'flex-1 px-3 py-2 rounded-md text-sm font-medium',
                                            row.status === 'Afwezig'
                                                ? 'bg-red-600 text-white'
                                                : 'bg-red-100 text-red-700'
                                        ]"
                                    >
                                        Afwezig
                                    </button>
                                </div>
                            </div>

                            <!-- Te laat -->
                            <div v-if="row.status === 'Aanwezig'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Te laat</label>
                                <button
                                    type="button"
                                    @click="toggleLate(row.player_id)"
                                    :class="[
                                        'w-full px-3 py-2 rounded-md text-sm font-medium',
                                        row.late
                                            ? 'bg-yellow-600 text-white'
                                            : 'bg-yellow-100 text-yellow-700'
                                    ]"
                                >
                                    {{ row.late ? 'Ja' : 'Nee' }}
                                </button>
                            </div>

                            <!-- Reden -->
                            <div v-if="row.status === 'Afwezig'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reden</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        v-for="reason in reasons"
                                        :key="reason"
                                        type="button"
                                        @click="setReason(row.player_id, reason)"
                                        :class="[
                                            'px-3 py-2 rounded-md text-sm font-medium',
                                            row.reason === reason
                                                ? 'bg-blue-600 text-white'
                                                : 'bg-blue-100 text-blue-700'
                                        ]"
                                    >
                                        {{ reason }}
                                    </button>
                                </div>
                            </div>

                            <!-- Notities -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                                <input
                                    type="text"
                                    v-model="row.notes"
                                    placeholder="Optionele notitie"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table (hidden on mobile) -->
                <div class="hidden lg:block overflow-hidden border border-gray-200 rounded-lg">
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
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium inline-flex items-center gap-2"
                    >
                        <Save :size="16" />
                        Aanwezigheid opslaan
                    </button>
                    <a
                        href="#"
                        onclick="history.back(); return false;"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium inline-flex items-center gap-2"
                    >
                        <X :size="16" />
                        Annuleren
                    </a>
                </div>
            </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
