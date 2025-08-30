<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Modules</h1>
            <p class="text-sm text-gray-600">Manage your available modules and features</p>
          </div>
          <div class="flex items-center space-x-4">
            <UserMenu />
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Available Modules -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Modules</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ModuleCard
            v-for="module in availableModules"
            :key="module.id"
            :module="module"
            @click="handleModuleClick(module)"
          />
        </div>
      </div>

      <!-- Subscription Info -->
      <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500">Current Plan</h3>
            <p class="mt-1 text-lg font-semibold text-gray-900 capitalize">{{ tenant?.plan }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Status</h3>
            <TenantStatusBadge :tenant="tenant" />
          </div>
        </div>
        
        <div v-if="tenant?.trial_ends_at" class="mt-4 p-4 bg-blue-50 rounded-md">
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationTriangleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Trial Period</h3>
              <div class="mt-2 text-sm text-blue-700">
                <p>Your trial ends on {{ formatDate(tenant.trial_ends_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { format } from 'date-fns'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import type { Module } from '@/types'
import { api } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import ModuleCard from '@/components/ModuleCard.vue'
import UserMenu from '@/components/UserMenu.vue'
import TenantStatusBadge from '@/components/TenantStatusBadge.vue'

const router = useRouter()
const authStore = useAuthStore()

const tenant = computed(() => authStore.user?.tenant)
const availableModules = ref<Module[]>([])

const fetchModules = async () => {
  try {
    const response = await api.get<Module[]>('/modules')
    availableModules.value = response.data
  } catch (error) {
    console.error('Failed to fetch modules:', error)
  }
}

const handleModuleClick = (module: Module) => {
  router.push(`/${module.key}`)
}

const formatDate = (dateString: string): string => {
  return format(new Date(dateString), 'PPP')
}

onMounted(() => {
  fetchModules()
})
</script>
