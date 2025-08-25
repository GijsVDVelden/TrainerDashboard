    <script setup>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import { Head, Link } from "@inertiajs/vue3";

    const props = defineProps({
        events: Array,   // wedstrijden met game-relatie
        filter: String,  // 'upcoming' | 'past'
    });

    // Datum formatter
    function fmtDate(dt) {
        if (!dt) return "-";
        const d = new Date(dt);
        return d.toLocaleDateString("nl-NL");
    }

    // Tijd formatter
    function fmtTime(dt) {
        if (!dt) return "-";
        const d = new Date(dt);
        return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    }

    // Helper voor actieve tab
    function isActive(val) {
        return (props.filter ?? "upcoming") === val;
    }
    </script>

    <template>
        <Head title="Wedstrijden" />
        <AuthenticatedLayout>
            <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h1 class="text-2xl font-semibold">Wedstrijden</h1>

                    <!-- Filter tabs -->
                    <div class="inline-flex rounded-md border border-gray-200 overflow-hidden">
                        <Link
                            :href="route('games.index', { filter: 'upcoming' })"
                            :class="[
                                'px-3 py-1.5 text-sm font-medium',
                                isActive('upcoming')
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            Gepland
                        </Link>
                        <Link
                            :href="route('games.index', { filter: 'past' })"
                            :class="[
                                'px-3 py-1.5 text-sm font-medium border-l border-gray-200',
                                isActive('past')
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            ]"
                        >
                            Gespeeld
                        </Link>
                    </div>

                    <!-- Nieuwe wedstrijd -->
                    <Link
                        :href="route('games.create')"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium"
                    >
                        Nieuwe wedstrijd
                    </Link>
                </div>

                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 font-medium text-gray-600">Datum</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Start</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Einde</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Tegenstander</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Thuis/Uit</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Locatie</th>
                            <th class="px-4 py-2 font-medium text-gray-600 text-center">Acties</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                        <tr v-for="ev in events" :key="ev.id" class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ fmtDate(ev.starts_at) }}</td>
                            <td class="px-4 py-2">{{ fmtTime(ev.starts_at) }}</td>
                            <td class="px-4 py-2">{{ fmtTime(ev.ends_at) }}</td>
                            <td class="px-4 py-2">{{ ev.game?.opponent ?? "-" }}</td>
                            <td class="px-4 py-2">
                                <span v-if="ev.game">{{ ev.game.home ? "Thuis" : "Uit" }}</span>
                            </td>
                            <td class="px-4 py-2">{{ ev.location ?? "-" }}</td>
                            <td class="px-4 py-2">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('attendance.edit', ev.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium"
                                    >
                                        Aanwezigheid
                                    </Link>
                                    <Link
                                        :href="route('games.evaluate', ev.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium"
                                    >
                                        Evalueer
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="events.length === 0">
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                Geen wedstrijden gevonden.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
