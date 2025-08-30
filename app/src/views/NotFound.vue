<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <h1 class="text-9xl font-bold text-gray-200">404</h1>
        <h2 class="text-2xl font-bold text-gray-900 mt-4">Page not found</h2>
        <p class="text-gray-600 mt-2">
          Sorry, we couldn't find the page you're looking for.
        </p>
      </div>
      
      <div class="space-y-4">
        <router-link
          to="/dashboard"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <HomeIcon class="mr-2 h-4 w-4" />
          Go back to Dashboard
        </router-link>
        
        <div class="text-sm text-gray-500">
          Or
          <router-link
            to="/modules"
            class="font-medium text-indigo-600 hover:text-indigo-500"
          >
            browse our modules
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { HomeIcon } from '@heroicons/vue/24/outline'
</script>
