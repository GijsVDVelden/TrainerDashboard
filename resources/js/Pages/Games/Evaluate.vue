<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { watch, ref } from 'vue'
import { Save, X, Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps({
    event: Object,
    game: Object,
    players: Array,
    stats: Object,
    defaults: Object,
})

const form = useForm({
    our_score: props.defaults.our_score ?? 0,
    opponent_score: props.defaults.opponent_score ?? 0,
    goals: props.defaults.goals ?? [],
    cards: props.defaults.cards ?? {},
})

// Initialiseer cards object voor alle spelers
props.players.forEach(player => {
    if (!form.cards[player.id]) {
        form.cards[player.id] = { yellow: 0, red: 0 }
    }
})

// Sync goals met score
watch(
    () => form.our_score,
    newVal => {
        const score = parseInt(newVal) || 0
        if (score > form.goals.length) {
            for (let i = form.goals.length; i < score; i++) {
                form.goals.push({ scorer: '', assist: '' })
            }
        } else if (score < form.goals.length) {
            form.goals.splice(score)
        }
    },
    { immediate: true }
)

function addYellowCard(playerId) {
    if (!form.cards[playerId]) {
        form.cards[playerId] = { yellow: 0, red: 0 }
    }
    
    // Check of speler al 2 gele kaarten heeft
    if (form.cards[playerId].yellow >= 2) {
        return // Max 2 gele kaarten
    }
    
    form.cards[playerId].yellow++
    
    // Bij 2 gele kaarten automatisch een rode kaart geven
    if (form.cards[playerId].yellow === 2) {
        form.cards[playerId].red++
    }
}

function addRedCard(playerId) {
    if (!form.cards[playerId]) {
        form.cards[playerId] = { yellow: 0, red: 0 }
    }
    form.cards[playerId].red++
}

function removeYellowCard(playerId) {
    if (form.cards[playerId] && form.cards[playerId].yellow > 0) {
        // Als er 2 gele kaarten waren, verwijder ook de automatische rode kaart
        if (form.cards[playerId].yellow === 2 && form.cards[playerId].red > 0) {
            form.cards[playerId].red--
        }
        form.cards[playerId].yellow--
    }
}

function removeRedCard(playerId) {
    if (form.cards[playerId] && form.cards[playerId].red > 0) {
        form.cards[playerId].red--
    }
}

function submit() {
    form.put(route('games.storeEvaluation', props.event.id))
}
</script>

<template>
    <Head :title="`Evaluatie - ${game?.opponent}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">

            <PageHeader
                :title="`Evaluatie: ${game?.opponent}`"
                :back-route="route('games.index')"
            />

            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Scoreboard -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Uitslag</h2>
                        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-700 mb-2">{{ event.team?.name || 'Ons' }}</p>
                                <input
                                    type="number"
                                    min="0"
                                    v-model.number="form.our_score"
                                    class="w-full text-4xl font-bold text-center border-2 border-gray-300 rounded-lg py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                />
                            </div>
                            <div class="flex items-center justify-center">
                                <span class="text-4xl font-bold text-gray-300">:</span>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-700 mb-2">{{ game?.opponent }}</p>
                                <input
                                    type="number"
                                    min="0"
                                    v-model.number="form.opponent_score"
                                    class="w-full text-4xl font-bold text-center border-2 border-gray-300 rounded-lg py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Doelpunten -->
                    <div v-if="form.our_score > 0">
                        <h2 class="text-lg font-semibold mb-4">Doelpuntenmakers</h2>
                        <div class="space-y-3">
                            <div
                                v-for="(goal, index) in form.goals"
                                :key="index"
                                class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                            >
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 flex items-center justify-center bg-green-100 text-green-700 rounded-full font-semibold text-sm">
                                        {{ index + 1 }}
                                    </div>
                                    <span class="font-medium text-gray-700">Goal {{ index + 1 }}</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Doelpuntenmaker</label>
                                        <select
                                            v-model="goal.scorer"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                        >
                                            <option value="">-- Kies speler --</option>
                                            <option v-for="p in players" :key="p.id" :value="p.id">
                                                {{ p.first_name }} {{ p.last_name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Assist</label>
                                        <select
                                            v-model="goal.assist"
                                            :disabled="!goal.scorer"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        >
                                            <option value="">-- Geen assist --</option>
                                            <option v-for="p in players" :key="p.id" :value="p.id" :disabled="p.id === goal.scorer">
                                                {{ p.first_name }} {{ p.last_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
                        Voer eerst een score in om doelpuntenmakers toe te voegen
                    </div>

                    <!-- Kaarten -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Kaarten</h2>
                        
                        <!-- Kaart selectie -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">🟨 Gele kaart</label>
                                <select
                                    @change="e => { if(e.target.value) { addYellowCard(e.target.value); e.target.value='' } }"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                >
                                    <option value="">-- Kies speler --</option>
                                    <option v-for="p in players" :key="p.id" :value="p.id">
                                        {{ p.first_name }} {{ p.last_name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">🟥 Rode kaart</label>
                                <select
                                    @change="e => { if(e.target.value) { addRedCard(e.target.value); e.target.value='' } }"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                >
                                    <option value="">-- Kies speler --</option>
                                    <option v-for="p in players" :key="p.id" :value="p.id">
                                        {{ p.first_name }} {{ p.last_name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Overzicht gegeven kaarten -->
                        <div
                            v-if="Object.values(form.cards).some(c => c.yellow > 0 || c.red > 0)"
                            class="space-y-2"
                        >
                            <p class="text-sm font-medium text-gray-600 mb-2">Gegeven kaarten:</p>
                            <div
                                v-for="player in players.filter(p => form.cards[p.id]?.yellow > 0 || form.cards[p.id]?.red > 0)"
                                :key="player.id"
                                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50"
                            >
                                <span class="font-medium text-gray-700">
                                    {{ player.first_name }} {{ player.last_name }}
                                </span>
                                <div class="flex items-center gap-4">
                                    <div v-if="form.cards[player.id]?.yellow > 0" class="flex items-center gap-2">
                                        <span class="text-sm font-semibold">{{ form.cards[player.id].yellow }}x 🟨</span>
                                        <button
                                            type="button"
                                            @click="removeYellowCard(player.id)"
                                            class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 hover:bg-gray-300 text-xs font-bold"
                                        >
                                            −
                                        </button>
                                    </div>
                                    <div v-if="form.cards[player.id]?.red > 0" class="flex items-center gap-2">
                                        <span class="text-sm font-semibold">{{ form.cards[player.id].red }}x 🟥</span>
                                        <button
                                            type="button"
                                            @click="removeRedCard(player.id)"
                                            class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 hover:bg-gray-300 text-xs font-bold"
                                        >
                                            −
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg border border-gray-200 text-sm">
                            Nog geen kaarten gegeven
                        </div>
                    </div>

                    <!-- Knoppen -->
                    <div class="flex gap-2 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium inline-flex items-center gap-2 disabled:opacity-50"
                        >
                            <Save :size="16" />
                            Evaluatie opslaan
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

