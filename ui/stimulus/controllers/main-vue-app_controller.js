import { Controller } from '@hotwired/stimulus'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import {router} from '@/modules/router/router'
import {vuetify} from '@/plugins/vuetify/vuetify'
import App from '@/views/App.vue'

export default class extends Controller {
    connect() {
        const app = createApp(App)
        const pinia = createPinia()

        app.use(pinia)
        app.use(router)
        app.use(vuetify)

        app.mount(this.element)
    }
}
