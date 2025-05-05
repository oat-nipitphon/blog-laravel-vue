<template>
    <div class="flex justify-center bg-red-500">
        <h1>index</h1>
    </div>
</template>
<script setup>
import { onMounted } from 'vue'
import axiosAPI from '@/services/axiosAPI'

const getTest = async () => {
    try {

        const res = axiosAPI.get('/api/test_get');

        const data = await res.json()

        if (!res.ok) {
            console.log('api false')
        } 
        console.log(data.userStatus)


    } catch (error) {
        console.error(error)
    }
}

onMounted(async () => {
    getTest()
})

</script>