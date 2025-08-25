<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage, Link, useForm } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import MobileNav from '@/Components/MobileNav.vue'
import { Home, Users, Goal, Volleyball, Settings, LogOut } from 'lucide-vue-next'

const page = usePage()
const auth = page.props.auth || {}
const user = auth.user || null
const userTeams = auth.userTeams || []
const activeTeam = auth.activeTeam || null

const form = useForm({
    team_id: activeTeam ? activeTeam.id : null
})
function changeTeam() {
    form.post(route('active-team.set'))
}

// Trainer/team naam
const trainerName = computed(() => user?.name ?? 'Trainer')
const teamName = computed(() => activeTeam?.name ?? '—')

// Responsive flag (desktop vs mobile)
const isDesktop = ref(window.innerWidth >= 640) // Tailwind sm breakpoint
function onResize() {
    isDesktop.value = window.innerWidth >= 640
}
onMounted(() => window.addEventListener('resize', onResize))
onUnmounted(() => window.removeEventListener('resize', onResize))
</script>

<template>
    <!-- Desktop layout -->
    <div v-if="isDesktop" class="flex">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 flex-col w-64 h-screen bg-white border-r border-gray-200 flex">
            <!-- Trainer + team card -->
            <div class="bg-gradient-to-br from-slate-800 to-blue-700 text-white shadow-md">
                <div class="p-4 flex items-center gap-3">
                    <ApplicationLogo class="h-10 w-auto"/>
                    <div class="leading-tight min-w-0">
                        <div class="text-xs uppercase text-gray-400">Trainer</div>
                        <div class="font-bold">
                            {{ trainerName }}
                        </div>
                    </div>
                </div>

                <!-- Team-select -->
                <div class="p-4 border-t border-white/10">
                    <div class="text-xs uppercase text-gray-400">Team</div>
                    <div class="flex flex-row gap-4">
                        <div v-if="userTeams.length > 1" class="mt-1 relative w-full">
                            <form @submit.prevent="changeTeam">
                                <select
                                    v-model="form.team_id"
                                    @change="changeTeam"
                                    class="w-full appearance-none rounded-lg px-3 py-2 text-sm
                         bg-white/10 backdrop-blur
                         text-white placeholder-white/60
                         border border-white/20 font-bold
                         outline-none focus:ring-2 focus:ring-white/50 focus:border-white/40
                         transition"
                                >
                                    <option disabled value="" class="bg-slate-800 text-white/80">
                                        Kies een team…
                                    </option>
                                    <option
                                        v-for="team in userTeams"
                                        :key="team.id"
                                        :value="team.id"
                                        class="bg-slate-800 text-white"
                                    >
                                        {{ team.name }}{{ team.season?.name ? ` (${team.season.name})` : '' }}
                                    </option>
                                </select>
                            </form>
                        </div>
                        <div v-else class="font-medium tracking-wide truncate">
                            {{ teamName }}
                        </div>
                        <Link :href="route('settings')" class="justify-between m-auto h-5 w-5">
                            <Settings />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Navigatie -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <nav class="space-y-1">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Home class="h-5 w-5"/>
                        <span>Dashboard</span>
                    </Link>

                    <Link
                        :href="route('players.index')"
                        class="flex items-center gap-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Users class="h-5 w-5"/>
                        <span>Spelers</span>
                    </Link>

                    <Link
                        :href="route('practices.index')"
                        class="flex items-center gap-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Goal class="h-5 w-5"/>
                        <span>Trainingen</span>
                    </Link>

                    <Link
                        :href="route('players.index')"
                    class="flex items-center gap-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                    <Volleyball class="h-5 w-5"/>
                    <span>Wedstrijden</span>
                    </Link>
                </nav>
            </div>

            <!-- Logout -->
            <div class="border-t p-4">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-2 px-2 py-2 rounded text-red-600 hover:bg-red-50 hover:text-red-800 font-medium w-full justify-start"
                >
                    <LogOut class="h-5 w-5"/>
                    <span>Uitloggen</span>
                </Link>
            </div>
        </aside>

        <!-- Content rechts -->
        <main class="flex-1 ml-64 min-h-screen bg-gray-100">
            <div class="py-6 px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>

    <!-- Mobile layout -->
    <div v-else class="min-h-screen bg-gray-100 pb-16">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <slot />
        </div>
        <MobileNav />
    </div>
</template>
