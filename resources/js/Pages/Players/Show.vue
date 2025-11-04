<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import PageHeader from "@/Components/PageHeader.vue"

// Icons
import { Trophy, Handshake, Square, CheckCircle2, XCircle, Clock } from 'lucide-vue-next'

const props = defineProps({
    player: Object,
    stats: Object,
    attendances: Array
})

// Helper bereken stats
function calcStats(list) {
    const total = list.length
    if (total === 0) return { present: 0, absent: 0, late: 0, total: 0, presentPct: 0, absentPct: 0, latePct: 0 }

    const present = list.filter(a => a.status === 'Aanwezig' && !a.late).length
    const absent = list.filter(a => a.status === 'Afwezig').length
    const late = list.filter(a => a.late === true || a.late === 1).length

    return {
        present,
        absent,
        late,
        total,
        presentPct: total > 0 ? Math.round((present / total) * 100) : 0,
        absentPct: total > 0 ? Math.round((absent / total) * 100) : 0,
        latePct: total > 0 ? Math.round((late / total) * 100) : 0,
    }
}

// Splits per type
const trainingAttendances = computed(() => 
    props.attendances
        .filter(a => a.event.type === 'training')
        .sort((a, b) => new Date(b.event.starts_at) - new Date(a.event.starts_at))
)
const matchAttendances = computed(() => 
    props.attendances
        .filter(a => a.event.type === 'match')
        .sort((a, b) => new Date(b.event.starts_at) - new Date(a.event.starts_at))
)

const trainingStats = computed(() => calcStats(trainingAttendances.value))
const matchStats = computed(() => calcStats(matchAttendances.value))
</script>

<template>
    <Head :title="`Speler - ${player.first_name} ${player.last_name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <PageHeader
                :title="`${player.first_name} ${player.last_name}`"
                :back-route="route('players.index')"
            />

            <!-- Wedstrijd Statistieken -->
            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold mb-4">Wedstrijd Statistieken</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <Trophy class="w-8 h-8 text-green-600 mx-auto mb-2" />
                        <div class="text-2xl font-bold text-green-700">{{ stats.goals }}</div>
                        <div class="text-sm text-gray-600">Goals</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <Handshake class="w-8 h-8 text-blue-600 mx-auto mb-2" />
                        <div class="text-2xl font-bold text-blue-700">{{ stats.assists }}</div>
                        <div class="text-sm text-gray-600">Assists</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <Square class="w-8 h-8 text-yellow-500 fill-yellow-500 mx-auto mb-2" />
                        <div class="text-2xl font-bold text-yellow-700">{{ stats.yellow_cards }}</div>
                        <div class="text-sm text-gray-600">Gele kaarten</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <Square class="w-8 h-8 text-red-600 fill-red-600 mx-auto mb-2" />
                        <div class="text-2xl font-bold text-red-700">{{ stats.red_cards }}</div>
                        <div class="text-sm text-gray-600">Rode kaarten</div>
                    </div>
                </div>
            </div>

            <!-- Aanwezigheid Overzicht -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Trainingen -->
                <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Aanwezigheid Trainingen ({{ trainingStats.total }})</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <CheckCircle2 class="w-6 h-6 text-green-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-green-700">{{ trainingStats.presentPct }}%</div>
                            <div class="text-xs text-gray-600">{{ trainingStats.present }} Aanwezig</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <Clock class="w-6 h-6 text-yellow-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-yellow-700">{{ trainingStats.latePct }}%</div>
                            <div class="text-xs text-gray-600">{{ trainingStats.late }} Laat</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <XCircle class="w-6 h-6 text-red-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-red-700">{{ trainingStats.absentPct }}%</div>
                            <div class="text-xs text-gray-600">{{ trainingStats.absent }} Afwezig</div>
                        </div>
                    </div>
                </div>

                <!-- Wedstrijden -->
                <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Aanwezigheid Wedstrijden ({{ matchStats.total }})</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <CheckCircle2 class="w-6 h-6 text-green-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-green-700">{{ matchStats.presentPct }}%</div>
                            <div class="text-xs text-gray-600">{{ matchStats.present }} Aanwezig</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <Clock class="w-6 h-6 text-yellow-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-yellow-700">{{ matchStats.latePct }}%</div>
                            <div class="text-xs text-gray-600">{{ matchStats.late }} Laat</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <XCircle class="w-6 h-6 text-red-600 mx-auto mb-1" />
                            <div class="text-xl font-bold text-red-700">{{ matchStats.absentPct }}%</div>
                            <div class="text-xs text-gray-600">{{ matchStats.absent }} Afwezig</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
