<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import { Square } from 'lucide-vue-next'
import {data} from "autoprefixer";

const props = defineProps({
    event: Object,
    game: Object,
    players: Array,
    stats: Object, // keyed by player_id
    defaults: Object,
})

const form = useForm({
    our_score: props.defaults.our_score ?? 0,
    opponent_score: props.defaults.opponent_score ?? 0,
    goals: props.defaults.goals ?? [],
    cards: props.defaults.cards ?? {},
})


console.log(data);

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

function submit() {
    form.put(route('games.storeEvaluation', props.event.id))
}
</script>

<template>
    <Head :title="`Evaluatie - ${new Date(event.starts_at).toLocaleDateString('nl-NL')}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">

            <!-- Titelblok -->
            <PageHeader
                :title="`Evaluatie: ${game?.opponent} (${game?.home ? 'Thuis' : 'Uit'})`"
                :back-route="route('games.index')"
            />

            <!-- Inhoud -->
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="space-y-4">

                <!-- Scoreboard -->
                <div>
                    <h2 class="text-lg font-semibold mb-3">Uitslag</h2>
                    <div class="flex justify-center items-center gap-6">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-1">DTS</p>
                            <input
                                type="number"
                                min="0"
                                v-model="form.our_score"
                                class="w-20 text-3xl font-bold text-center border rounded-md focus:ring-2 focus:ring-blue-400"
                            />
                        </div>
                        <span class="text-3xl font-bold text-gray-700">–</span>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-1">Tegenstander</p>
                            <input
                                type="number"
                                min="0"
                                v-model="form.opponent_score"
                                class="w-20 text-3xl font-bold text-center border rounded-md focus:ring-2 focus:ring-blue-400"
                            />
                        </div>
                    </div>
                </div>

                <!-- Doelpunten -->
                <div v-if="form.our_score > 0">
                    <h2 class="text-lg font-semibold mb-3">Doelpuntenmakers</h2>
                    <div class="space-y-4">
                        <div
                            v-for="(goal, index) in form.goals"
                            :key="index"
                            class="p-4 border rounded-lg bg-gray-50 space-y-3"
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

                    <!-- Selects -->
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <select
                            @change="e => { if(e.target.value) { form.cards[e.target.value].yellow++; e.target.value='' } }"
                            class="flex-1 border rounded-md px-2 py-2 text-sm focus:ring-2 focus:ring-yellow-400"
                        >
                            <option value="">🟨 Gele kaart voor...</option>
                            <option v-for="p in players" :key="p.id" :value="p.id">
                                {{ p.first_name }} {{ p.last_name }}
                            </option>
                        </select>

                        <select
                            @change="e => { if(e.target.value) { form.cards[e.target.value].red++; e.target.value='' } }"
                            class="flex-1 border rounded-md px-2 py-2 text-sm focus:ring-2 focus:ring-red-400"
                        >
                            <option value="">🟥 Rode kaart voor...</option>
                            <option v-for="p in players" :key="p.id" :value="p.id">
                                {{ p.first_name }} {{ p.last_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Overzicht -->
                    <div
                        v-if="Object.values(form.cards).some(c => c.yellow > 0 || c.red > 0)"
                        class="space-y-2"
                    >
                        <div
                            v-for="p in players.filter(p => form.cards[p.id].yellow > 0 || form.cards[p.id].red > 0)"
                            :key="p.id"
                            class="flex items-center justify-between p-3 border rounded-lg bg-gray-50"
                        >
                            <span class="font-medium">{{ p.first_name }} {{ p.last_name }}</span>
                            <div class="flex gap-3 text-sm">
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

                <!-- Knoppen -->
                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                    >
                        Opslaan
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

