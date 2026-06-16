<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import GuestLayout from '@/layouts/GuestLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import api from '@/lib/axios';
import { useAuthStore } from '@/stores/auth';
import { useToast } from '@/lib/toast';
import { errorMessage } from '@/lib/errors';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const toast = useToast();

// 'verifying' | 'verified' | 'failed' | 'notice'
const mode = ref<'verifying' | 'verified' | 'failed' | 'notice'>('notice');
const message = ref('');
const sending = ref(false);
const linkSent = ref(false);

onMounted(async () => {
    const verifyUrl = route.query.verify_url as string | undefined;

    if (verifyUrl) {
        mode.value = 'verifying';
        try {
            // The signed URL is absolute (points at the API), so axios ignores baseURL.
            const { data } = await api.get(decodeURIComponent(verifyUrl));
            mode.value = 'verified';
            message.value = data.message ?? 'Email verified successfully.';
            if (auth.isAuthenticated) {
                await auth.fetchUser();
            }
        } catch (e) {
            mode.value = 'failed';
            message.value = errorMessage(e, 'The verification link is invalid or has expired.');
        }
        return;
    }

    // No verify link: this is the "please verify" notice for logged-in users.
    if (auth.isAuthenticated && auth.isVerified) {
        router.replace('/dashboard');
    }
});

const resend = async () => {
    sending.value = true;
    try {
        await api.post('/email/verification-notification');
        linkSent.value = true;
        toast.success('Verification link sent.');
    } catch (e) {
        toast.error(errorMessage(e));
    } finally {
        sending.value = false;
    }
};

const logout = async () => {
    await auth.logout();
    router.push('/login');
};
</script>

<template>
    <GuestLayout>
        <!-- Result of clicking the email link -->
        <template v-if="mode === 'verifying'">
            <p class="text-center text-gray-600 dark:text-gray-400">Verifying your email…</p>
        </template>

        <template v-else-if="mode === 'verified'">
            <div class="text-center space-y-6">
                <div class="text-5xl">✅</div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Email verified</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ message }}</p>
                <RouterLink
                    :to="auth.isAuthenticated ? '/dashboard' : '/login'"
                    class="inline-block px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl"
                >
                    Continue
                </RouterLink>
            </div>
        </template>

        <template v-else-if="mode === 'failed'">
            <div class="text-center space-y-6">
                <div class="text-5xl">⚠️</div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Verification failed</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ message }}</p>
                <PrimaryButton v-if="auth.isAuthenticated" type="button" :disabled="sending" @click="resend">
                    Resend Verification Email
                </PrimaryButton>
            </div>
        </template>

        <!-- Notice mode (logged in, not yet verified) -->
        <template v-else>
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If
                you didn't receive the email, we will gladly send you another.
            </div>

            <div v-if="linkSent" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                A new verification link has been sent to the email address you provided during registration.
            </div>

            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton type="button" :class="{ 'opacity-25': sending }" :disabled="sending" @click="resend">
                    Resend Verification Email
                </PrimaryButton>

                <button
                    type="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    @click="logout"
                >
                    Log Out
                </button>
            </div>
        </template>
    </GuestLayout>
</template>
