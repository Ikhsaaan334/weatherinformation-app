<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import Checkbox from '@/components/Checkbox.vue';
import { useAuthStore } from '@/stores/auth';
import { extractErrors } from '@/lib/errors';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const form = reactive({ email: '', password: '', remember: false });
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await auth.login(form.email, form.password);
        const redirect = (route.query.redirect as string) || '/dashboard';
        router.push(redirect);
    } catch (e) {
        errors.value = extractErrors(e);
        form.password = '';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <GuestLayout>
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">Welcome Back</h1>
            <p class="text-gray-500 mt-2">Enter your credentials to access your dashboard</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@company.com"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div>
                <div class="flex justify-between items-center">
                    <InputLabel for="password" value="Password" />
                    <RouterLink to="/forgot-password" class="text-xs font-bold text-blue-600 hover:text-blue-500 transition">
                        Forgot password?
                    </RouterLink>
                </div>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox v-model="form.remember" />
                    <span class="ms-2 text-sm text-gray-500">Stay logged in</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-lg rounded-2xl shadow-xl shadow-blue-600/30 transition-all active:scale-[0.98]"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                >
                    Sign In
                </PrimaryButton>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-gray-500">
                    Don't have an account?
                    <RouterLink to="/register" class="font-bold text-blue-600 hover:text-blue-500 transition">Create account</RouterLink>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
