import { createWebHashHistory, createRouter } from "vue-router";

import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';


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
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

export default router;
