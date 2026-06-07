<script setup lang="ts">
import { reactive, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

const form = reactive({
    name: auth.user?.name ?? '',
    email: auth.user?.email ?? '',
});
const errors = ref<Record<string, string>>({});
const processing = ref(false);
const recentlySuccessful = ref(false);
const linkSent = ref(false);

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        const { data } = await api.patch('/profile', { ...form });
        auth.user = data.user;
        recentlySuccessful.value = true;
        setTimeout(() => (recentlySuccessful.value = false), 2000);
    } catch (e) {
        errors.value = extractErrors(e);
    } finally {
        processing.value = false;
    }
};

const resendVerification = async () => {
    await api.post('/email/verification-notification');
    linkSent.value = true;
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your account's profile information and email address.</p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div v-if="auth.user && !auth.user.email_verified">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <button
                        type="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                        @click="resendVerification"
                    >
                        Click here to re-send the verification email.
                    </button>
                </p>
                <div v-show="linkSent" class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="processing">Save</PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
