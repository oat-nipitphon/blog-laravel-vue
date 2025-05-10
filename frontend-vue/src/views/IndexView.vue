<template>
    <div class="w-full"
    v-if="!authStore.users"
    >
    aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        <ModalFormLoginAndRegister />
    </div>
    <div class="flex justify-center"
    v-if="authStore.users"
    >
    sssssssssssssssssssssssssssssssssss
        <button 
        
        @click="storeAPILogout">
            Logout
        </button>
    </div>
</template>
<script setup>
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
const authStore = useAuthStore()
const { users } = storeToRefs(authStore)
const { storeAPILogout } = useAuthStore()
    import ModalFormLoginAndRegister from '@/components/auth/ModalFormLoginAndRegister.vue';
</script>