export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        roles: string[];
    };
    can?: {
        manage_cities: boolean;
        manage_users: boolean;
    };
    flash: {
        message: string | null;
        error: string | null;
    };
};
