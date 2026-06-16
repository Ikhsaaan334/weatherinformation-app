import { reactive } from 'vue';

export type ToastType = 'success' | 'error';

export interface Toast {
    id: number;
    text: string;
    type: ToastType;
}

const state = reactive<{ items: Toast[] }>({ items: [] });
let counter = 0;

function push(text: string, type: ToastType) {
    const id = ++counter;
    state.items.push({ id, text, type });
    setTimeout(() => dismiss(id), 4000);
}

export function dismiss(id: number) {
    const index = state.items.findIndex((t) => t.id === id);
    if (index !== -1) {
        state.items.splice(index, 1);
    }
}

export function useToast() {
    return {
        items: state.items,
        success: (text: string) => push(text, 'success'),
        error: (text: string) => push(text, 'error'),
        dismiss,
    };
}
