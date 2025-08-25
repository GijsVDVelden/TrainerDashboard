<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    players: Array,
    positions: Array,
});

const destroyForm = useForm({});

function destroyPlayer(id) {
    if (confirm("Weet je zeker dat je deze speler wilt verwijderen?")) {
        destroyForm.delete(route("players.destroy", id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">

            <PageHeader title="Selectie" />

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="w-full flex justify-end items-center mb-6">
                    <Link
                        :href="route('players.create')"
                        class="items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium"
                    >
                        Speler toevoegen
                    </Link>
                </div>

                <!-- GRID (mobiel + tablet) -->
                <div class="grid gap-4 sm:grid-cols-2 lg:hidden">
                    <div
                        v-for="player in players"
                        :key="player.id"
                        class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-sm transition"
                    >
                        <h2 class="text-lg font-semibold">
                            {{ player.first_name }} {{ player.last_name }}
                        </h2>

                        <!-- Positie -->
                        <p class="text-sm text-gray-600 mt-1">
                            {{ Array.isArray(player.positions)
                            ? player.positions.map(p => p.name).join(", ")
                            : (typeof player.position === "string"
                                ? player.position
                                : player.position?.value ?? player.position) }}
                        </p>

                        <!-- Geboortedatum -->
                        <p class="text-sm text-gray-500 mt-1">
                            Geboortedatum: {{ new Date(player.birth_date).toLocaleDateString("nl-NL") }}
                        </p>

                        <!-- Acties -->
                        <div class="mt-3 flex gap-2">
                            <Link
                                :href="route('players.edit', player.id)"
                                class="flex-1 px-3 py-1.5 text-center rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 font-medium text-sm"
                            >
                                Bewerken
                            </Link>
                            <button
                                class="flex-1 px-3 py-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 font-medium text-sm"
                                @click="destroyPlayer(player.id)"
                            >
                                Verwijderen
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABEL (desktop) -->
                <div class="hidden lg:block overflow-hidden border border-gray-200 rounded-lg bg-white">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 font-medium text-gray-600">Voornaam</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Achternaam</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Positie(s)</th>
                            <th class="px-4 py-2 font-medium text-gray-600">Geboortedatum</th>
                            <th class="px-4 py-2 font-medium text-gray-600 text-center">Acties</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ player.first_name }}</td>
                            <td class="px-4 py-2">{{ player.last_name }}</td>
                            <td class="px-4 py-2">
                                {{ Array.isArray(player.positions)
                                ? player.positions.map(p => p.name).join(", ")
                                : (typeof player.position === "string"
                                    ? player.position
                                    : player.position?.value ?? player.position) }}
                            </td>
                            <td class="px-4 py-2">
                                {{ new Date(player.birth_date).toLocaleDateString("nl-NL") }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('players.show', player.id)"
                                        class="px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200 font-medium"
                                    >
                                        Statistieken
                                    </Link>
                                    <Link
                                        :href="route('players.edit', player.id)"
                                        class="px-3 py-1.5 rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 font-medium"
                                    >
                                        Bewerken
                                    </Link>
                                    <button
                                        class="px-3 py-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 font-medium"
                                        @click="destroyPlayer(player.id)"
                                    >
                                        Verwijderen
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
