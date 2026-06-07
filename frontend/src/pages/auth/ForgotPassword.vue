<script setup lang="ts">
import { reactive, ref } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';

const form = reactive({ email: '' });
const errors = ref<Record<string, string>>({});
const status = ref('');
const processing = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    status.value = '';
    try {
        const { data } = await api.post('/forgot-password', { email: form.email });
        status.value = data.message;
    } catch (e) {
        errors.value = extractErrors(e);
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to
            choose a new one.
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus autocomplete="username" />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">Email Password Reset Link</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
