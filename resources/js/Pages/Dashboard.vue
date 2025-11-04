<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Link } from '@inertiajs/vue3';
import { Calendar, Users, Trophy, CalendarDays, TrendingUp, MapPin } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
    attendanceStats: Array,
    totalTrainings: Number,
    calendarEvents: Array,
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

// Week view for mobile
const currentWeekStart = ref(getWeekStart(new Date()));

function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Adjust for Monday start
    return new Date(d.setDate(diff));
}

const weekDays = computed(() => {
    const days = [];
    const start = new Date(currentWeekStart.value);
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(start);
        date.setDate(start.getDate() + i);
        days.push(date);
    }
    
    return days;
});

// Get days in month
const daysInMonth = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    const days = [];
    
    // Convert Sunday (0) to 7, so Monday becomes 1, Sunday becomes 7
    const adjustedStartDay = startingDayOfWeek === 0 ? 6 : startingDayOfWeek - 1;
    
    // Add empty cells for days before month starts
    for (let i = 0; i < adjustedStartDay; i++) {
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
    
    return props.calendarEvents.filter(event => {
        const eventDate = new Date(event.starts_at);
        eventDate.setHours(0, 0, 0, 0);
        return eventDate.getTime() === dateToCheck.getTime();
    });
}

// Get events for a specific date (for week view)
function getEventsForDate(date) {
    const dateToCheck = new Date(date);
    dateToCheck.setHours(0, 0, 0, 0);
    
    return props.calendarEvents.filter(event => {
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

// Check if date is today (for week view)
function isDateToday(date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const checkDate = new Date(date);
    checkDate.setHours(0, 0, 0, 0);
    return today.getTime() === checkDate.getTime();
}

// Navigate months
function previousMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value - 1);
}

function nextMonth() {
    currentDate.value = new Date(currentYear.value, currentMonth.value + 1);
}

// Navigate weeks
function previousWeek() {
    const newStart = new Date(currentWeekStart.value);
    newStart.setDate(newStart.getDate() - 7);
    currentWeekStart.value = newStart;
}

function nextWeek() {
    const newStart = new Date(currentWeekStart.value);
    newStart.setDate(newStart.getDate() + 7);
    currentWeekStart.value = newStart;
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

const dayNames = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <PageHeader title="Dashboard" />

            <!-- Stats Cards -->
            <div class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-4 sm:p-6">
                    <!-- Week View (Mobile) -->
                    <div class="sm:hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Week {{ weekDays[0].getDate() }} {{ monthNames[weekDays[0].getMonth()].substring(0, 3) }} - {{ weekDays[6].getDate() }} {{ monthNames[weekDays[6].getMonth()].substring(0, 3) }}
                            </h2>
                            <div class="flex gap-2">
                                <button 
                                    @click="previousWeek"
                                    class="px-2 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium text-sm"
                                >
                                    ←
                                </button>
                                <button 
                                    @click="nextWeek"
                                    class="px-2 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium text-sm"
                                >
                                    →
                                </button>
                            </div>
                        </div>

                        <!-- Week Days -->
                        <div class="space-y-2">
                            <div
                                v-for="date in weekDays"
                                :key="date.getTime()"
                                :class="[
                                    'p-3 border rounded-lg',
                                    isDateToday(date) ? 'border-blue-500 border-2 bg-blue-50' : 'border-gray-200'
                                ]"
                            >
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p :class="[
                                            'font-semibold',
                                            isDateToday(date) ? 'text-blue-700' : 'text-gray-900'
                                        ]">
                                            {{ dayNames[date.getDay() === 0 ? 6 : date.getDay() - 1] }} {{ date.getDate() }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ monthNames[date.getMonth()] }}</p>
                                    </div>
                                    <span v-if="isDateToday(date)" class="text-xs font-medium text-blue-700 bg-blue-100 px-2 py-1 rounded">
                                        Vandaag
                                    </span>
                                </div>
                                
                                <div v-if="getEventsForDate(date).length === 0" class="text-sm text-gray-400 italic">
                                    Geen evenementen
                                </div>
                                <div v-else class="space-y-2">
                                    <div
                                        v-for="event in getEventsForDate(date)"
                                        :key="event.id"
                                        :class="[
                                            'p-2 rounded-md text-sm',
                                            event.type === 'match' 
                                                ? 'bg-amber-100 text-amber-900' 
                                                : 'bg-green-100 text-green-900'
                                        ]"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium">
                                                {{ event.type === 'match' ? event.game.opponent : 'Training' }}
                                            </span>
                                            <span class="text-xs">{{ formatTime(event.starts_at) }}</span>
                                        </div>
                                        <p class="text-xs mt-1 flex items-center gap-1">
                                            <MapPin :size="10" />
                                            {{ event.location }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Month View (Desktop) -->
                    <div class="hidden sm:block">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ monthNames[currentMonth] }} {{ currentYear }}
                            </h2>
                            <div class="flex gap-2">
                                <button 
                                    @click="previousMonth"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium text-sm"
                                >
                                    ←
                                </button>
                                <button 
                                    @click="nextMonth"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium text-sm"
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
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
