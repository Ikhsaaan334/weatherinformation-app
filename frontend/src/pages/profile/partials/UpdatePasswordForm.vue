<script setup lang="ts">
import { reactive, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { extractErrors } from '@/lib/errors';

const form = reactive({ current_password: '', password: '', password_confirmation: '' });
const errors = ref<Record<string, string>>({});
const processing = ref(false);
const recentlySuccessful = ref(false);

const updatePassword = async () => {
    processing.value = true;
    errors.value = {};
    try {
        await api.put('/password', { ...form });
        form.current_password = '';
        form.password = '';
        form.password_confirmation = '';
        recentlySuccessful.value = true;
        setTimeout(() => (recentlySuccessful.value = false), 2000);
    } catch (e) {
        errors.value = extractErrors(e);
        if (errors.value.password) {
            form.password = '';
            form.password_confirmation = '';
        }
        if (errors.value.current_password) {
            form.current_password = '';
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="updatePassword">
            <div>
                <InputLabel for="current_password" value="Current Password" />
                <TextInput id="current_password" v-model="form.current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <InputError class="mt-2" :message="errors.current_password" />
            </div>

            <div>
                <InputLabel for="password" value="New Password" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <InputError class="mt-2" :message="errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <InputError class="mt-2" :message="errors.password_confirmation" />
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
