import { createRouter, createWebHistory } from 'vue-router'

import LoginPage from '../views/LoginPage.vue'
import RegisterPage from '../views/RegisterPage.vue'
import ProfilePage from '../views/ProfilePage.vue'
import TermsPage from '../views/TermsPage.vue'
import HelpPage from '../views/HelpPage.vue'
import FeedPage from '../views/FeedPage.vue'
import CollectionsPage from '../views/CollectionsPage.vue'
import MessagesPage from '../views/MessagesPage.vue'
import PublicProfilePage from '../views/PublicProfilePage.vue'

const routes = [
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/login',
        name: 'Login',
        component: LoginPage,
    },
    {
        path: '/register',
        name: 'Register',
        component: RegisterPage,
    },
    {
        path: '/profile',
        name: 'Profile',
        component: ProfilePage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/help',
        name: 'Help',
        component: HelpPage,
    },
    {
        path: '/feed',
        name: 'Feed',
        component: FeedPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/collections',
        name: 'Collections',
        component: CollectionsPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/messages',
        name: 'Messages',
        component: MessagesPage,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/terms',
        name: 'Terms',
        component: TermsPage,
    },
    {
        path: '/profiles/:id',
        name: 'PublicProfile',
        component: PublicProfilePage,
        meta: {
            requiresAuth: true,
        },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('amw_token')

    if (to.meta.requiresAuth && !token) {
        next('/login')
        return
    }

    next()
})

export default router