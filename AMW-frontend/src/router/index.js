// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// Importa tus vistas
import Login from '../views/login.vue';
import Register from '../views/register.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
    },
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;