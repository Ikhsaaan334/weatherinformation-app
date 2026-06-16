<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';
import { useToast } from '@/lib/toast';

const route = useRoute();
const router = useRouter();
const toast = useToast();

// token + email arrive as query params from the email link (see AppServiceProvider).
const form = reactive({
    token: (route.query.token as string) || '',
    email: (route.query.email as string) || '',
    password: '',
    password_confirmation: '',
});
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await api.post('/reset-password', { ...form });
        toast.success('Password reset. Please log in.');
        router.push('/login');
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
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">Reset Password</h1>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autofocus autocomplete="new-password" />
                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="errors.password_confirmation" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">Reset Password</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
