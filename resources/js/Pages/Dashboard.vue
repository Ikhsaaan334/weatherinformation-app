<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    weatherData: Array<{
        wilayah: string;
        kecamatan: string;
        kota: string;
        temp: number;
        humidity: number;
        wind_speed: number;
        description: string;
        image: string;
        datetime: string;
    }>;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-blue-500 font-bold tracking-widest uppercase text-xs mb-2">Real-time Monitoring</p>
                    <h2 class="text-4xl font-black leading-tight text-gray-900 dark:text-white">
                        Weather Overview
                    </h2>
                </div>
                <div class="text-right hidden md:block">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Data Source</p>
                    <p class="text-gray-900 dark:text-white font-bold">BMKG Indonesia</p>
                </div>
            </div>
        </template>

        <div class="space-y-12">
            <!-- Weather Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="data in weatherData" 
                    :key="data.wilayah"
                    class="group bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-2xl hover:border-blue-500/20 transition-all duration-500 relative overflow-hidden"
                >
                    <!-- Glassmorphism Background Accent -->
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-1">{{ data.wilayah }}</h3>
                                <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">{{ data.kecamatan }}, {{ data.kota }}</p>
                            </div>
                            <div class="w-16 h-16 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                                <img v-if="data.image" :src="data.image" :alt="data.description" class="w-12 h-12 object-contain" />
                                <span v-else class="text-3xl">🌥️</span>
                            </div>
                        </div>

                        <div class="flex items-end space-x-4 mb-8">
                            <div class="text-6xl font-black text-gray-900 dark:text-white">{{ Math.round(data.temp) }}°</div>
                            <div class="pb-2">
                                <div class="text-blue-600 dark:text-blue-400 font-bold text-lg leading-none">{{ data.description }}</div>
                                <div class="text-gray-400 text-xs mt-1 uppercase tracking-tighter">{{ new Date(data.datetime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-50 dark:border-gray-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Humidity</p>
                                    <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ data.humidity }}%</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Wind</p>
                                    <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ data.wind_speed }} km/h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Attribution -->
            <footer class="mt-12 text-center border-t border-gray-100 dark:border-gray-800 pt-8 pb-4">
                <p class="text-gray-400 dark:text-gray-600 text-sm font-medium italic">
                    Sumber Data: <span class="text-gray-600 dark:text-gray-400 font-bold not-italic">BMKG (Badan Meteorologi, Klimatologi, dan Geofisika)</span>
                </p>
            </footer>
        </div>
    </AuthenticatedLayout>
</template>
