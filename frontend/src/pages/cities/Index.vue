<script setup lang="ts">
import { onMounted, reactive, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import Modal from '@/components/Modal.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import api from '@/lib/axios';
import { extractErrors, errorMessage } from '@/lib/errors';
import { useToast } from '@/lib/toast';
import { useAuthStore } from '@/stores/auth';
import type { City, Paginator } from '@/types';

const auth = useAuthStore();
const toast = useToast();

const cities = ref<Paginator<City> | null>(null);
const canManage = ref(false);
const search = ref('');
const loading = ref(false);

const fetchCities = async (url = '/cities') => {
    loading.value = true;
    try {
        // url is either the relative '/cities' (with params) or an absolute
        // paginator link returned by Laravel.
        const { data } = await api.get(url, url === '/cities' ? { params: { search: search.value } } : undefined);
        cities.value = data.cities;
        canManage.value = data.can?.manage_cities ?? auth.canManageCities;
    } finally {
        loading.value = false;
    }
};

let debounce: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => fetchCities(), 300);
});

onMounted(() => fetchCities());

// --- Create / Edit ---
const showForm = ref(false);
const editing = ref<City | null>(null);
const form = reactive({ name: '', country: '', lat: '', lon: '' });
const errors = ref<Record<string, string>>({});
const processing = ref(false);

const resetForm = () => {
    form.name = '';
    form.country = '';
    form.lat = '';
    form.lon = '';
    errors.value = {};
};

const openCreate = () => {
    editing.value = null;
    resetForm();
    showForm.value = true;
};

const openEdit = (city: City) => {
    editing.value = city;
    form.name = city.name;
    form.country = city.country;
    form.lat = String(city.lat);
    form.lon = String(city.lon);
    errors.value = {};
    showForm.value = true;
};

const submit = async () => {
    processing.value = true;
    errors.value = {};
    try {
        if (editing.value) {
            await api.put(`/cities/${editing.value.id}`, { ...form });
            toast.success('City updated successfully.');
        } else {
            await api.post('/cities', { ...form });
            toast.success('City created successfully.');
        }
        showForm.value = false;
        await fetchCities();
    } catch (e) {
        errors.value = extractErrors(e);
        if (Object.keys(errors.value).length === 0) toast.error(errorMessage(e));
    } finally {
        processing.value = false;
    }
};

// --- Delete ---
const deleting = ref<City | null>(null);

const confirmDelete = async () => {
    if (!deleting.value) return;
    try {
        await api.delete(`/cities/${deleting.value.id}`);
        toast.success('City deleted successfully.');
        deleting.value = null;
        await fetchCities();
    } catch (e) {
        toast.error(errorMessage(e));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">Explore Cities</h2>
                <PrimaryButton v-if="canManage" type="button" @click="openCreate">Add City</PrimaryButton>
            </div>
        </template>

        <div class="mb-6">
            <TextInput v-model="search" type="text" class="w-full max-w-md" placeholder="Search by city or country..." />
        </div>

        <div v-if="cities" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="city in cities.data"
                :key="city.id"
                class="group bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl hover:border-blue-500/30 transition-all duration-300 relative overflow-hidden"
            >
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500 pointer-events-none"></div>

                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ city.name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">{{ city.country }}</p>
                    </div>
                    <div v-if="canManage" class="relative z-10 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg" @click="openEdit(city)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                        <button type="button" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" @click="deleting = city">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center text-sm text-gray-400 dark:text-gray-500 mb-6">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span>{{ city.lat }}, {{ city.lon }}</span>
                </div>

                <RouterLink
                    :to="`/cities/${city.id}/weather`"
                    class="block w-full text-center py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-bold rounded-2xl hover:bg-blue-600 hover:text-white transition-all duration-300"
                >
                    View Weather
                </RouterLink>
            </div>
        </div>

        <div v-if="cities && cities.data.length === 0 && !loading" class="text-center py-16 text-gray-400">No cities found.</div>

        <!-- Pagination -->
        <div v-if="cities" class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <button
                    v-for="link in cities.links"
                    :key="link.label"
                    type="button"
                    :disabled="!link.url"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                    :class="[
                        link.active ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
                        !link.url ? 'opacity-50 cursor-not-allowed' : '',
                    ]"
                    @click="link.url && fetchCities(link.url)"
                    v-html="link.label"
                />
            </nav>
        </div>

        <!-- Create / Edit Modal -->
        <Modal :show="showForm" @close="showForm = false">
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">{{ editing ? 'Edit City' : 'Add New City' }}</h2>

                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="City Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div>
                        <InputLabel for="country" value="Country Code (e.g., ID, US)" />
                        <TextInput id="country" v-model="form.country" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="errors.country" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="lat" value="Latitude" />
                            <TextInput id="lat" v-model="form.lat" type="number" step="any" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="errors.lat" />
                        </div>
                        <div>
                            <InputLabel for="lon" value="Longitude" />
                            <TextInput id="lon" v-model="form.lon" type="number" step="any" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="errors.lon" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-8 space-x-3">
                        <SecondaryButton type="button" @click="showForm = false">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                            {{ editing ? 'Save Changes' : 'Create' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="!!deleting" @close="deleting = null">
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">Delete City</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Are you sure you want to delete
                    <span class="font-semibold text-gray-900 dark:text-white">{{ deleting?.name }}</span>
                    ? This action cannot be undone.
                </p>
                <div class="mt-8 flex justify-end space-x-3">
                    <SecondaryButton type="button" @click="deleting = null">Cancel</SecondaryButton>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        @click="confirmDelete"
                    >
                        Delete City
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
