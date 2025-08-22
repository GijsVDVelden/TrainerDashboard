<script setup>
import { ref } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { Home, Users, LogOut, Goal, Volleyball } from 'lucide-vue-next'

const page = usePage()

// Data vanuit Inertia middleware
const auth = page.props.auth || {}
const user = auth.user || null
const userTeams = auth.userTeams || []
const activeTeam = auth.activeTeam || null

// Form voor team switch
const form = useForm({
    team_id: activeTeam ? activeTeam.id : null
})

function changeTeam() {
    form.post(route('active-team.set'))
}
</script>

<template>
        <div class="flex flex-row">
            <aside class="hidden sm:flex fixed flex-col w-64 h-screen bg-white border-r border-gray-200 justify-between">
                <!-- Logo + navigatie -->
                <div>
                    <div class="h-16 flex flex-row items-center justify-center border-b gap-3">
                        <ApplicationLogo class="h-10 w-auto" />
                        <div v-if="activeTeam != null">
                            {{ activeTeam.name }}
                        </div>
                    </div>

                    <div class="flex-1 p-4 space-y-4">
                    <!-- Team selector -->
                    <div v-if="userTeams.length > 1">
                        <form @submit.prevent="changeTeam">
                            <select
                                v-model="form.team_id"
                                @change="changeTeam"
                                class="w-full border rounded px-2 py-1 text-sm focus:ring focus:ring-blue-300"
                            >
                                <option
                                    v-for="team in userTeams"
                                    :key="team.id"
                                    :value="team.id"
                                >
                                    {{ team.name }} ({{ team.season?.name }})
                                </option>
                            </select>
                        </form>
                    </div>
                    <div v-else-if="activeTeam" class="px-2 text-blue-600 font-semibold">
                        {{ activeTeam.name }}
                    </div>

                    <!-- Dashboard -->
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center space-x-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Home class="h-5 w-5" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Players -->
                    <Link
                        :href="route('players.index')"
                        class="flex items-center space-x-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Users class="h-5 w-5" />
                        <span>Spelers</span>
                    </Link>

                    <!-- Practices -->
                    <Link
                        :href="route('practices.index')"
                        class="flex items-center space-x-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Goal class="h-5 w-5" />
                        <span>Trainingen</span>
                    </Link>

                    <!-- Matches -->
                    <Link
                        :href="route('players.index')"
                        class="flex items-center space-x-2 px-2 py-2 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium"
                    >
                        <Volleyball class="h-5 w-5" />
                        <span>Wedstrijden</span>
                    </Link>
                </div>

                <!-- Bottom logout -->
                    <div class="border-t p-4">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex items-center space-x-2 px-2 py-2 rounded text-red-600 hover:bg-red-50 hover:text-red-800 font-medium"
                        >
                            <LogOut class="h-5 w-5" />
                            <span>Uitloggen</span>
                        </Link>
                    </div>
                </div>
            </aside>

            <main class="flex-1 ml-64 min-h-screen">
                 <slot />
            </main>
        </div>
</template>
