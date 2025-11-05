<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, useForm } from "@inertiajs/vue3";
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
            notes: null,
        }),
    },
});

// Helper functie om datetime op te splitsen
function splitDateTime(datetime) {
    if (!datetime) return { date: "", time: "" };
    const d = new Date(datetime);
    
    // Gebruik lokale tijd in plaats van UTC
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return { 
        date: `${year}-${month}-${day}`, 
        time: `${hours}:${minutes}` 
    };
}

const startDateTime = splitDateTime(props.defaults.starts_at);
const endDateTime = splitDateTime(props.defaults.ends_at);

const form = useForm({
    team_id: props.defaults.team_id ?? "",
    practice_date: startDateTime.date,
    start_time: startDateTime.time,
    end_time: endDateTime.time,
    location: props.defaults.location ?? "",
    notes: props.defaults.notes ?? "",
});

function submit() {
    // Combineer datum en tijden tot datetime strings
    const submitData = {
        team_id: form.team_id,
        starts_at: form.practice_date && form.start_time ? `${form.practice_date} ${form.start_time}` : null,
        ends_at: form.practice_date && form.end_time ? `${form.practice_date} ${form.end_time}` : null,
        location: form.location,
        notes: form.notes,
    };
    
    console.log("Form data:", submitData);
    
    if (props.event) {
        form.transform(() => submitData).put(route("practices.update", props.event.id));
    } else {
        form.transform(() => submitData).post(route("practices.store"));
    }
}


// helper om 'datetime-local' netjes te binden (indien je ISO uit backend geeft)
function toLocalInput(dt) {
    if (!dt) return "";
    const d = new Date(dt);
    // yyyy-MM-ddThh:mm
    return [
        d.getFullYear(),
        String(d.getMonth() + 1).padStart(2, "0"),
        String(d.getDate()).padStart(2, "0"),
    ].join("-") + "T" + [String(d.getHours()).padStart(2, "0"), String(d.getMinutes()).padStart(2, "0")].join(":");
}
</script>

<template>
    <Head :title="event ? 'Training bewerken' : 'Nieuwe training'" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <PageHeader 
                :title="event ? 'Training bewerken' : 'Nieuwe training'"
                :back-route="route('practices.index')"
            />
            
            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <form @submit.prevent="submit" class="space-y-4">

                <input type="hidden" v-model="form.team_id" />

                <!-- Datum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Datum</label>
                    <input
                        type="date"
                        v-model="form.practice_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        required
                    />
                    <p v-if="form.errors.practice_date" class="text-red-600 text-sm mt-1">{{ form.errors.practice_date }}</p>
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

                <!-- Locatie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
                    <input type="text" v-model="form.location" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                    <p v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</p>
                </div>

                <!-- Notities -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                    <textarea
                        v-model="form.notes"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md resize-none"
                        placeholder="Bijv. focus: positiespel, hesjes mee, ..."
                    />
                    <p v-if="form.errors.notes" class="text-red-600 text-sm mt-1">{{ form.errors.notes }}</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium inline-flex items-center gap-2">
                        <Save :size="16" />
                        Training opslaan
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
