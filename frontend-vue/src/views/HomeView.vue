<template>
    <div class="bg-red-500 rounded-lg shadow-lg w-full">
        <div class="grid">
            <div class="flex justify-end">
                <RouterLink class="" :to="{ name: 'CreatePostView' }">
                    Create Post
                </RouterLink>
            </div>
        </div>
        <div class="flex">
            {{ authStore.users }}
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const { users } = storeToRefs(authStore)

console.log('home view', authStore.users);

onMounted(async () => {
    await authStore.storeAPIStartAuth();
})

</script>