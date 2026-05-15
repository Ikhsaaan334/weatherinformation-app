<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <Link href="/" class="flex justify-center items-center space-x-2 mb-6">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-600/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>
                </div>
                <span class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white uppercase">SkyCast<span class="text-blue-600">Pro</span></span>
            </Link>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4">
            <div class="bg-white dark:bg-gray-900 py-10 px-6 shadow-2xl shadow-gray-200/50 dark:shadow-none sm:rounded-[2.5rem] sm:px-10 border border-gray-100 dark:border-gray-800">
                <slot />
            </div>
            
            <p class="mt-8 text-center text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                &copy; 2026 SkyCast Intelligence Systems
            </p>
        </div>
    </div>
</template>
