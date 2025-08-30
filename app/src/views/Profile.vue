<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
            <p class="text-sm text-gray-600">Manage your account settings</p>
          </div>
          <div class="flex items-center space-x-4">
            <UserMenu />
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
            </div>
            <div class="p-6">
              <form @submit.prevent="updateProfile">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                      id="email"
                      v-model="form.email"
                      type="email"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
                <div class="mt-6 flex justify-end">
                  <button
                    type="submit"
                    :disabled="authStore.isLoading"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  >
                    {{ authStore.isLoading ? 'Updating...' : 'Update Profile' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Change Password -->
          <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
            </div>
            <div class="p-6">
              <form @submit.prevent="updatePassword">
                <div class="space-y-6">
                  <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input
                      id="current_password"
                      v-model="passwordForm.current_password"
                      type="password"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input
                      id="new_password"
                      v-model="passwordForm.password"
                      type="password"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input
                      id="password_confirmation"
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
                <div class="mt-6 flex justify-end">
                  <button
                    type="submit"
                    :disabled="authStore.isLoading"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  >
                    {{ authStore.isLoading ? 'Updating...' : 'Change Password' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <!-- User Info -->
          <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center">
                <span class="text-xl font-medium text-white">
                  {{ user?.initials || 'U' }}
                </span>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">{{ user?.name }}</h3>
                <p class="text-sm text-gray-500">{{ user?.email }}</p>
              </div>
            </div>
          </div>

          <!-- Account Info -->
          <div class="mt-6 bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
            <dl class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500">Member since</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user?.created_at) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Last login</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user?.last_login_at) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                  <span
                    :class="user?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ user?.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { format } from 'date-fns'
import { useAuthStore } from '@/stores/auth'
import UserMenu from '@/components/UserMenu.vue'
import type { User } from '@/types'

const authStore = useAuthStore()

const user = computed(() => authStore.user)

const form = ref({
  name: '',
  email: ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = async () => {
  const success = await authStore.updateProfile({
    name: form.value.name,
    email: form.value.email
  })
  
  if (success) {
    // Reset form
    form.value.name = user.value?.name || ''
    form.value.email = user.value?.email || ''
  }
}

const updatePassword = async () => {
  const success = await authStore.updatePassword(passwordForm.value)
  
  if (success) {
    // Reset form
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }
  }
}

const formatDate = (dateString?: string): string => {
  if (!dateString) return 'N/A'
  return format(new Date(dateString), 'PPP')
}

onMounted(() => {
  // Initialize form with current user data
  form.value.name = user.value?.name || ''
  form.value.email = user.value?.email || ''
})
</script>
