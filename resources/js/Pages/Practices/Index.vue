<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageHeader from "@/Components/PageHeader.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Plus, Users, Edit, Trash2 } from "lucide-vue-next";
import { ref } from "vue";

const props = defineProps({
    events: Array,   // gewone array, geen paginator meer
    filter: String,  // 'upcoming' | 'past' (of undefined)
});

const deleteForm = useForm({});
const showDeleteModal = ref(false);
const eventToDelete = ref(null);

function confirmDelete(event) {
    eventToDelete.value = event;
    showDeleteModal.value = true;
}

function destroyEvent() {
    if (eventToDelete.value) {
        deleteForm.delete(route("events.destroy", eventToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                eventToDelete.value = null;
            }
        });
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    eventToDelete.value = null;
}

// Datum formatter
function fmtDate(dt) {
    if (!dt) return "-";
    const d = new Date(dt);
    return d.toLocaleDateString();
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

// Check of evenement verlopen is (gebruik ends_at of starts_at als er geen eindtijd is)
function isPast(event) {
    const checkTime = event.ends_at || event.starts_at;
    return new Date(checkTime) < new Date();
}
</script>

<template>
    <Head title="Trainingen" />
    <AuthenticatedLayout>
        <div class="space-y-6">

            <PageHeader title="Trainingen" />

            <div class="p-4 sm:p-6 bg-white rounded-lg shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

                <!-- Filter tabs -->
                <div class="inline-flex w-full sm:w-auto rounded-md border border-gray-200 overflow-hidden">
                    <Link
                        :href="route('practices.index', { filter: 'upcoming' })"
                        :class="[
                            'flex-1 sm:flex-none px-3 py-1.5 text-sm font-medium text-center',
                            isActive('upcoming') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Gepland
                    </Link>
                    <Link
                        :href="route('practices.index', { filter: 'past' })"
                        :class="[
                            'flex-1 sm:flex-none px-3 py-1.5 text-sm font-medium border-l border-gray-200 text-center',
                            isActive('past') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                    >
                        Verlopen
                    </Link>
                </div>

                <!-- Nieuwe training -->
                <Link
                    :href="route('practices.create', { type: 'training' })"
                    class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium gap-2"
                >
                    <Plus :size="18" />
                    Nieuwe training
                </Link>
            </div>

            <!-- Mobile Cards (hidden on lg+) -->
            <div class="lg:hidden space-y-4">
                <div
                    v-for="ev in events"
                    :key="ev.id"
                    class="border border-gray-200 rounded-lg p-4 space-y-3"
                >
                    <div>
                        <h3 class="font-semibold text-lg">Training</h3>
                        <p class="text-sm text-gray-600">{{ fmtDate(ev.starts_at) }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Start:</span>
                            <p class="font-medium">{{ fmtTime(ev.starts_at) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Einde:</span>
                            <p class="font-medium">{{ fmtTime(ev.ends_at) }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-600">Locatie:</span>
                            <p class="font-medium">{{ ev.location ?? "-" }}</p>
                        </div>
                        <div v-if="ev.notes" class="col-span-2">
                            <span class="text-gray-600">Notities:</span>
                            <p class="font-medium">{{ ev.notes }}</p>
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
                        <div class="grid grid-cols-2 gap-2">
                            <Link
                                v-if="!isPast(ev)"
                                :href="route('practices.edit', ev.id)"
                                class="inline-flex items-center justify-center px-3 py-2 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-200 font-medium text-sm gap-1"
                            >
                                <Edit :size="16" />
                                Bewerken
                            </Link>
                            <button
                                @click="confirmDelete(ev)"
                                :class="[
                                    'inline-flex items-center justify-center px-3 py-2 rounded-md font-medium text-sm gap-1',
                                    !isPast(ev) ? 'col-span-1' : 'col-span-2',
                                    'bg-red-100 text-red-700 hover:bg-red-200'
                                ]"
                            >
                                <Trash2 :size="16" />
                                Verwijderen
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="events.length === 0" class="px-4 py-6 text-center text-gray-500">
                    Geen trainingen gevonden.
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
                        <th class="px-4 py-2 font-medium text-gray-600">Locatie</th>
                        <th class="px-4 py-2 font-medium text-gray-600">Notities</th>
                        <th class="px-4 py-2 font-medium text-gray-600 text-center">Acties</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="ev in events" :key="ev.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ fmtDate(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.starts_at) }}</td>
                        <td class="px-4 py-2">{{ fmtTime(ev.ends_at) }}</td>
                        <td class="px-4 py-2">{{ ev.location ?? "-" }}</td>
                        <td class="px-4 py-2">
                            <span class="text-gray-600">{{ ev.notes || "-" }}</span>
                        </td>
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
                                    v-if="!isPast(ev)"
                                    :href="route('practices.edit', ev.id)"
                                    class="inline-flex items-center px-3 py-1.5 bg-amber-100 text-amber-700 rounded-md hover:bg-amber-200 font-medium gap-1"
                                >
                                    <Edit :size="16" />
                                    Bewerken
                                </Link>
                                <button
                                    @click="confirmDelete(ev)"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 font-medium gap-1"
                                >
                                    <Trash2 :size="16" />
                                    Verwijderen
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="events.length === 0">
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Geen trainingen gevonden.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
        :show="showDeleteModal"
        type="danger"
        title="Training verwijderen"
        confirm-text="Verwijderen"
        cancel-text="Annuleren"
        @confirm="destroyEvent"
        @cancel="cancelDelete"
    >
        <template #message>
            Weet je zeker dat je de training op <strong>{{ eventToDelete ? fmtDate(eventToDelete.starts_at) : '' }}</strong> wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.
        </template>
    </ConfirmModal>
</AuthenticatedLayout>
</template>