<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-xl font-semibold mb-4">Upload PDF</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block mb-1">Name</label>
        <input v-model="name" class="border rounded px-3 py-2 w-full" required />
      </div>
      <div>
        <label class="block mb-1">Description</label>
        <textarea v-model="description" class="border rounded px-3 py-2 w-full" rows="3"></textarea>
      </div>
      <div class="flex gap-4">
        <div class="flex-1">
          <label class="block mb-1">Language</label>
          <select v-model="language" class="border rounded px-3 py-2 w-full">
            <option value="">Auto/Unknown</option>
            <option value="en">English</option>
            <option value="ro">Romanian</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <input id="active" type="checkbox" v-model="isActive" class="mr-2"/>
          <label for="active">Active</label>
        </div>
      </div>
      <div>
        <label class="block mb-1">PDF File</label>
        <input type="file" accept="application/pdf" @change="onFile" required />
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-2 rounded bg-blue-600 text-white" :disabled="loading">Upload</button>
        <router-link class="px-3 py-2 rounded bg-gray-100" :to="{ name: 'AdminContentPdfs' }">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { contentLibraryService } from '@/services/contentLibrary'

const router = useRouter()
const toast = useToast()

const name = ref('')
const description = ref('')
const language = ref('')
const isActive = ref(true)
const file = ref<File | null>(null)
const loading = ref(false)

const onFile = (e: Event) => {
  const target = e.target as HTMLInputElement
  file.value = target.files?.[0] || null
}

const submit = async () => {
  if (!file.value) { toast.error('Please choose a PDF'); return }
  loading.value = true
  try {
    const res = await contentLibraryService.upload({ name: name.value, description: description.value, language: language.value, is_active: isActive.value, pdf: file.value })
    toast.success('Upload started. Processing…')
    const id = res.id || res.data?.id
    if (id) router.push({ name: 'AdminContentPdfShow', params: { id } })
  } catch (e) {
    toast.error('Upload failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
</style>

