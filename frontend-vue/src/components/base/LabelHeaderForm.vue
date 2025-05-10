<template>
    <label :for="forLabel" :class="classLabel">
        {{ textLabel }}
    </label>
</template>
<script setup>
defineProps({
    forLabel: {
        type: String,
        required: true,
    },
    classLabel: {
        type: String,
        required: true,
    },
    textLabel: {
        type: String,
        required: true,
    },
})
</script>