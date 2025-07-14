import { createRouter, createWebHashHistory } from "vue-router"


import ChangePassword from '../components/Student/ChangePassword.vue'

import MyProfile from '../components/Student/MyProfile.vue'


const routes = [
    {
        path: '/',
        redirect: '/my-profile',

    },
    {
        path: '/my-profile',
        name: 'my-profile',
        component: MyProfile
    },

    {
        path: '/change-password',
        name: 'change-password',
        component: ChangePassword
    },

]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})




export default router
