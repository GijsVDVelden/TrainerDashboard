<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import PageHeader from "@/Components/PageHeader.vue"

// ✅ Icons
import { Trophy, Handshake, Square } from 'lucide-vue-next'

const props = defineProps({
    player: Object,
    stats: Object,
    attendances: Array
})

// Helper bereken stats
function calcStats(list) {
    const total = list.length
    if (total === 0) return { present: 0, absent: 0, late: 0, total: 0, presentPct: 0, absentPct: 0, latePct: 0 }

    const present = list.filter(a => a.status === 'Aanwezig').length
    const absent = list.filter(a => a.status === 'Afwezig').length
    const late = list.filter(a => a.status === 'Laat').length

    return {
        present,
        absent,
        late,
        total,
        presentPct: Math.round((present / total) * 100),
        absentPct: Math.round((absent / total) * 100),
        latePct: Math.round((late / total) * 100),
    }
}

// Splits per type
const trainingAttendances = computed(() => props.attendances.filter(a => a.event.type === 'Training'))
const matchAttendances = computed(() => props.attendances.filter(a => a.event.type === 'Wedstrijd'))

const trainingStats = computed(() => calcStats(trainingAttendances.value))
const matchStats = computed(() => calcStats(matchAttendances.value))
</script>

<template>
    <Head :title="`Speler - ${player.first_name} ${player.last_name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- ✅ PageHeader -->
            <PageHeader
                :title="`${player.first_name} ${player.last_name}`"
                :back-route="route('players.index')"
            />

            <div class="p-6 bg-white rounded-lg shadow-sm space-y-8">

                <!-- Statistieken -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Statistieken</h2>
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-center"><Trophy class="inline w-4 h-4 text-green-600" /> Goals</th>
                            <th class="px-3 py-2 text-center"><Handshake class="inline w-4 h-4 text-blue-600" /> Assists</th>
                            <th class="px-3 py-2 text-center"><Square class="inline w-4 h-4 text-yellow-500 fill-yellow-500" /> Geel</th>
                            <th class="px-3 py-2 text-center"><Square class="inline w-4 h-4 text-red-600 fill-red-600" /> Rood</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center font-semibold">
                            <td class="px-3 py-3 text-green-700">{{ stats.goals }}</td>
                            <td class="px-3 py-3 text-blue-700">{{ stats.assists }}</td>
                            <td class="px-3 py-3 text-yellow-700">{{ stats.yellow_cards }}</td>
                            <td class="px-3 py-3 text-red-700">{{ stats.red_cards }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Aanwezigheid: Trainingen -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Aanwezigheid Trainingen ({{ trainingStats.total }})</h2>
                    <div class="flex justify-around">
                        <!-- Donut Aanwezig -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-green-700"
                                :style="{
                                  background: `conic-gradient(#16a34a ${trainingStats.presentPct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ trainingStats.presentPct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Aanwezig</p>
                        </div>
                        <!-- Donut Laat -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-yellow-700"
                                :style="{
                                  background: `conic-gradient(#eab308 ${trainingStats.latePct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ trainingStats.latePct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Laat</p>
                        </div>
                        <!-- Donut Afwezig -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-red-700"
                                :style="{
                                  background: `conic-gradient(#dc2626 ${trainingStats.absentPct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ trainingStats.absentPct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Afwezig</p>
                        </div>
                    </div>
                </div>

                <!-- Aanwezigheid: Wedstrijden -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Aanwezigheid Wedstrijden ({{ matchStats.total }})</h2>
                    <div class="flex justify-around">
                        <!-- Donut Aanwezig -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-green-700"
                                :style="{
                                  background: `conic-gradient(#16a34a ${matchStats.presentPct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ matchStats.presentPct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Aanwezig</p>
                        </div>
                        <!-- Donut Laat -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-yellow-700"
                                :style="{
                                  background: `conic-gradient(#eab308 ${matchStats.latePct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ matchStats.latePct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Laat</p>
                        </div>
                        <!-- Donut Afwezig -->
                        <div class="flex flex-col items-center">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center text-sm font-bold text-red-700"
                                :style="{
                                  background: `conic-gradient(#dc2626 ${matchStats.absentPct * 3.6}deg, #e5e7eb 0)`
                                }"
                            >
                                {{ matchStats.absentPct }}%
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Afwezig</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
