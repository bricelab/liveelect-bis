
import DefaultLayout from '@/layouts/DefaultLayout'

export const routes = [
    {
        path: '/scrutin',
        name: 'Default',
        component: DefaultLayout,
        redirect: '/scrutin/remonter-par-arrondissement',
        children: [
            {
                name: 'Home',
                path: '/scrutin/remonter-par-arrondissement',
                component: () => import('@/views/main/Home.vue'),
            },
        ]
    },
]
