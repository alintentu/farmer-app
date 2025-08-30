<template>
  <Menu as="div" class="relative">
    <div>
      <MenuButton class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
          <span class="text-sm font-medium text-white">
            {{ user?.initials || 'U' }}
          </span>
        </div>
        <div class="hidden md:block text-left">
          <p class="text-sm font-medium text-gray-700">{{ user?.name }}</p>
          <p class="text-xs text-gray-500">{{ user?.email }}</p>
        </div>
        <ChevronDownIcon class="h-4 w-4 text-gray-400" />
      </MenuButton>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        <div class="py-1">
          <MenuItem v-slot="{ active }">
            <router-link
              to="/profile"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'block px-4 py-2 text-sm'
              ]"
            >
              <UserIcon class="mr-3 h-4 w-4" />
              Profile
            </router-link>
          </MenuItem>
          
          <MenuItem v-slot="{ active }">
            <router-link
              to="/settings"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'block px-4 py-2 text-sm'
              ]"
            >
              <CogIcon class="mr-3 h-4 w-4" />
              Settings
            </router-link>
          </MenuItem>
          
          <MenuItem v-slot="{ active }">
            <router-link
              to="/modules"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'block px-4 py-2 text-sm'
              ]"
            >
              <CubeIcon class="mr-3 h-4 w-4" />
              Modules
            </router-link>
          </MenuItem>
          
          <div class="border-t border-gray-100" />
          
          <MenuItem v-slot="{ active }">
            <button
              @click="handleLogout"
              :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'block w-full text-left px-4 py-2 text-sm'
              ]"
            >
              <ArrowRightOnRectangleIcon class="mr-3 h-4 w-4" />
              Sign out
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Menu, MenuButton, MenuItem } from '@headlessui/vue'
import {
  ChevronDownIcon,
  UserIcon,
  CogIcon,
  CubeIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import type { User } from '@/types'

const authStore = useAuthStore()

const user = computed(() => authStore.user)

const handleLogout = async () => {
  await authStore.logout()
}
</script>
