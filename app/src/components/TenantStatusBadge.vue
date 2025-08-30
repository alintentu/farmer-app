<template>
  <div class="flex items-center space-x-2">
    <span
      :class="statusClasses"
      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
    >
      <span
        :class="dotClasses"
        class="w-2 h-2 rounded-full mr-1.5"
      />
      {{ statusText }}
    </span>
    
    <span
      v-if="tenant?.is_on_trial"
      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
    >
      Trial
    </span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Tenant } from '@/types'

interface Props {
  tenant?: Tenant | null
}

const props = defineProps<Props>()

const statusText = computed(() => {
  if (!props.tenant) return 'Unknown'
  
  if (props.tenant.is_on_trial) {
    return 'Trial'
  }
  
  if (props.tenant.has_active_subscription) {
    return 'Active'
  }
  
  return 'Inactive'
})

const statusClasses = computed(() => {
  if (!props.tenant) return 'bg-gray-100 text-gray-800'
  
  if (props.tenant.is_on_trial) {
    return 'bg-yellow-100 text-yellow-800'
  }
  
  if (props.tenant.has_active_subscription) {
    return 'bg-green-100 text-green-800'
  }
  
  return 'bg-red-100 text-red-800'
})

const dotClasses = computed(() => {
  if (!props.tenant) return 'bg-gray-400'
  
  if (props.tenant.is_on_trial) {
    return 'bg-yellow-400'
  }
  
  if (props.tenant.has_active_subscription) {
    return 'bg-green-400'
  }
  
  return 'bg-red-400'
})
</script>
