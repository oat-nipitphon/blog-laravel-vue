<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

import NavbarTop from '@/layouts/NavbarTop.vue'

const authStore = useAuthStore()
const { users } = storeToRefs(authStore)

</script>

<template>
<div class="w-full h-full bg-red-500">
  <div class="grid">
    <NavbarTop 
      v-if="authStore.users"
    />
  </div>
  <div class="grid">
    <RouterView />
  </div>
</div>
</template>

<style scoped>

</style>
