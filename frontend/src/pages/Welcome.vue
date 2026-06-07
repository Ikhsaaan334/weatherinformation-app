<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-950 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-blue-600 mb-4">SkyCast Pro</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-8">Professional Weather Monitoring System</p>

            <div class="space-x-4">
                <RouterLink v-if="auth.isAuthenticated" to="/dashboard" class="text-blue-500 font-bold underline">
                    Dashboard
                </RouterLink>
                <template v-else>
                    <RouterLink to="/login" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Log in</RouterLink>
                    <RouterLink to="/register" class="px-6 py-2 bg-gray-800 text-white rounded-lg">Register</RouterLink>
                </template>
            </div>
        </div>
    </div>
</template>
