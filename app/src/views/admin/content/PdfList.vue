<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Content Library · PDFs</h1>
      <router-link class="px-3 py-2 rounded bg-blue-600 text-white" :to="{ name: 'AdminContentPdfNew' }">Upload PDF</router-link>
    </div>

    <div class="flex gap-2 mb-4">
      <input v-model="q" type="text" placeholder="Search name..." class="border rounded px-3 py-2 w-64" @keyup.enter="fetch" />
      <select v-model="language" class="border rounded px-3 py-2">
        <option value="">All languages</option>
        <option value="en">EN</option>
        <option value="ro">RO</option>
      </select>
      <select v-model="status" class="border rounded px-3 py-2">
        <option value="">All status</option>
        <option value="pending">pending</option>
        <option value="processing">processing</option>
        <option value="complete">complete</option>
        <option value="failed">failed</option>
      </select>
      <button class="px-3 py-2 rounded bg-gray-100" @click="fetch">Filter</button>
    </div>

    <div class="overflow-x-auto border rounded">
      <table class="min-w-full divide-y">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-3 py-2">Name</th>
            <th class="text-left px-3 py-2">Language</th>
            <th class="text-left px-3 py-2">Status</th>
            <th class="text-left px-3 py-2">Tokens</th>
            <th class="text-right px-3 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t">
            <td class="px-3 py-2">{{ item.name }}</td>
            <td class="px-3 py-2">{{ item.language || '-' }}</td>
            <td class="px-3 py-2">{{ item.processing_status }}</td>
            <td class="px-3 py-2">{{ item.tokens_used }}</td>
            <td class="px-3 py-2 text-right">
              <router-link class="text-blue-600" :to="{ name: 'AdminContentPdfShow', params: { id: item.id } }">Open</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { contentLibraryService, type ContentPdf } from '@/services/contentLibrary'

const items = ref<ContentPdf[]>([])
const q = ref('')
const language = ref('')
const status = ref('')

const fetch = async () => {
  const res = await contentLibraryService.list({ q: q.value, language: language.value, status: status.value, per_page: 50 })
  items.value = res.data || res
}

onMounted(fetch)
</script>

<style scoped>
</style>

