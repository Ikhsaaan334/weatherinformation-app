<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { useAuthStore } from '@/stores/auth';
import { extractErrors } from '@/lib/errors';

const router = useRouter();
const auth = useAuthStore();

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await auth.register({ ...form });
        // New accounts are unverified — the router will route to /verify-email.
        router.push('/dashboard');
    } catch (e) {
        errors.value = extractErrors(e);
        form.password = '';
        form.password_confirmation = '';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <GuestLayout>
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">Join the Elite</h1>
            <p class="text-gray-500 mt-2">Get precision weather data at your fingertips</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Business Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                    required
                    autocomplete="username"
                    placeholder="john@company.com"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-lg rounded-2xl shadow-xl shadow-blue-600/30 transition-all active:scale-[0.98]"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                >
                    Create Account
                </PrimaryButton>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-gray-500">
                    Already using SkyCast Pro?
                    <RouterLink to="/login" class="font-bold text-blue-600 hover:text-blue-500 transition">Log In instead</RouterLink>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
