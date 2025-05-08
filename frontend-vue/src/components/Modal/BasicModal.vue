<script setup>
import { ref } from 'vue'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const open = ref(false)

</script>
<template>
  <div class="m-auto">
    <button class="btn btn-sm btn-outline-primary" @click="open = true">
      open
    </button>
    <TransitionRoot as="template" :show="open">
      <Dialog class="fixed inset-0 z-10 flex items-start justify-center mt-20" @close="open = false">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <DialogPanel
            class="w-90 bg-white rounded-lg shadow-xl p-6"
          >
            <div class="flex items-center">
              <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100">
                <ExclamationTriangleIcon class="size-6 text-red-600" aria-hidden="true" />
              </div>
              <div class="ml-4">
                <DialogTitle as="h3" class="text-lg font-semibold text-gray-900">
                    title modal
                </DialogTitle>
                <p class="text-sm text-gray-500 mt-2">
                  body modal
                </p>
              </div>
            </div>

            <div class="mt-4 flex justify-end space-x-3">
              <button
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500"
                @click="open = false"
              >
                Deactivate
              </button>
              <button
                class="px-4 py-2 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300"
                @click="open = false"
              >
                Cancel
              </button>
            </div>
          </DialogPanel>
        </TransitionChild>
      </Dialog>
    </TransitionRoot>
  </div>
</template>
