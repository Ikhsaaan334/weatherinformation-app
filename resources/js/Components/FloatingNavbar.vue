<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isHovered = ref(false);

const navItems = [
    { name: 'Dashboard', href: route('dashboard'), icon: 'dashboard', active: route().current('dashboard') },
    { name: 'Cities', href: route('cities.index'), icon: 'location_city', active: route().current('cities.*') },
    { name: 'Profile', href: route('profile.edit'), icon: 'person', active: route().current('profile.edit') },
];

// Simple SVG Icons
const icons: Record<string, string> = {
    dashboard: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
    location_city: 'M15 11V5l-3-3-3 3v2H3v14h18V11h-6zm-8 8H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm6 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm6 12h-2v-2h2v2zm0-4h-2v-2h2v2z',
    person: 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
};
</script>

<template>
    <div 
        @mouseenter="isHovered = true" 
        @mouseleave="isHovered = false"
        class="fixed left-4 top-1/2 -translate-y-1/2 z-50 flex flex-col bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-xl transition-all duration-300 ease-in-out border border-white/20 dark:border-gray-700/50"
        :class="isHovered ? 'w-48 p-4' : 'w-16 p-2'"
    >
        <div class="flex flex-col space-y-4">
            <Link 
                v-for="item in navItems" 
                :key="item.name" 
                :href="item.href"
                class="flex items-center space-x-3 p-2 rounded-xl transition-colors duration-200"
                :class="item.active ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
            >
                <div class="shrink-0 w-8 h-8 flex items-center justify-center">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path :d="icons[item.icon]" />
                    </svg>
                </div>
                <span 
                    v-show="isHovered"
                    class="font-medium whitespace-nowrap overflow-hidden transition-opacity duration-300"
                    :class="isHovered ? 'opacity-100' : 'opacity-0'"
                >
                    {{ item.name }}
                </span>
            </Link>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="flex items-center space-x-3 p-2 w-full text-left rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                >
                    <div class="shrink-0 w-8 h-8 flex items-center justify-center">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                        </svg>
                    </div>
                    <span 
                        v-show="isHovered"
                        class="font-medium whitespace-nowrap"
                    >
                        Logout
                    </span>
                </Link>
            </div>
        </div>
    </div>
</template>
