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
            notes: null,
        }),
    },
});

const form = useForm({
    team_id: props.defaults.team_id ?? "",
    starts_at: props.defaults.starts_at ?? "",
    ends_at: props.defaults.ends_at ?? "",
    location: props.defaults.location ?? "",
    notes: props.defaults.notes ?? "",
});

function submit() {
    console.log("Form data:", form);
    form.post(route("practices.store"));
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
    <Head title="Training toevoegen" />
    <AuthenticatedLayout>
        <div class="p-8 text-gray-900 bg-white rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold">Training toevoegen</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-4">

                <input v-if="form.team_id && teams.length === 0" type="hidden" v-model="form.team_id" />

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
                    <input type="text" v-model="form.location" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                    <p v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</p>
                </div>

                <!-- Notities -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                    <textarea
                        v-model="form.notes"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Bijv. focus: positiespel, hesjes mee, ..."
                    />
                    <p v-if="form.errors.notes" class="text-red-600 text-sm mt-1">{{ form.errors.notes }}</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium">
                        Training opslaan
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
