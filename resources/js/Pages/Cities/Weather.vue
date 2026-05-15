<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps<{
    city: any;
    weather: any;
}>();

const current = props.weather?.current_weather;
const hourly = props.weather?.hourly;

// Simple weather code to icon/description mapping
const weatherCodes: Record<number, { label: string; icon: string }> = {
    0: { label: "Clear sky", icon: "☀️" },
    1: { label: "Mainly clear", icon: "🌤️" },
    2: { label: "Partly cloudy", icon: "⛅" },
    3: { label: "Overcast", icon: "☁️" },
    45: { label: "Fog", icon: "🌫️" },
    48: { label: "Depositing rime fog", icon: "🌫️" },
    51: { label: "Drizzle: Light", icon: "🌦️" },
    61: { label: "Rain: Slight", icon: "🌧️" },
    71: { label: "Snow fall: Slight", icon: "❄️" },
    95: { label: "Thunderstorm", icon: "⛈️" },
};

const getWeatherInfo = (code: number) => {
    return weatherCodes[code] || { label: "Unknown", icon: "❓" };
};
</script>

<template>
    <Head :title="`Weather in ${city.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('cities.index')"
                    class="p-2 rounded-full transition-colors text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                </Link>
                <h2
                    class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    {{ city.name }}, {{ city.country }}
                </h2>
            </div>
        </template>

        <div v-if="weather" class="space-y-8">
            <!-- Current Weather Card -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-[3rem] p-12 text-white shadow-2xl shadow-blue-500/40 relative overflow-hidden"
            >
                <!-- Decorative Elements -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"
                ></div>
                <div
                    class="absolute bottom-0 left-0 w-48 h-48 bg-blue-400/20 rounded-full -ml-10 -mb-10 blur-2xl"
                ></div>

                <div
                    class="relative z-10 flex flex-col md:flex-row justify-between items-center"
                >
                    <div class="text-center md:text-left mb-8 md:mb-0">
                        <div class="text-8xl font-black mb-2">
                            {{ Math.round(current.temperature) }}°
                        </div>
                        <div class="text-2xl font-medium opacity-90">
                            {{ getWeatherInfo(current.weathercode).label }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-[10rem] leading-none mb-4 drop-shadow-2xl"
                        >
                            {{ getWeatherInfo(current.weathercode).icon }}
                        </div>
                    </div>
                </div>

                <div
                    class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 pt-8 border-t border-white/20"
                >
                    <div class="text-center">
                        <div
                            class="text-blue-100 text-sm mb-1 uppercase tracking-wider font-bold"
                        >
                            Wind Speed
                        </div>
                        <div class="text-2xl font-bold">
                            {{ current.windspeed }} km/h
                        </div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-blue-100 text-sm mb-1 uppercase tracking-wider font-bold"
                        >
                            Wind Direction
                        </div>
                        <div class="text-2xl font-bold">
                            {{ current.winddirection }}°
                        </div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-blue-100 text-sm mb-1 uppercase tracking-wider font-bold"
                        >
                            Time
                        </div>
                        <div class="text-2xl font-bold">
                            {{
                                new Date(current.time).toLocaleTimeString([], {
                                    hour: "2-digit",
                                    minute: "2-digit",
                                })
                            }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-blue-100 text-sm mb-1 uppercase tracking-wider font-bold"
                        >
                            Condition
                        </div>
                        <div class="text-2xl font-bold">
                            {{ getWeatherInfo(current.weathercode).label }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Forecast -->
            <div
                class="bg-white dark:bg-gray-900 rounded-[3rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm"
            >
                <h3
                    class="text-2xl font-bold mb-6 px-4 text-gray-900 dark:text-white"
                >
                    Hourly Forecast
                </h3>
                <div class="flex overflow-x-auto pb-4 space-x-6 scrollbar-hide">
                    <div
                        v-for="(time, index) in hourly.time.slice(0, 24)"
                        :key="time"
                        class="flex-shrink-0 w-24 flex flex-col items-center p-4 rounded-3xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    >
                        <span class="text-sm font-bold text-gray-500 mb-3">
                            {{ new Date(time).getHours() }}:00
                        </span>
                        <span class="text-2xl mb-3">🌡️</span>
                        <span
                            class="text-xl font-black text-gray-900 dark:text-white"
                        >
                            {{ Math.round(hourly.temperature_2m[index]) }}°
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-20">
            <div class="text-6xl mb-4">😰</div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                Failed to load weather data.
            </h3>
            <p class="text-gray-500 mt-2">Please try again later.</p>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
