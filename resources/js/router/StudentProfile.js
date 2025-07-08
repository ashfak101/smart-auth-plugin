import { createRouter, createWebHashHistory } from "vue-router"

import AppPermission from '../components/Student/AppPermission.vue'
import Billing from '../components/Student/Billing.vue'
import ChangePassword from '../components/Student/ChangePassword.vue'
import History from '../components/Student/History.vue'
import Membership from '../components/Student/Membership.vue'
import MyCard from '../components/Student/MyCard.vue'
import MyProfile from '../components/Student/MyProfile.vue'
import SavedOffer from '../components/Student/SavedOffer.vue'
import VipMembership from '../components/Student/VipMembership.vue'

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
        path: '/my-card',
        name: 'my-card',
        component: MyCard
    },
    {
        path: '/vip-access',
        name: 'vip-access',
        component: VipMembership
    },
    {
        path: '/saved-offers',
        name: 'saved-offers',
        component: SavedOffer
    },
    {
        path: '/history',
        name: 'history',
        component: History
    },
    {
        path: '/billing',
        name: 'billing',
        component: Billing
    },
    {
        path: '/app-permission',
        name: 'app-permission',
        component: AppPermission
    },
    {
        path: '/change-password',
        name: 'change-password',
        component: ChangePassword
    },
    {
        path: '/choose-membership',
        name: 'choose-membership',
        component: Membership
    },
]

const router = createRouter({
    history: createWebHashHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const app_vars = window.student_card_app_vars
    const is_purchased = `${app_vars.is_purchased}`
    const is_remaining_days = `${app_vars.remaining_days}`
    const is_logged_in = `${app_vars.is_logged_in}`;

    if( ! is_logged_in ){
        window.location.href = `${app_vars.auth_page}`;
    }

    if (!is_purchased) {
        if (to.path !== '/choose-membership') {
            next({ path: '/choose-membership' })
        } else {
            next()
        }
    }
    else {
        if (to.path === '/choose-membership' && is_remaining_days > 30) {
            next({ path: '/my-profile' })
        } else {
            next()
        }
    }
})


export default router
