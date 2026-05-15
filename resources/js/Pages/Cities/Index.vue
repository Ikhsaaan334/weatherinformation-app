<script setup lang="ts">
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps<{
    cities: any;
    filters: {
        search?: string;
    };
    can: {
        manage_cities: boolean;
    };
}>();

const search = ref(props.filters.search || "");

watch(search, (value) => {
    router.get(
        route("cities.index"),
        { search: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const isCreating = ref(false);
const isEditing = ref(false);
const editingCity = ref<any>(null);
const isDeleting = ref(false);
const deletingCity = ref<any>(null);

const form = useForm({
    name: "",
    country: "",
    lat: "",
    lon: "",
});

const openCreateModal = () => {
    form.reset();
    isCreating.value = true;
};

const openEditModal = (city: any) => {
    editingCity.value = city;
    form.name = city.name;
    form.country = city.country;
    form.lat = city.lat;
    form.lon = city.lon;
    isEditing.value = true;
};

const submit = () => {
    if (isCreating.value) {
        form.post(route("cities.store"), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route("cities.update", editingCity.value.id), {
            onSuccess: () => {
                isEditing.value = false;
                editingCity.value = null;
                form.reset();
            },
        });
    }
};

const deleteCity = (city: any) => {
    deletingCity.value = city;
    isDeleting.value = true;
};

const confirmDeleteCity = () => {
    if (!deletingCity.value) {
        return;
    }

    router.delete(route("cities.destroy", deletingCity.value.id), {
        onSuccess: () => {
            isDeleting.value = false;
            deletingCity.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const closeDeleteModal = () => {
    isDeleting.value = false;
    deletingCity.value = null;
};
</script>

<template>
    <Head title="Cities" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Explore Cities
                </h2>
                <PrimaryButton
                    v-if="can.manage_cities"
                    @click="openCreateModal"
                >
                    Add City
                </PrimaryButton>
            </div>
        </template>

        <div class="mb-6">
            <TextInput
                v-model="search"
                type="text"
                class="w-full max-w-md"
                placeholder="Search by city or country..."
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="city in cities.data"
                :key="city.id"
                class="group bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl hover:border-blue-500/30 transition-all duration-300 relative overflow-hidden"
            >
                <!-- Decorative Circle -->
                <div
                    class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500 pointer-events-none"
                ></div>

                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ city.name }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">
                            {{ city.country }}
                        </p>
                    </div>
                    <div
                        class="relative z-10 flex space-x-2 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity pointer-events-none group-hover:pointer-events-auto"
                    >
                        <button
                            type="button"
                            v-if="can.manage_cities"
                            @click="openEditModal(city)"
                            class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                />
                            </svg>
                        </button>
                        <button
                            type="button"
                            v-if="can.manage_cities"
                            @click="deleteCity(city)"
                            class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div
                    class="flex items-center text-sm text-gray-400 dark:text-gray-500 mb-6"
                >
                    <svg
                        class="w-4 h-4 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    <span>{{ city.lat }}, {{ city.lon }}</span>
                </div>

                <Link
                    :href="route('cities.weather', city.id)"
                    class="block w-full text-center py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-bold rounded-2xl hover:bg-blue-600 hover:text-white transition-all duration-300"
                >
                    View Weather
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <Link
                    v-for="link in cities.links"
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

        <!-- Create/Edit Modal -->
        <Modal
            :show="isCreating || isEditing"
            @close="isCreating = isEditing = false"
        >
            <div class="p-8">
                <h2
                    class="text-2xl font-bold mb-6 text-gray-900 dark:text-white"
                >
                    {{ isCreating ? "Add New City" : "Edit City" }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="City Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel
                            for="country"
                            value="Country Code (e.g., ID, US)"
                        />
                        <TextInput
                            id="country"
                            v-model="form.country"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.country"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="lat" value="Latitude" />
                            <TextInput
                                id="lat"
                                v-model="form.lat"
                                type="number"
                                step="any"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.lat"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel for="lon" value="Longitude" />
                            <TextInput
                                id="lon"
                                v-model="form.lon"
                                type="number"
                                step="any"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.lon"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-8 space-x-3">
                        <SecondaryButton @click="isCreating = isEditing = false"
                            >Cancel</SecondaryButton
                        >
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ isCreating ? "Create" : "Save Changes" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="isDeleting" @close="closeDeleteModal">
            <div class="p-8">
                <h2
                    class="text-2xl font-bold mb-3 text-gray-900 dark:text-white"
                >
                    Delete City
                </h2>

                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Are you sure you want to delete
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ deletingCity?.name }}
                    </span>
                    ? This action cannot be undone.
                </p>

                <div class="mt-8 flex justify-end space-x-3">
                    <SecondaryButton @click="closeDeleteModal">
                        Cancel
                    </SecondaryButton>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 dark:focus:ring-offset-gray-800 disabled:opacity-25"
                        :disabled="!deletingCity"
                        @click="confirmDeleteCity"
                    >
                        Delete City
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
