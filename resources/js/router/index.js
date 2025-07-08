import { createWebHashHistory, createRouter } from "vue-router";
import Settings from '../components/Admin/Settings.vue';


const routes = [
    {
        path: '/',
        redirect: '/general'
    },
    {
        path: '/general',
        name: 'GeneralSettings',
        component: Settings
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

export default router;
