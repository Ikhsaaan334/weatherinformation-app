import { defineStore } from 'pinia';
import api, { clearToken, getToken, setToken } from '@/lib/axios';
import type { User } from '@/types';

interface AuthState {
    token: string | null;
    user: User | null;
}

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        token: getToken(),
        user: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.roles.includes('admin') ?? false,
        isVerified: (state) => state.user?.email_verified ?? false,
        canManageCities: (state) => state.user?.can.manage_cities ?? false,
    },

    actions: {
        applyToken(token: string) {
            this.token = token;
            setToken(token);
        },

        async login(email: string, password: string) {
            const { data } = await api.post('/login', { email, password });
            this.applyToken(data.token);
            this.user = data.user;
        },

        async register(payload: {
            name: string;
            email: string;
            password: string;
            password_confirmation: string;
        }) {
            const { data } = await api.post('/register', payload);
            this.applyToken(data.token);
            this.user = data.user;
        },

        async fetchUser() {
            const { data } = await api.get('/user');
            this.user = data.data ?? data;
        },

        async logout() {
            try {
                await api.post('/logout');
            } finally {
                this.clear();
            }
        },

        clear() {
            this.token = null;
            this.user = null;
            clearToken();
        },
    },
});
