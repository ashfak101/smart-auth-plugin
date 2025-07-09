import { createWebHashHistory, createRouter } from "vue-router";

import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import ForgotPassword from "../components/Auth/ForgotPassword.vue"
import NewPassword from "../components/Auth/NewPassword.vue"


const routes = [
    {
        path: '/',
        redirect: '/sign-in'
    },
    {
        path: '/sign-in',
        name: 'sign-in',
        component: Login
    },
    {
        path: '/sign-up',
        name: 'sign-up',
        component: Register
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword
    },
    {
        path: '/reset-password/',
        name: 'reset-password',
        component: NewPassword,
    }


];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

export default router;
