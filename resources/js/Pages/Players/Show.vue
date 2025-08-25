<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import PageHeader from "@/Components/PageHeader.vue";

const props = defineProps({
    player: Object,
    stats: Object,
    attendances: Array
})

// 📊 Bereken aanwezigheid percentages
const attendanceStats = computed(() => {
    const total = props.attendances.length
    if (total === 0) return { present: 0, absent: 0, late: 0, total: 0 }

    const present = props.attendances.filter(a => a.status === 'Aanwezig').length
    const absent = props.attendances.filter(a => a.status === 'Afwezig').length
    const late = props.attendances.filter(a => a.status === 'Laat').length

    return {
        present,
        absent,
        late,
        total,
        presentPct: Math.round((present / total) * 100),
        absentPct: Math.round((absent / total) * 100),
        latePct: Math.round((late / total) * 100),
    }
})
</script>

<template>
    <Head :title="`Speler - ${player.first_name} ${player.last_name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- ✅ PageHeader bovenaan -->
            <PageHeader
                :title="`${player.first_name} ${player.last_name}`"
                :back-route="route('players.index')"
            />

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div>
                    <h2 class="text-xl font-semibold mb-3">Statistieken</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="p-4 rounded-lg text-center bg-gradient-to-br from-green-100 to-green-200">
                            <p class="text-3xl font-extrabold text-green-800">{{ stats.goals }}</p>
                            <p class="text-sm text-gray-700">Goals</p>
                        </div>
                        <div class="p-4 rounded-lg text-center bg-gradient-to-br from-blue-100 to-blue-200">
                            <p class="text-3xl font-extrabold text-blue-800">{{ stats.assists }}</p>
                            <p class="text-sm text-gray-700">Assists</p>
                        </div>
                        <div class="p-4 rounded-lg text-center bg-gradient-to-br from-yellow-100 to-yellow-200">
                            <p class="text-3xl font-extrabold text-yellow-800">{{ stats.yellow_cards }}</p>
                            <p class="text-sm text-gray-700">Gele kaarten</p>
                        </div>
                        <div class="p-4 rounded-lg text-center bg-gradient-to-br from-red-100 to-red-200">
                            <p class="text-3xl font-extrabold text-red-800">{{ stats.red_cards }}</p>
                            <p class="text-sm text-gray-700">Rode kaarten</p>
                        </div>
                    </div>
                </div>

                <!-- Aanwezigheid samenvatting -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Aanwezigheid</h2>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-green-100 rounded-lg text-center">
                            <p class="text-2xl font-bold text-green-700">
                                {{ attendanceStats.presentPct }}%
                            </p>
                            <p class="text-sm text-gray-600">
                                Aanwezig ({{ attendanceStats.present }})
                            </p>
                        </div>
                        <div class="p-4 bg-yellow-100 rounded-lg text-center">
                            <p class="text-2xl font-bold text-yellow-700">
                                {{ attendanceStats.latePct }}%
                            </p>
                            <p class="text-sm text-gray-600">
                                Laat ({{ attendanceStats.late }})
                            </p>
                        </div>
                        <div class="p-4 bg-red-100 rounded-lg text-center">
                            <p class="text-2xl font-bold text-red-700">
                                {{ attendanceStats.absentPct }}%
                            </p>
                            <p class="text-sm text-gray-600">
                                Afwezig ({{ attendanceStats.absent }})
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
