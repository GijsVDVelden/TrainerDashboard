<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    players: Array,
    positions: Array,
});

const destroyForm = useForm({});

function destroyPlayer(id) {
    if (confirm('Weet je zeker dat je deze speler wilt verwijderen?')) {
        destroyForm.delete(route('players.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Spelers</h1>
                <Link
                    :href="route('players.create')"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium"
                >Nieuwe speler</Link>
            </div>

            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="w-full">
                    <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-2 font-medium text-gray-600">Voornaam</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Achternaam</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Positie</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Geboortedatum</th>
                        <th class="px-4 py-2 font-medium text-gray-600 text-center">Acties</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ player.first_name }}</td>
                        <td class="px-4 py-2">{{ player.last_name }}</td>
                        <td class="px-4 py-2">
                            {{ typeof player.position === 'string' ? player.position : player.position?.value ?? player.position }}
                        </td>
                        <td class="px-4 py-2">
                            {{ new Date(player.birth_date).toLocaleDateString() }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex justify-center gap-2">
                                <Link
                                    :href="route('players.edit', player.id)"
                                    class="px-3 py-1.5 rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 font-medium"
                                >Bewerken</Link>
                                <button
                                    class="px-3 py-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 font-medium"
                                    @click="destroyPlayer(player.id)"
                                >Verwijderen</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
