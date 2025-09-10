<template>
  <div class="p-6 max-w-2xl mx-auto" v-if="user">
    <h1 class="text-xl font-semibold mb-4">Edit User</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-1">Name</label>
        <input v-model="form.name" class="border rounded px-3 py-2 w-full" required />
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1">City</label>
          <input v-model="form.city" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block mb-1">Phone</label>
          <input v-model="form.phone" class="border rounded px-3 py-2 w-full" />
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1">Region</label>
          <input v-model="form.region" class="border rounded px-3 py-2 w-full" />
        </div>
        <div>
          <label class="block mb-1">Language</label>
          <select v-model="form.language" class="border rounded px-3 py-2 w-full">
            <option value="">Auto/Unknown</option>
            <option value="en">English</option>
            <option value="ro">Romanian</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block mb-1">Profile image</label>
        <input type="file" accept="image/*" @change="onFile" />
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-2 rounded bg-blue-600 text-white" :disabled="loading">Save</button>
        <router-link class="px-3 py-2 rounded bg-gray-100" :to="{ name: 'AdminUsers' }">Back</router-link>
      </div>
    </form>
  </div>
  <div v-else class="p-6">Loading…</div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { adminUsersService, type AdminUser } from '@/services/adminUsers'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const user = ref<AdminUser | null>(null)
const form = ref<{ name: string; city: string; phone: string; region: string; language: string; profile_image: File | null }>({
  name: '', city: '', phone: '', region: '', language: '', profile_image: null
})
const loading = ref(false)

const load = async () => {
  const id = route.params.id as string
  const data = await adminUsersService.show(id)
  user.value = data
  form.value.name = data.name
  form.value.city = (data as any).city || ''
  form.value.phone = (data as any).phone || ''
  form.value.region = data.region || ''
  form.value.language = data.language || ''
}

const onFile = (e: Event) => {
  const target = e.target as HTMLInputElement
  form.value.profile_image = target.files?.[0] || null
}

const submit = async () => {
  if (!user.value) return
  loading.value = true
  try {
    await adminUsersService.update(user.value.id, form.value)
    toast.success('User updated')
    router.push({ name: 'AdminUsers' })
  } catch (e) {
    toast.error('Update failed')
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<style scoped></style>

