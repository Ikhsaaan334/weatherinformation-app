<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import type { PageProps } from "@/types";

const page = usePage<PageProps>();

const visible = ref(false);
const text = ref("");
const type = ref<"success" | "error">("success");
let timer: ReturnType<typeof setTimeout> | undefined;

const flash = computed(() => page.props.flash);

watch(
    flash,
    (value) => {
        if (!value) return;

        if (value.error) {
            text.value = value.error;
            type.value = "error";
        } else if (value.message) {
            text.value = value.message;
            type.value = "success";
        } else {
            return;
        }

        visible.value = true;
        clearTimeout(timer);
        timer = setTimeout(() => (visible.value = false), 4000);
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="visible"
            class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 rounded-2xl px-5 py-4 shadow-xl border backdrop-blur-md"
            :class="
                type === 'success'
                    ? 'bg-green-50/90 border-green-200 text-green-800 dark:bg-green-900/40 dark:border-green-800 dark:text-green-200'
                    : 'bg-red-50/90 border-red-200 text-red-800 dark:bg-red-900/40 dark:border-red-800 dark:text-red-200'
            "
            role="status"
        >
            <svg
                v-if="type === 'success'"
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                />
            </svg>
            <svg
                v-else
                class="w-5 h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <span class="font-medium text-sm">{{ text }}</span>
            <button
                type="button"
                class="ml-2 opacity-60 hover:opacity-100 transition-opacity"
                @click="visible = false"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
    </Transition>
</template>
