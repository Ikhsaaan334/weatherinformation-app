<script setup lang="ts">
const model = defineModel<boolean>({ default: false });
</script>

<template>
    <input
        v-model="model"
        type="checkbox"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
    />
</template>
