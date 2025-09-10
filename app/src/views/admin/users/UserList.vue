<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Users</h1>
      <router-link class="px-3 py-2 rounded bg-blue-600 text-white" :to="{ name: 'AdminUserInvite' }">Invite User</router-link>
    </div>

    <div class="flex gap-2 mb-4">
      <input v-model="q" type="text" placeholder="Search name/email/region" class="border rounded px-3 py-2 w-80" @keyup.enter="fetch" />
      <button class="px-3 py-2 rounded bg-gray-100" @click="fetch">Search</button>
    </div>

    <div class="overflow-x-auto border rounded">
      <table class="min-w-full divide-y">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-3 py-2">Name</th>
            <th class="text-left px-3 py-2">Email</th>
            <th class="text-left px-3 py-2">Region</th>
            <th class="text-left px-3 py-2">Language</th>
            <th class="text-left px-3 py-2">Active</th>
            <th class="text-right px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in items" :key="u.id" class="border-t">
            <td class="px-3 py-2">{{ u.name }}</td>
            <td class="px-3 py-2">{{ u.email }}</td>
            <td class="px-3 py-2">{{ u.region || '-' }}</td>
            <td class="px-3 py-2">{{ u.language || '-' }}</td>
            <td class="px-3 py-2">{{ u.is_active ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2 text-right">
              <router-link class="text-blue-600" :to="{ name: 'AdminUserEdit', params: { id: u.id } }">Edit</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminUsersService, type AdminUser } from '@/services/adminUsers'

const items = ref<AdminUser[]>([])
const q = ref('')

const fetch = async () => {
  const res = await adminUsersService.list({ q: q.value, per_page: 50 })
  items.value = res.data || res
}

onMounted(fetch)
</script>

<style scoped></style>

