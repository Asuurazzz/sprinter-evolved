import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from 'vue-router';

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: () => import('../pages/Home.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/auth/Login.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../pages/auth/Register.vue'),
    },
    {
        path: '/verify-email',
        name: 'verify-email',
        component: () => import('../pages/auth/VerifyEmail.vue'),
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../pages/auth/ForgotPassword.vue'),
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: () => import('../pages/auth/ResetPassword.vue'),
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, _from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        if (to.hash) {
            return { el: to.hash, behavior: 'smooth' };
        }
        return { top: 0 };
    },
});
