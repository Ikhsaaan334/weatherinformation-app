<script setup lang="ts">
import FloatingNavbar from '@/components/FloatingNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <FloatingNavbar />

        <div class="pl-24 pr-8 py-8 transition-all duration-300">
            <div class="max-w-7xl mx-auto">
                <header v-if="$slots.header" class="mb-8">
                    <div class="bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-3xl p-8 border border-white/20 dark:border-gray-800 shadow-sm">
                        <slot name="header" />
                    </div>
                </header>

                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
