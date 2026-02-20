<template>
  <TabGroup as="div" :class="['w-full']">
    <TabList class="flex space-x-1 rounded-xl bg-gray-100 p-1">
      <Tab
        v-for="(tab, index) in tabs"
        :key="index"
        v-slot="{ selected }"
        as="template"
      >
        <button
          :class="[
            'w-full rounded-lg py-2.5 text-sm font-medium leading-5 transition-colors',
            'ring-white ring-opacity-60 ring-offset-2 ring-offset-primary-400 focus:outline-none focus:ring-2',
            selected
              ? 'bg-white shadow text-primary-600'
              : 'text-gray-600 hover:bg-white/[0.12] hover:text-gray-900'
          ]"
        >
          {{ tab.label }}
        </button>
      </Tab>
    </TabList>

    <TabPanels class="mt-4">
      <TabPanel
        v-for="(tab, index) in tabs"
        :key="index"
        :class="['rounded-xl bg-white p-4', 'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2']"
      >
        <slot :name="tab.slot || index" :tab="tab">
          {{ tab.content }}
        </slot>
      </TabPanel>
    </TabPanels>
  </TabGroup>
</template>

<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

defineProps({
  tabs: {
    type: Array,
    required: true,
    default: () => []
  }
})
</script>
