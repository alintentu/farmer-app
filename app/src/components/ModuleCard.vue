<template>
  <div
    class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
    @click="$emit('click', module)"
  >
    <div class="p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component
            :is="iconComponent"
            class="h-8 w-8 text-gray-400"
            :class="iconColorClass"
            aria-hidden="true"
          />
        </div>
        <div class="ml-4 flex-1">
          <h3 class="text-lg font-medium text-gray-900">
            {{ module.label }}
          </h3>
          <p class="text-sm text-gray-500 mt-1">
            {{ module.description }}
          </p>
          <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
              >
                Active
              </span>
            </div>
            <div class="text-sm text-gray-500">
              {{ Object.keys(module.limits).length }} limits
            </div>
          </div>
        </div>
        <div class="ml-4 flex-shrink-0">
          <ChevronRightIcon class="h-5 w-5 text-gray-400" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'
import {
  CheckCircleIcon,
  UserGroupIcon,
  CurrencyDollarIcon,
  MegaphoneIcon,
  CogIcon,
  ChartBarIcon,
  DocumentTextIcon,
  LifeRingIcon
} from '@heroicons/vue/24/outline'
import type { Module } from '@/types'

interface Props {
  module: Module
}

const props = defineProps<Props>()

const emit = defineEmits<{
  click: [module: Module]
}>()

const iconComponent = computed(() => {
  const iconMap: Record<string, any> = {
    CheckCircleIcon,
    UserGroupIcon,
    CurrencyDollarIcon,
    MegaphoneIcon,
    CogIcon,
    ChartBarIcon,
    DocumentTextIcon,
    LifeRingIcon
  }
  return iconMap[props.module.icon] || CheckCircleIcon
})

const iconColorClass = computed(() => {
  const colorMap: Record<string, string> = {
    CheckCircleIcon: 'text-green-500',
    UserGroupIcon: 'text-blue-500',
    CurrencyDollarIcon: 'text-green-600',
    MegaphoneIcon: 'text-purple-500',
    CogIcon: 'text-gray-500',
    ChartBarIcon: 'text-indigo-500',
    DocumentTextIcon: 'text-yellow-500',
    LifeRingIcon: 'text-red-500'
  }
  return colorMap[props.module.icon] || 'text-gray-500'
})
</script>
