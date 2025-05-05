<script setup>
import { ref } from 'vue'
import { RouterView } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import NavbarTop from '@/layouts/NavbarTop.vue'
import FormLogin from './components/FormLogin.vue'
const authStore = useAuthStore()
const { storeUser } = storeToRefs(authStore)

</script>

<template>
<div class="grid">
  <div class="grid">
    <NavbarTop 
      v-if="authStore.storeUser"
    />
    <FormLogin 
      v-if="!authStore.storeUser"
    />
  </div>
  <div class="grid">
    <RouterView />
  </div>
</div>
</template>

<style scoped>

</style>
