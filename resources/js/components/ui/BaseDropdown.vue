<template>
  <Menu as="div" class="relative inline-block text-left">
    <div>
      <MenuButton
        :class="buttonClasses"
      >
        <slot name="trigger">
          {{ label }}
          <ChevronDownIcon class="ml-2 -mr-1 h-5 w-5" aria-hidden="true" />
        </slot>
      </MenuButton>
    </div>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
        :class="[positionClasses, 'absolute z-50 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none']"
      >
        <div class="px-1 py-1">
          <MenuItem
            v-for="(item, index) in items"
            :key="index"
            v-slot="{ active }"
          >
            <button
              v-if="item.action"
              @click="item.action"
              :class="[
                active ? 'bg-primary-500 text-white' : 'text-gray-900',
                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
            >
              <component
                v-if="item.icon"
                :is="item.icon"
                :class="[
                  active ? 'text-white' : 'text-gray-400',
                  'mr-2 h-5 w-5',
                ]"
                aria-hidden="true"
              />
              {{ item.label }}
            </button>
            <a
              v-else-if="item.href"
              :href="item.href"
              :class="[
                active ? 'bg-primary-500 text-white' : 'text-gray-900',
                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
            >
              <component
                v-if="item.icon"
                :is="item.icon"
                :class="[
                  active ? 'text-white' : 'text-gray-400',
                  'mr-2 h-5 w-5',
                ]"
                aria-hidden="true"
              />
              {{ item.label }}
            </a>
          </MenuItem>
        </div>
        <div v-if="$slots.footer" class="px-1 py-1">
          <slot name="footer" />
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { computed } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
  label: {
    type: String,
    default: 'Options'
  },
  items: {
    type: Array,
    default: () => []
  },
  position: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  },
  variant: {
    type: String,
    default: 'secondary',
    validator: (value) => ['primary', 'secondary', 'ghost'].includes(value)
  }
})

const buttonClasses = computed(() => {
  const variants = {
    primary: 'bg-primary-500 hover:bg-primary-600 text-white',
    secondary: 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
    ghost: 'text-gray-700 hover:bg-gray-100'
  }
  return [
    'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
    variants[props.variant]
  ].join(' ')
})

const positionClasses = computed(() => {
  return props.position === 'right' ? 'right-0' : 'left-0'
})
</script>
