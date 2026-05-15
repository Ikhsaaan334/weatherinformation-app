<script setup lang="ts">
import { ref } from 'vue';
import FloatingNavbar from '@/Components/FloatingNavbar.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
            <!-- Floating Navbar -->
            <FloatingNavbar />

            <!-- Main Content Container -->
            <div class="pl-24 pr-8 py-8 transition-all duration-300">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Heading -->
                    <header
                        class="mb-8"
                        v-if="$slots.header"
                    >
                        <div class="bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-3xl p-8 border border-white/20 dark:border-gray-800 shadow-sm">
                            <slot name="header" />
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main>
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
