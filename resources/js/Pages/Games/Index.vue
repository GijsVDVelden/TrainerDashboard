    <script setup>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import PageHeader from "@/Components/PageHeader.vue";
    import { Head, Link } from "@inertiajs/vue3";
    import { Plus, Users, ClipboardCheck } from "lucide-vue-next";

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
            <div class="space-y-6">

                <PageHeader title="Wedstrijden" />

                <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

                    <!-- Filter tabs -->
                    <div class="inline-flex w-full sm:w-auto rounded-md border border-gray-200 overflow-hidden">
                        <Link
                            :href="route('games.index', { filter: 'upcoming' })"
                            :class="[
                                'flex-1 sm:flex-none px-3 py-1.5 text-sm font-medium text-center',
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
                                'flex-1 sm:flex-none px-3 py-1.5 text-sm font-medium border-l border-gray-200 text-center',
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
                        class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium gap-2"
                    >
                        <Plus :size="18" />
                        Nieuwe wedstrijd
                    </Link>
                </div>

                    <!-- Mobile Cards (hidden on lg+) -->
                    <div class="lg:hidden space-y-4">
                        <div
                            v-for="ev in events"
                            :key="ev.id"
                            class="border border-gray-200 rounded-lg p-4 space-y-3"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg">{{ ev.game?.opponent ?? "-" }}</h3>
                                    <p class="text-sm text-gray-600">{{ ev.game?.home ? "Thuis" : "Uit" }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-md" :class="ev.game?.home ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'">
                                    {{ ev.game?.home ? "Thuis" : "Uit" }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Datum:</span>
                                    <p class="font-medium">{{ fmtDate(ev.starts_at) }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Tijd:</span>
                                    <p class="font-medium">{{ fmtTime(ev.starts_at) }} - {{ fmtTime(ev.ends_at) }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-600">Locatie:</span>
                                    <p class="font-medium">{{ ev.location ?? "-" }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-gray-200">
                                <Link
                                    :href="route('attendance.edit', ev.id)"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium text-sm gap-2"
                                >
                                    <Users :size="16" />
                                    Aanwezigheid
                                </Link>
                                <Link
                                    :href="route('games.evaluate', ev.id)"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium text-sm gap-2"
                                >
                                    <ClipboardCheck :size="16" />
                                    Evalueer
                                </Link>
                            </div>
                        </div>
                        <div v-if="events.length === 0" class="px-4 py-6 text-center text-gray-500">
                            Geen wedstrijden gevonden.
                        </div>
                    </div>

                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="hidden lg:block overflow-hidden border border-gray-200 rounded-lg">
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
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium gap-1"
                                    >
                                        <Users :size="16" />
                                        Aanwezigheid
                                    </Link>
                                    <Link
                                        :href="route('games.evaluate', ev.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium gap-1"
                                    >
                                        <ClipboardCheck :size="16" />
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
            </div>
        </AuthenticatedLayout>
    </template>
