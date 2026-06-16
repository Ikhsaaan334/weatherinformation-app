export interface User {
    id: number;
    name: string;
    email: string;
    email_verified: boolean;
    email_verified_at: string | null;
    roles: string[];
    permissions: string[];
    can: {
        manage_cities: boolean;
    };
    created_at: string | null;
}

export interface City {
    id: number;
    name: string;
    state?: string | null;
    country: string;
    lat: number | string;
    lon: number | string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginator<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
}
