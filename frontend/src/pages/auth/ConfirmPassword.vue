<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';
import { useToast } from '@/lib/toast';

const router = useRouter();
const toast = useToast();

const form = reactive({ password: '' });
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await api.post('/confirm-password', { password: form.password });
        toast.success('Password confirmed.');
        router.back();
    } catch (e) {
        errors.value = extractErrors(e);
    } finally {
        processing.value = false;
        form.password = '';
    }
};
</script>

<template>
    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autofocus autocomplete="current-password" />
                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">Confirm</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
