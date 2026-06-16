import axios from 'axios';

const TOKEN_KEY = 'skycast_token';

export function getToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token: string): void {
    localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
    localStorage.removeItem(TOKEN_KEY);
}

// All API calls go through this instance. Absolute URLs (e.g. signed email
// verification links) bypass the baseURL automatically. Fall back to the local
// API so a missing .env doesn't send requests to "undefined/api".
const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const api = axios.create({
    baseURL: `${apiBaseUrl}/api`,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

api.interceptors.request.use((config) => {
    const token = getToken();
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// A single handler (registered from main.ts) reacts to expired/invalid tokens
// so we don't create a circular dependency between axios, the store and router.
let onUnauthorized: (() => void) | null = null;

export function setUnauthorizedHandler(handler: () => void): void {
    onUnauthorized = handler;
}

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && onUnauthorized) {
            onUnauthorized();
        }
        return Promise.reject(error);
    },
);

export default api;
