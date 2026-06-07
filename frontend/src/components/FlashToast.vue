<script setup lang="ts">
import { useToast } from '@/lib/toast';

const toast = useToast();
</script>

<template>
    <div class="fixed bottom-6 right-6 z-[60] flex flex-col gap-3">
        <TransitionGroup
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-for="item in toast.items"
                :key="item.id"
                class="flex items-center gap-3 rounded-2xl border px-5 py-4 shadow-xl backdrop-blur-md"
                :class="
                    item.type === 'success'
                        ? 'bg-green-50/90 border-green-200 text-green-800 dark:bg-green-900/40 dark:border-green-800 dark:text-green-200'
                        : 'bg-red-50/90 border-red-200 text-red-800 dark:bg-red-900/40 dark:border-red-800 dark:text-red-200'
                "
                role="status"
            >
                <svg v-if="item.type === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-sm">{{ item.text }}</span>
                <button type="button" class="ml-2 opacity-60 hover:opacity-100 transition-opacity" @click="toast.dismiss(item.id)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
