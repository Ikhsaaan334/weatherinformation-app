<script setup lang="ts">
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import type { PageProps } from "@/types";

const props = defineProps<{
    users: any;
    filters: {
        search?: string;
    };
    roles: string[];
}>();

const page = usePage<PageProps>();
const currentUserId = page.props.auth.user.id;

const search = ref(props.filters.search || "");

watch(search, (value) => {
    router.get(
        route("admin.users.index"),
        { search: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const updatingId = ref<number | null>(null);

const changeRole = (userId: number, role: string) => {
    updatingId.value = userId;
    router.patch(
        route("admin.users.update-role", userId),
        { role },
        {
            preserveScroll: true,
            onFinish: () => {
                updatingId.value = null;
            },
        },
    );
};

const badgeClass = (role?: string) =>
    role === "admin"
        ? "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300"
        : "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300";
</script>

<template>
    <Head title="Manage Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Manage Users
            </h2>
        </template>

        <div class="mb-6">
            <TextInput
                v-model="search"
                type="text"
                class="w-full max-w-md"
                placeholder="Search by name or email..."
            />
        </div>

        <div
            class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden"
        >
            <table class="w-full text-left">
                <thead
                    class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wider"
                >
                    <tr>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Current Role</th>
                        <th class="px-6 py-4 font-semibold">Change Role</th>
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                >
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
                    >
                        <td
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            {{ user.name }}
                            <span
                                v-if="user.id === currentUserId"
                                class="ml-2 text-xs text-gray-400"
                                >(you)</span
                            >
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold capitalize"
                                :class="badgeClass(user.role)"
                            >
                                {{ user.role || "none" }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <select
                                :value="user.role"
                                :disabled="
                                    user.id === currentUserId ||
                                    updatingId === user.id
                                "
                                @change="
                                    changeRole(
                                        user.id,
                                        ($event.target as HTMLSelectElement)
                                            .value,
                                    )
                                "
                                class="rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed capitalize"
                            >
                                <option
                                    v-for="role in roles"
                                    :key="role"
                                    :value="role"
                                >
                                    {{ role }}
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td
                            colspan="4"
                            class="px-6 py-10 text-center text-gray-400"
                        >
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                    :class="[
                        link.active
                            ? 'bg-blue-600 text-white'
                            : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
                        !link.url ? 'opacity-50 cursor-not-allowed' : '',
                    ]"
                />
            </nav>
        </div>
    </AuthenticatedLayout>
</template>
