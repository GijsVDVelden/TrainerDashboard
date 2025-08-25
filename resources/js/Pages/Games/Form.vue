<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    defaults: {
        type: Object,
        default: () => ({
            team_id: null,
            starts_at: null,
            ends_at: null,
            location: null,
            opponent: null,
            home: true,
            notes: null,
        }),
    },
});

const form = useForm({
    team_id: props.defaults.team_id ?? "",
    starts_at: props.defaults.starts_at ?? "",
    ends_at: props.defaults.ends_at ?? "",
    location: props.defaults.location ?? "",
    opponent: props.defaults.opponent ?? "",
    home: props.defaults.home ?? true,
    notes: props.defaults.notes ?? "",
});

function submit() {
    form.post(route("games.store"));
}
</script>

<template>
    <Head title="Wedstrijd toevoegen" />
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">Wedstrijd toevoegen</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <input type="hidden" v-model="form.team_id" />
                <!-- Start -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                    <input
                        type="datetime-local"
                        v-model="form.starts_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    />
                    <p v-if="form.errors.starts_at" class="text-red-600 text-sm mt-1">{{ form.errors.starts_at }}</p>
                </div>

                <!-- Einde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Einde</label>
                    <input
                        type="datetime-local"
                        v-model="form.ends_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                    />
                    <p v-if="form.errors.ends_at" class="text-red-600 text-sm mt-1">{{ form.errors.ends_at }}</p>
                </div>

                <!-- Locatie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
                    <input
                        type="text"
                        v-model="form.location"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                    />
                    <p v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</p>
                </div>

                <!-- Tegenstander -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tegenstander</label>
                    <input
                        type="text"
                        v-model="form.opponent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    />
                    <p v-if="form.errors.opponent" class="text-red-600 text-sm mt-1">{{ form.errors.opponent }}</p>
                </div>

                <!-- Thuis/Uit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thuis of uit?</label>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="form.home = true"
                            :class="[
                                'px-4 py-2 rounded-md text-sm font-medium',
                                form.home === true
                                    ? 'bg-green-600 text-white'
                                    : 'bg-green-100 text-green-700 hover:bg-green-200'
                            ]"
                        >
                            Thuis
                        </button>
                        <button
                            type="button"
                            @click="form.home = false"
                            :class="[
                'px-4 py-2 rounded-md text-sm font-medium',
                form.home === false
                    ? 'bg-blue-600 text-white'
                    : 'bg-blue-100 text-blue-700 hover:bg-blue-200'
            ]"
                        >
                            Uit
                        </button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                    >
                        Wedstrijd opslaan
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
