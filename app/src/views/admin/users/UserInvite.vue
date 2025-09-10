<template>
  <div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-semibold mb-4">Invite User</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-1">Name</label>
        <input v-model="name" class="border rounded px-3 py-2 w-full" required />
      </div>
      <div>
        <label class="block mb-1">Email</label>
        <input v-model="email" type="email" class="border rounded px-3 py-2 w-full" required />
      </div>
      <div>
        <label class="block mb-1">Role</label>
        <select v-model="role" class="border rounded px-3 py-2 w-full" required>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1">Region</label>
          <input v-model="region" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block mb-1">Language</label>
          <select v-model="language" class="border rounded px-3 py-2 w-full">
            <option value="">Auto/Unknown</option>
            <option value="en">English</option>
            <option value="ro">Romanian</option>
          </select>
        </div>
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-2 rounded bg-blue-600 text-white" :disabled="loading">Send Invite</button>
        <router-link class="px-3 py-2 rounded bg-gray-100" :to="{ name: 'AdminUsers' }">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { adminUsersService } from '@/services/adminUsers'

const router = useRouter()
const toast = useToast()

const name = ref('')
const email = ref('')
const role = ref('user')
const region = ref('')
const language = ref('')
const loading = ref(false)

const submit = async () => {
  loading.value = true
  try {
    await adminUsersService.invite({ name: name.value, email: email.value, role: role.value, region: region.value, language: language.value })
    toast.success('Invite sent (mocked email).')
    router.push({ name: 'AdminUsers' })
  } catch (e) {
    toast.error('Invite failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped></style>

