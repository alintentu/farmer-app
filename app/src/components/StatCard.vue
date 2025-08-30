<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component
            :is="iconComponent"
            class="h-6 w-6 text-gray-400"
            :class="iconColorClass"
            aria-hidden="true"
          />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd class="text-lg font-medium text-gray-900">
              {{ formattedValue }}
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  UsersIcon,
  UserGroupIcon,
  CubeIcon,
  CreditCardIcon,
  ChartBarIcon,
  DocumentTextIcon,
  CogIcon,
  MegaphoneIcon
} from '@heroicons/vue/24/outline'

interface Props {
  title: string
  value: string | number
  icon: string
  color?: 'blue' | 'green' | 'red' | 'purple' | 'yellow' | 'gray'
}

const props = withDefaults(defineProps<Props>(), {
  color: 'blue'
})

const iconComponent = computed(() => {
  const iconMap: Record<string, any> = {
    UsersIcon,
    UserGroupIcon,
    CubeIcon,
    CreditCardIcon,
    ChartBarIcon,
    DocumentTextIcon,
    CogIcon,
    MegaphoneIcon
  }
  return iconMap[props.icon] || UsersIcon
})

const iconColorClass = computed(() => {
  const colorMap: Record<string, string> = {
    blue: 'text-blue-500',
    green: 'text-green-500',
    red: 'text-red-500',
    purple: 'text-purple-500',
    yellow: 'text-yellow-500',
    gray: 'text-gray-500'
  }
  return colorMap[props.color] || 'text-blue-500'
})

const formattedValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString()
  }
  return props.value
})
</script>
