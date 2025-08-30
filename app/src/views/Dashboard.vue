<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-600">Welcome back, {{ user?.name }}</p>
          </div>
          <div class="flex items-center space-x-4">
            <UserMenu />
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard
          title="Total Users"
          :value="stats.total_users"
          icon="UsersIcon"
          color="blue"
        />
        <StatCard
          title="Active Users"
          :value="stats.active_users"
          icon="UserGroupIcon"
          color="green"
        />
        <StatCard
          title="Active Modules"
          :value="stats.total_modules"
          icon="CubeIcon"
          color="purple"
        />
        <StatCard
          title="Subscription"
          :value="stats.subscription_status"
          icon="CreditCardIcon"
          :color="stats.subscription_status === 'active' ? 'green' : 'red'"
        />
      </div>

      <!-- Tenant Info -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-900">Tenant Information</h2>
          <TenantStatusBadge :tenant="tenant" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <dt class="text-sm font-medium text-gray-500">Tenant Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ tenant?.name }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Plan</dt>
            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ tenant?.plan }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Status</dt>
            <dd class="mt-1 text-sm text-gray-900">
              <span :class="tenant?.is_active ? 'text-green-600' : 'text-red-600'">
                {{ tenant?.is_active ? 'Active' : 'Inactive' }}
              </span>
            </dd>
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

      <!-- Active Modules -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Active Modules</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <ModuleCard
            v-for="module in modules"
            :key="module.id"
            :module="module"
            @click="navigateToModule(module)"
          />
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <div v-if="recentActivity.length === 0" class="text-center py-8">
          <ActivityIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No activity yet</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by using one of your modules.</p>
        </div>
        <div v-else class="flow-root">
          <ul role="list" class="-mb-8">
            <li v-for="(activity, index) in recentActivity" :key="activity.id">
              <div class="relative pb-8">
                <span
                  v-if="index !== recentActivity.length - 1"
                  class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                  aria-hidden="true"
                />
                <div class="relative flex space-x-3">
                  <div>
                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                      <ActivityIcon class="h-5 w-5 text-white" />
                    </span>
                  </div>
                  <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                    <div>
                      <p class="text-sm text-gray-500">
                        {{ activity.description }}
                      </p>
                    </div>
                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                      <time :datetime="activity.created_at">
                        {{ formatRelativeTime(activity.created_at) }}
                      </time>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { format, formatDistanceToNow } from 'date-fns'
import {
  UsersIcon,
  UserGroupIcon,
  CubeIcon,
  CreditCardIcon,
  ExclamationTriangleIcon,
  ActivityIcon
} from '@heroicons/vue/24/outline'
import type { DashboardData, Module, Activity } from '@/types'
import { api } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import StatCard from '@/components/StatCard.vue'
import ModuleCard from '@/components/ModuleCard.vue'
import UserMenu from '@/components/UserMenu.vue'
import TenantStatusBadge from '@/components/TenantStatusBadge.vue'

const router = useRouter()
const authStore = useAuthStore()

// Computed properties
const user = computed(() => authStore.user)
const tenant = computed(() => authStore.user?.tenant)

// Reactive data
const dashboardData = ref<DashboardData | null>(null)
const isLoading = ref(true)

// Computed from dashboard data
const stats = computed(() => dashboardData.value?.quick_stats ?? {
  total_users: 0,
  active_users: 0,
  total_modules: 0,
  subscription_status: 'inactive'
})

const modules = computed(() => dashboardData.value?.modules ?? [])
const recentActivity = computed(() => dashboardData.value?.recent_activity ?? [])

// Methods
const fetchDashboardData = async (): Promise<void> => {
  try {
    isLoading.value = true
    const response = await api.get<DashboardData>('/dashboard')
    dashboardData.value = response.data
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    isLoading.value = false
  }
}

const navigateToModule = (module: Module): void => {
  router.push(`/${module.key}`)
}

const formatDate = (dateString: string): string => {
  return format(new Date(dateString), 'PPP')
}

const formatRelativeTime = (dateString: string): string => {
  return formatDistanceToNow(new Date(dateString), { addSuffix: true })
}

// Lifecycle
onMounted(async () => {
  await fetchDashboardData()
})
</script>
