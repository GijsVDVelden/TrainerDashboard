<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Home, Users, Goal, Volleyball, Settings } from 'lucide-vue-next'

// Huidige route naam via Ziggy
const current = computed(() => route().current())

// Definieer de tabs hier centraal
const tabs = [
    { name: 'dashboard',        label: 'Dashboard',  icon: Home,      href: () => route('dashboard') },
    { name: 'players.*',        label: 'Spelers',    icon: Users,     href: () => route('players.index') },
    { name: 'practices.*',      label: 'Trainingen', icon: Goal,      href: () => route('practices.index') },
    { name: 'games.*',        label: 'Wedstrijden',icon: Volleyball, href: () => route('games.index') },
    { name: 'settings',         label: 'Instellingen', icon: Settings, href: () => route('settings') },
]

function isActive(pattern) {
    // Als pattern eindigt op .* gebruiken we Ziggy wildcard
    if (pattern.endsWith('.*')) {
        return route().current(pattern)
    }
    return route().current(pattern)
}
</script>

<template>
    <!-- Alleen tonen op mobiel -->
    <nav
        class="sm:hidden fixed bottom-0 inset-x-0 z-40 border-t bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60"
        role="navigation"
        aria-label="Mobiele navigatie"
        :style="{ paddingBottom: 'env(safe-area-inset-bottom)' }"
    >
        <ul class="grid grid-cols-5 p-1">
            <li v-for="tab in tabs" :key="tab.name">
                <Link
                    :href="tab.href()"
                    class="flex flex-col items-center justify-center gap-1 py-2.5"
                    :aria-current="isActive(tab.name) ? 'page' : undefined"
                >
                    <component
                        :is="tab.icon"
                        class="h-5 w-5"
                        :class="isActive(tab.name) ? 'text-blue-600' : 'text-gray-500'"
                    />
                    <span
                        class="text-[11px] leading-none font-medium"
                        :class="isActive(tab.name) ? 'text-blue-600' : 'text-gray-500'"
                    >
            {{ tab.label }}
          </span>
                </Link>
            </li>
        </ul>
    </nav>
</template>
