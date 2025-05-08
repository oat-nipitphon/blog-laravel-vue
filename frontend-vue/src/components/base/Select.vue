<template>
    <div>
        <label :for="id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ label }}
        </label>
        <select :id="id" :v-model="modelValue" @change="$emit('update:modelValue', $event.target.value)" :class="[
            'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5',
            'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'
        ]">
            <option value="">{{ placeholder }}</option>
            <option v-for="item in options" :key="item[optionKey]" :value="item[optionKey]">
                {{ item[optionLabel] }}
            </option>
        </select>
    </div>
</template>

<script setup>
defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    placeholder: { type: String, default: 'เลือกตัวเลือก' },
    options: { type: Array, required: true },
    optionKey: { type: String, default: 'id' },
    optionLabel: { type: String, default: 'name' },
    modelValue: [String, Number]
})

defineEmits(['update:modelValue'])
</script>