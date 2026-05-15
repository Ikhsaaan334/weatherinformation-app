<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-blue-600 mb-4">SkyCast Pro</h1>
            <p class="text-gray-600 mb-8">Professional Weather Monitoring System</p>
            
            <div v-if="canLogin" class="space-x-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-blue-500 font-bold underline">Dashboard</Link>
                <template v-else>
                    <Link :href="route('login')" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Log in</Link>
                    <Link v-if="canRegister" :href="route('register')" class="px-6 py-2 bg-gray-800 text-white rounded-lg">Register</Link>
                </template>
            </div>
        </div>
    </div>
</template>
