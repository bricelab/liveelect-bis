import process from 'process'
import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'
import { routes } from '@/modules/router/routes'

export const router = createRouter ({
    // history: createWebHashHistory(process.env.BASE_URL),
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 }
    },
})
