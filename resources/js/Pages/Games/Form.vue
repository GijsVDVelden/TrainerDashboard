<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { watch } from "vue";
import { Save, X } from "lucide-vue-next";

const props = defineProps({
    event: {
        type: Object,
        default: null,
    },
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

// Helper functie om datetime op te splitsen
function splitDateTime(datetime) {
    if (!datetime) return { date: "", time: "" };
    const d = new Date(datetime);
    const date = d.toISOString().split('T')[0];
    const time = d.toTimeString().slice(0, 5);
    return { date, time };
}

const startDateTime = splitDateTime(props.defaults.starts_at);
const endDateTime = splitDateTime(props.defaults.ends_at);

const form = useForm({
    team_id: props.defaults.team_id ?? "",
    game_date: startDateTime.date,
    start_time: startDateTime.time,
    end_time: endDateTime.time,
    location: props.defaults.location ?? (props.defaults.home === true ? "Sportpark Inschoten" : ""),
    opponent: props.defaults.opponent ?? "",
    home: props.defaults.home ?? true,
    notes: props.defaults.notes ?? "",
});

// Watch voor wijzigingen in home waarde
watch(() => form.home, (newValue) => {
    if (newValue === true) {
        form.location = "Sportpark Inschoten";
    } else if (newValue === false) {
        form.location = "";
    }
});

function submit() {
    const submitData = {
        team_id: form.team_id,
        starts_at: form.game_date && form.start_time ? `${form.game_date} ${form.start_time}` : null,
        ends_at: form.game_date && form.end_time ? `${form.game_date} ${form.end_time}` : null,
        location: form.location,
        opponent: form.opponent,
        home: form.home,
        notes: form.notes,
    };
    
    if (props.event) {
        form.transform(() => submitData).put(route("games.update", props.event.id));
    } else {
        form.transform(() => submitData).post(route("games.store"));
    }
}
</script>

<template>
    <Head :title="event ? 'Wedstrijd bewerken' : 'Nieuwe wedstrijd'" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <PageHeader 
                :title="event ? 'Wedstrijd bewerken' : 'Nieuwe wedstrijd'"
                :back-route="route('games.index')"
            />
            
            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="space-y-4">
                    <input type="hidden" v-model="form.team_id" />

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

                    <!-- Datum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Datum</label>
                        <input
                            type="date"
                            v-model="form.game_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            required
                        />
                        <p v-if="form.errors.game_date" class="text-red-600 text-sm mt-1">{{ form.errors.game_date }}</p>
                    </div>

                    <!-- Begintijd en Eindtijd -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Begintijd</label>
                            <input
                                type="time"
                                v-model="form.start_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                required
                            />
                            <p v-if="form.errors.start_time" class="text-red-600 text-sm mt-1">{{ form.errors.start_time }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Eindtijd</label>
                            <input
                                type="time"
                                v-model="form.end_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            />
                            <p v-if="form.errors.end_time" class="text-red-600 text-sm mt-1">{{ form.errors.end_time }}</p>
                        </div>
                    </div>
                    
                    <!-- Thuis/Uit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thuis of uit?</label>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="form.home = true"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                    form.home === true
                                        ? 'bg-green-100 text-green-700 border-2 border-green-500'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-2 border-transparent'
                                ]"
                            >
                                Thuis
                            </button>
                            <button
                                type="button"
                                @click="form.home = false"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                    form.home === false
                                        ? 'bg-blue-100 text-blue-700 border-2 border-blue-500'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-2 border-transparent'
                                ]"
                            >
                                Uit
                            </button>
                        </div>
                    </div>

                    <!-- Locatie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
                        <input
                            v-if="form.home === false"
                            type="text"
                            v-model="form.location"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2"
                            placeholder="Vul de locatie in"
                        />
                        <input
                            v-else
                            type="text"
                            v-model="form.location"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 mb-2"
                            readonly
                        />
                        <p v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium inline-flex items-center gap-2"
                        >
                            <Save :size="16" />
                            Wedstrijd opslaan
                        </button>
                        <a
                            href="#"
                            onclick="history.back(); return false;"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium inline-flex items-center gap-2"
                        >
                            <X :size="16" />
                            Annuleren
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>