<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    players: Array
})
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 text-gray-900">

          <table class="w-full border-collapse border border-gray-300">
            <thead>
            <tr class="bg-gray-100 text-left">
              <th class="border border-gray-300 px-4 py-2">Voornaam</th>
              <th class="border border-gray-300 px-4 py-2">Achternaam</th>
              <th class="border border-gray-300 px-4 py-2">Positie</th>
              <th class="border border-gray-300 px-4 py-2">Geboortedatum</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50">
              <td class="border border-gray-300 px-4 py-2">{{ player.first_name }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ player.last_name }}</td>
              <td class="border border-gray-300 px-4 py-2">{{ player.position }}</td>
              <td class="border border-gray-300 px-4 py-2">
                {{ new Date(player.birth_date).toLocaleDateString() }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
    </AuthenticatedLayout>
</template>
