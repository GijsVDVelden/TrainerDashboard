<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Link } from '@inertiajs/vue3';
import { Calendar, Users, Trophy, CalendarDays, TrendingUp, MapPin } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    attendanceStats: Array,
    totalTrainings: Number,
    upcomingEvents: Array,
    recentEvents: Array,
    totalPlayers: Number,
    totalGames: Number,
    upcomingGames: Number,
});

// Calendar state
const currentDate = ref(new Date());
const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());

// Get days in month
const daysInMonth = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    const days = [];
    
    // Add empty cells for days before month starts
    for (let i = 0; i < startingDayOfWeek; i++) {
        days.push(null);
    }
    
    // Add all days in month
    for (let day = 1; day <= daysInMonth; day++) {
        days.push(day);
    }
    
    return days;
});

// Get events for a specific day
function getEventsForDay(day) {
    if (!day) return [];
    
    const dateToCheck = new Date(currentYear.value, currentMonth.value, day);
    dateToCheck.setHours(0, 0, 0, 0);
    
    return props.upcomingEvents.filter(event => {
        const eventDate = new Date(event.starts_at);
        eventDate.setHours(0, 0, 0, 0);
        return eventDate.getTime() === dateToCheck.getTime();
    });
}

// Check if day is today
function isToday(day) {
    if (!day) return false;
    const today = new Date();
    return day === today.getDate() && 
           currentMonth.value === today.getMonth() && 
           currentYear.value === today.getFullYear();
}

// Navigate months
function previousMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value - 1);
}

function nextMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value + 1);
}

// Format date/time
function formatDate(datetime) {
    return new Date(datetime).toLocaleDateString('nl-NL', { 
        weekday: 'short', 
        day: 'numeric', 
        month: 'short' 
    });
}

function formatTime(datetime) {
    return new Date(datetime).toLocaleTimeString('nl-NL', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// Month names
const monthNames = [
    'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni',
    'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'
];

const dayNames = ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'];
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <PageHeader title="Dashboard" />

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Players -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Spelers</p>
                            <p class="text-3xl font-bold text-gray-900">{{ totalPlayers }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <Users class="w-6 h-6 text-blue-700" />
                        </div>
                    </div>
                </div>

                <!-- Total Trainings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Trainingen</p>
                            <p class="text-3xl font-bold text-gray-900">{{ totalTrainings }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <CalendarDays class="w-6 h-6 text-green-700" />
                        </div>
                    </div>
                </div>

                <!-- Total Games -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Wedstrijden</p>
                            <p class="text-3xl font-bold text-gray-900">{{ totalGames }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                            <Trophy class="w-6 h-6 text-amber-700" />
                        </div>
                    </div>
                </div>

                <!-- Upcoming Games -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Aankomende wedstrijden</p>
                            <p class="text-3xl font-bold text-gray-900">{{ upcomingGames }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <TrendingUp class="w-6 h-6 text-purple-700" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Calendar -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ monthNames[currentMonth] }} {{ currentYear }}
                        </h2>
                        <div class="flex gap-2">
                            <button 
                                @click="previousMonth"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium"
                            >
                                ←
                            </button>
                            <button 
                                @click="nextMonth"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium"
                            >
                                →
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-2">
                        <!-- Day names -->
                        <div 
                            v-for="day in dayNames" 
                            :key="day"
                            class="text-center text-xs font-medium text-gray-600 pb-2"
                        >
                            {{ day }}
                        </div>

                        <!-- Calendar days -->
                        <div
                            v-for="(day, index) in daysInMonth"
                            :key="index"
                            :class="[
                                'min-h-20 p-1 border rounded-md',
                                day ? 'bg-white hover:bg-gray-50' : 'bg-gray-50',
                                isToday(day) ? 'border-blue-500 border-2' : 'border-gray-200'
                            ]"
                        >
                            <div v-if="day" class="h-full flex flex-col">
                                <span :class="[
                                    'text-sm font-medium mb-1',
                                    isToday(day) ? 'text-blue-700' : 'text-gray-700'
                                ]">
                                    {{ day }}
                                </span>
                                
                                <!-- Events for this day -->
                                <div class="space-y-1 flex-1">
                                    <div
                                        v-for="event in getEventsForDay(day)"
                                        :key="event.id"
                                        :class="[
                                            'text-xs px-1.5 py-0.5 rounded truncate',
                                            event.type === 'match' 
                                                ? 'bg-amber-100 text-amber-800' 
                                                : 'bg-green-100 text-green-800'
                                        ]"
                                        :title="event.type === 'match' 
                                            ? `${event.game.home ? 'Thuis' : 'Uit'} vs ${event.game.opponent}` 
                                            : 'Training'"
                                    >
                                        {{ formatTime(event.starts_at) }}
                                        {{ event.type === 'match' ? event.game.opponent : 'Training' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Aankomende evenementen</h2>
                    
                    <div v-if="upcomingEvents.length === 0" class="text-center py-8 text-gray-500">
                        Geen aankomende evenementen
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="event in upcomingEvents.slice(0, 5)"
                            :key="event.id"
                            :class="[
                                'p-3 rounded-lg border',
                                event.type === 'match' 
                                    ? 'bg-amber-50 border-amber-200' 
                                    : 'bg-green-50 border-green-200'
                            ]"
                        >
                            <div class="flex items-start gap-2">
                                <Calendar :size="16" :class="event.type === 'match' ? 'text-amber-700' : 'text-green-700'" class="mt-0.5" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-900 truncate">
                                        {{ event.type === 'match' ? event.game.opponent : 'Training' }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        {{ formatDate(event.starts_at) }} • {{ formatTime(event.starts_at) }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <MapPin :size="12" />
                                        {{ event.location }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Link
                        v-if="upcomingEvents.length > 0"
                        :href="route('games.index')"
                        class="block mt-4 text-center text-sm text-blue-700 hover:text-blue-800 font-medium"
                    >
                        Bekijk alle evenementen →
                    </Link>
                </div>
            </div>

            <!-- Top Attendance -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Opkomst overzicht</h2>
                
                <div v-if="attendanceStats.length === 0" class="text-center py-8 text-gray-500">
                    Nog geen opkomst data beschikbaar
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-4 py-2 font-medium text-gray-600">Speler</th>
                                <th class="px-4 py-2 font-medium text-gray-600 text-center">Aanwezig</th>
                                <th class="px-4 py-2 font-medium text-gray-600 text-center">Te laat</th>
                                <th class="px-4 py-2 font-medium text-gray-600 text-center">Afwezig</th>
                                <th class="px-4 py-2 font-medium text-gray-600 text-center">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr 
                                v-for="stat in attendanceStats.slice(0, 10)" 
                                :key="stat.player_id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-3">{{ stat.first_name }} {{ stat.last_name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                        {{ stat.present }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                                        {{ stat.late }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                                        {{ stat.absent }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div 
                                                :style="{ width: stat.percentage + '%' }"
                                                :class="[
                                                    'h-2 rounded-full',
                                                    stat.percentage >= 80 ? 'bg-green-500' :
                                                    stat.percentage >= 60 ? 'bg-yellow-500' :
                                                    'bg-red-500'
                                                ]"
                                            ></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 w-10">{{ stat.percentage }}%</span>
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
