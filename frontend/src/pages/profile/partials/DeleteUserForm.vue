<script setup lang="ts">
import { nextTick, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import DangerButton from '@/components/DangerButton.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';
import { useAuthStore } from '@/stores/auth';
import { useToast } from '@/lib/toast';

const router = useRouter();
const auth = useAuthStore();
const toast = useToast();

const confirming = ref(false);
const passwordInput = ref<InstanceType<typeof TextInput> | null>(null);
const form = reactive({ password: '' });
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const confirmUserDeletion = () => {
    confirming.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await api.delete('/profile', { data: { password: form.password } });
        auth.clear();
        toast.success('Account deleted.');
        router.push('/');
    } catch (e) {
        errors.value = extractErrors(e);
        passwordInput.value?.focus();
    } finally {
        processing.value = false;
    }
};

const closeModal = () => {
    confirming.value = false;
    errors.value = {};
    form.password = '';
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download
                any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton type="button" @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirming" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Are you sure you want to delete your account?</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you
                    would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />
                    <InputError class="mt-2" :message="errors.password" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton type="button" @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton class="ms-3" type="button" :class="{ 'opacity-25': processing }" :disabled="processing" @click="deleteUser">
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
