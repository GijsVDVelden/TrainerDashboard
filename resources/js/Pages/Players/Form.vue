<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    positions: Array, // [{ key: "Keeper", label: "Keeper" }, ...]
    player: {
        type: Object,
        default: null,
    },
});

const isEdit = props.player !== null;

const form = useForm({
    first_name: props.player?.first_name ?? "",
    last_name: props.player?.last_name ?? "",
    position: props.player?.position ?? "", // komt overeen met "Keeper", etc.
    birth_date: props.player?.birth_date ?? "",
});

function submit() {
    if (isEdit) {
        form.put(route("players.update", props.player.id));
    } else {
        form.post(route("players.store"));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Speler Bewerken' : 'Speler Toevoegen'" />

    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">
                    {{ isEdit ? "Speler bewerken" : "Speler toevoegen" }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Voornaam -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Voornaam</label>
                    <input
                        type="text"
                        v-model="form.first_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    />
                    <p v-if="form.errors.first_name" class="text-red-600 text-sm mt-1">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <!-- Achternaam -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Achternaam</label>
                    <input
                        type="text"
                        v-model="form.last_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    />
                    <p v-if="form.errors.last_name" class="text-red-600 text-sm mt-1">
                        {{ form.errors.last_name }}
                    </p>
                </div>

                <!-- Positie dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Positie</label>
                    <select
                        v-model="form.position"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    >
                        <option disabled value="">Selecteer een positie</option>
                        <option
                            v-for="pos in positions"
                            :key="pos.key"
                            :value="pos.key"
                        >
                            {{ pos.label }}
                        </option>
                    </select>
                    <p v-if="form.errors.position" class="text-red-600 text-sm mt-1">
                        {{ form.errors.position }}
                    </p>
                </div>

                <!-- Geboortedatum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Geboortedatum</label>
                    <input
                        type="date"
                        v-model="form.birth_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2"
                        required
                    />
                    <p v-if="form.errors.birth_date" class="text-red-600 text-sm mt-1">
                        {{ form.errors.birth_date }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                    >
                        {{ isEdit ? "Speler bijwerken" : "Speler toevoegen" }}
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
    </AuthenticatedLayout>
</template>
