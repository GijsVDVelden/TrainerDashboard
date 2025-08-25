<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { watch } from "vue";
import { Trophy, Target, Square } from "lucide-vue-next";

const props = defineProps({
    event: Object,
    game: Object,
    players: Array,
    stats: Object, // keyed by player_id
});

const form = useForm({
    our_score: props.game?.our_score ?? 0,
    opponent_score: props.game?.opponent_score ?? 0,
    goals: [],
    cards: Object.fromEntries(
        props.players.map(p => [
            p.id,
            {
                yellow: props.stats[p.id]?.yellow_cards ?? 0,
                red: props.stats[p.id]?.red_cards ?? 0,
            },
        ])
    ),
});

// Sync goals met score
watch(
    () => form.our_score,
    (newVal) => {
        const score = parseInt(newVal) || 0;
        if (score > form.goals.length) {
            for (let i = form.goals.length; i < score; i++) {
                form.goals.push({ scorer: "", assist: "" });
            }
        } else if (score < form.goals.length) {
            form.goals.splice(score);
        }
    },
    { immediate: true }
);

function addYellow(playerId) {
    form.cards[playerId].yellow++;
}
function addRed(playerId) {
    form.cards[playerId].red++;
}
function submit() {
    form.put(route("games.update", props.event.id));
}
</script>

<template>
    <Head :title="`Evaluatie - ${new Date(event.starts_at).toLocaleDateString('nl-NL')}`" />
    <AuthenticatedLayout>
        <form
            @submit.prevent="submit"
            class="p-4 sm:p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-8"
        >
            <!-- Titel -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h1 class="text-xl sm:text-2xl font-semibold">
                    Evaluatie: {{ game?.opponent }} ({{ game?.home ? "Thuis" : "Uit" }})
                </h1>
                <p class="text-gray-500">
                    {{ new Date(event.starts_at).toLocaleDateString("nl-NL") }}
                </p>
            </div>

            <!-- Scoreboard -->
            <div class="bg-white border rounded-lg p-6 shadow-sm flex justify-around items-center text-center">
                <div>
                    <p class="text-gray-600 text-sm">DTS</p>
                    <input
                        type="number"
                        min="0"
                        v-model="form.our_score"
                        class="w-20 text-3xl font-bold text-center border rounded-md focus:ring-2 focus:ring-blue-400"
                    />
                </div>
                <span class="text-3xl font-bold text-gray-700">–</span>
                <div>
                    <p class="text-gray-600 text-sm">Tegenstander</p>
                    <input
                        type="number"
                        min="0"
                        v-model="form.opponent_score"
                        class="w-20 text-3xl font-bold text-center border rounded-md focus:ring-2 focus:ring-blue-400"
                    />
                </div>
            </div>

            <!-- Doelpunten -->
            <div v-if="form.our_score > 0">
                <h2 class="text-lg font-semibold mb-3">Doelpuntenmakers</h2>
                <div class="space-y-3">
                    <div
                        v-for="(goal, index) in form.goals"
                        :key="index"
                        class="p-4 border rounded-lg shadow-sm bg-gray-50 space-y-2"
                    >
                        <p class="text-sm font-medium text-gray-600">Goal {{ index + 1 }}</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Scorer -->
                            <select
                                v-model="goal.scorer"
                                class="flex-1 border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400"
                            >
                                <option disabled value="">-- Doelpuntenmaker --</option>
                                <option v-for="p in players" :key="p.id" :value="p.id">
                                    {{ p.first_name }} {{ p.last_name }}
                                </option>
                            </select>
                            <!-- Assist -->
                            <select
                                v-model="goal.assist"
                                class="flex-1 border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400"
                            >
                                <option value="">-- Geen assist --</option>
                                <option v-for="p in players" :key="p.id" :value="p.id">
                                    {{ p.first_name }} {{ p.last_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kaarten -->
            <div>
                <h2 class="text-lg font-semibold mb-3">Kaarten</h2>

                <!-- Actieknoppen -->
                <div class="flex gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <Square class="w-5 h-5 text-yellow-500 fill-yellow-500" />
                        <select
                            @change="e => { if(e.target.value) { form.cards[e.target.value].yellow++; e.target.value='' } }"
                            class="border rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400"
                        >
                            <option value="">Geef gele kaart aan...</option>
                            <option v-for="p in players" :key="p.id" :value="p.id">
                                {{ p.first_name }} {{ p.last_name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <Square class="w-5 h-5 text-red-600 fill-red-600" />
                        <select
                            @change="e => { if(e.target.value) { form.cards[e.target.value].red++; e.target.value='' } }"
                            class="border rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400"
                        >
                            <option value="">Geef rode kaart aan...</option>
                            <option v-for="p in players" :key="p.id" :value="p.id">
                                {{ p.first_name }} {{ p.last_name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Overzicht van gegeven kaarten -->
                <div v-if="Object.values(form.cards).some(c => c.yellow > 0 || c.red > 0)" class="space-y-2">
                    <div
                        v-for="p in players.filter(p => form.cards[p.id].yellow > 0 || form.cards[p.id].red > 0)"
                        :key="p.id"
                        class="flex items-center justify-between p-3 border rounded-lg bg-gray-50"
                    >
                        <span class="font-medium">{{ p.first_name }} {{ p.last_name }}</span>
                        <div class="flex gap-2 text-sm">
                <span v-if="form.cards[p.id].yellow > 0" class="flex items-center gap-1 text-yellow-700">
                    🟨 {{ form.cards[p.id].yellow }}
                </span>
                            <span v-if="form.cards[p.id].red > 0" class="flex items-center gap-1 text-red-700">
                    🟥 {{ form.cards[p.id].red }}
                </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                >
                    Evaluatie opslaan
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
    </AuthenticatedLayout>
</template>
