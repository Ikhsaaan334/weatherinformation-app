import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'welcome',
            component: () => import('@/pages/Welcome.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('@/pages/auth/Login.vue'),
            meta: { guestOnly: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('@/pages/auth/Register.vue'),
            meta: { guestOnly: true },
        },
        {
            path: '/forgot-password',
            name: 'password.request',
            component: () => import('@/pages/auth/ForgotPassword.vue'),
            meta: { guestOnly: true },
        },
        {
            path: '/reset-password',
            name: 'password.reset',
            component: () => import('@/pages/auth/ResetPassword.vue'),
            meta: { guestOnly: true },
        },
        {
            // Lands here from the verification email (?verify_url=...) and also
            // serves as the "please verify" notice for logged-in users.
            path: '/verify-email',
            name: 'verification.notice',
            component: () => import('@/pages/auth/VerifyEmail.vue'),
        },
        {
            path: '/confirm-password',
            name: 'password.confirm',
            component: () => import('@/pages/auth/ConfirmPassword.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: () => import('@/pages/Dashboard.vue'),
            meta: { requiresAuth: true, requiresVerified: true },
        },
        {
            path: '/cities',
            name: 'cities.index',
            component: () => import('@/pages/cities/Index.vue'),
            meta: { requiresAuth: true, requiresVerified: true },
        },
        {
            path: '/cities/:id/weather',
            name: 'cities.weather',
            component: () => import('@/pages/cities/Weather.vue'),
            meta: { requiresAuth: true, requiresVerified: true },
        },
        {
            path: '/admin/users',
            name: 'admin.users.index',
            component: () => import('@/pages/admin/Users.vue'),
            meta: { requiresAuth: true, requiresAdmin: true, requiresVerified: true },
        },
        {
            path: '/profile',
            name: 'profile.edit',
            component: () => import('@/pages/profile/Edit.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/',
        },
    ],
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    // Hydrate the user once per session if we hold a token but no profile yet.
    if (auth.token && !auth.user) {
        try {
            await auth.fetchUser();
        } catch {
            auth.clear();
        }
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'dashboard' };
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return { name: 'dashboard' };
    }

    if (to.meta.requiresVerified && auth.isAuthenticated && !auth.isVerified) {
        return { name: 'verification.notice' };
    }

    return true;
});

export default router;
