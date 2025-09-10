<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">{{ pdf?.name }}</h1>
      <div class="text-sm text-gray-600">Status: <span class="font-medium">{{ pdf?.processing_status }}</span></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <div class="border rounded">
        <div class="p-2 border-b font-medium">Preview</div>
        <iframe v-if="pdf" class="w-full h-[70vh]" :src="downloadUrl(pdf.id)"></iframe>
        <div v-else class="p-4 text-gray-500">Loading…</div>
      </div>
      <div class="space-y-6">
        <div class="border rounded">
          <div class="p-2 border-b font-medium">Pages</div>
          <div class="max-h-[34vh] overflow-auto">
            <table class="min-w-full divide-y">
              <thead class="bg-gray-50">
                <tr>
                  <th class="text-left px-3 py-2">#</th>
                  <th class="text-left px-3 py-2">Active</th>
                  <th class="text-left px-3 py-2">Embedding</th>
                  <th class="text-right px-3 py-2">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in pages" :key="p.id" class="border-t">
                  <td class="px-3 py-2">{{ p.page_number }}</td>
                  <td class="px-3 py-2">{{ p.is_active ? 'Yes' : 'No' }}</td>
                  <td class="px-3 py-2">{{ p.embedding_status }}</td>
                  <td class="px-3 py-2 text-right">
                    <button class="text-blue-600" @click="togglePage(p)">Toggle</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="border rounded">
          <div class="p-2 border-b font-medium">Images</div>
          <div class="max-h-[34vh] overflow-auto">
            <table class="min-w-full divide-y">
              <thead class="bg-gray-50">
                <tr>
                  <th class="text-left px-3 py-2">#</th>
                  <th class="text-left px-3 py-2">Active</th>
                  <th class="text-left px-3 py-2">Embedding</th>
                  <th class="text-right px-3 py-2">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="img in images" :key="img.id" class="border-t">
                  <td class="px-3 py-2">{{ img.page_number }}</td>
                  <td class="px-3 py-2">{{ img.is_active ? 'Yes' : 'No' }}</td>
                  <td class="px-3 py-2">{{ img.embedding_status }}</td>
                  <td class="px-3 py-2 text-right">
                    <button class="text-blue-600" @click="toggleImage(img)">Toggle</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import { contentLibraryService, type ContentPdf, type ContentPdfPage, type ContentPdfImage } from '@/services/contentLibrary'

const route = useRoute()
const toast = useToast()

const pdf = ref<ContentPdf | null>(null)
const pages = ref<ContentPdfPage[]>([])
const images = ref<ContentPdfImage[]>([])

const load = async () => {
  const id = route.params.id as string
  const data = await contentLibraryService.get(id)
  pdf.value = data
  pages.value = data.pages || []
  images.value = data.images || []
}

const downloadUrl = (id: string) => contentLibraryService.downloadUrl(id)

const togglePage = async (p: ContentPdfPage) => {
  if (!pdf.value) return
  try {
    const res = await contentLibraryService.togglePage(pdf.value.id, p.id)
    p.is_active = res.is_active
  } catch {
    toast.error('Failed to toggle page')
  }
}

const toggleImage = async (img: ContentPdfImage) => {
  if (!pdf.value) return
  try {
    const res = await contentLibraryService.toggleImage(pdf.value.id, img.id)
    img.is_active = res.is_active
  } catch {
    toast.error('Failed to toggle image')
  }
}

onMounted(load)
</script>

<style scoped>
</style>

