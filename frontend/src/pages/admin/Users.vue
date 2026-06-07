<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import TextInput from '@/components/TextInput.vue';
import api from '@/lib/axios';
import { errorMessage } from '@/lib/errors';
import { useToast } from '@/lib/toast';
import { useAuthStore } from '@/stores/auth';
import type { Paginator } from '@/types';

interface UserRow {
    id: number;
    name: string;
    email: string;
    role: string | null;
    created_at: string | null;
}

const auth = useAuthStore();
const toast = useToast();
const currentUserId = auth.user?.id;

const users = ref<Paginator<UserRow> | null>(null);
const roles = ref<string[]>([]);
const search = ref('');
const updatingId = ref<number | null>(null);

const fetchUsers = async (url = '/admin/users') => {
    const { data } = await api.get(url, url === '/admin/users' ? { params: { search: search.value } } : undefined);
    users.value = data.users;
    roles.value = data.roles;
};

let debounce: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => fetchUsers(), 300);
});

onMounted(() => fetchUsers());

const changeRole = async (userId: number, role: string) => {
    updatingId.value = userId;
    try {
        const { data } = await api.patch(`/admin/users/${userId}/role`, { role });
        toast.success(data.message);
        await fetchUsers(users.value ? `/admin/users?page=${users.value.current_page}` : '/admin/users');
    } catch (e) {
        toast.error(errorMessage(e));
    } finally {
        updatingId.value = null;
    }
};

const badgeClass = (role?: string | null) =>
    role === 'admin'
        ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
        : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">Manage Users</h2>
        </template>

        <div class="mb-6">
            <TextInput v-model="search" type="text" class="w-full max-w-md" placeholder="Search by name or email..." />
        </div>

        <div v-if="users" class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Current Role</th>
                        <th class="px-6 py-4 font-semibold">Change Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ user.name }}
                            <span v-if="user.id === currentUserId" class="ml-2 text-xs text-gray-400">(you)</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold capitalize" :class="badgeClass(user.role)">
                                {{ user.role || 'none' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <select
                                :value="user.role || ''"
                                :disabled="user.id === currentUserId || updatingId === user.id"
                                class="rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed capitalize"
                                @change="changeRole(user.id, ($event.target as HTMLSelectElement).value)"
                            >
                                <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">No users found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="users" class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <button
                    v-for="link in users.links"
                    :key="link.label"
                    type="button"
                    :disabled="!link.url"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                    :class="[
                        link.active ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
                        !link.url ? 'opacity-50 cursor-not-allowed' : '',
                    ]"
                    @click="link.url && fetchUsers(link.url)"
                    v-html="link.label"
                />
            </nav>
        </div>
    </AuthenticatedLayout>
</template>
