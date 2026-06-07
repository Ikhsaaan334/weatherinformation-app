import axios from 'axios';

/**
 * Flatten a Laravel 422 validation response into { field: firstMessage }.
 * Falls back to a generic message for non-validation errors.
 */
export function extractErrors(error: unknown): Record<string, string> {
    if (axios.isAxiosError(error) && error.response?.status === 422) {
        const bag = error.response.data?.errors ?? {};
        const out: Record<string, string> = {};
        for (const key of Object.keys(bag)) {
            out[key] = Array.isArray(bag[key]) ? bag[key][0] : String(bag[key]);
        }
        return out;
    }
    return {};
}

export function errorMessage(error: unknown, fallback = 'Something went wrong.'): string {
    if (axios.isAxiosError(error)) {
        return error.response?.data?.message ?? fallback;
    }
    return fallback;
}
