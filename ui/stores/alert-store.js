import {defineStore} from 'pinia'

export const useAlertStore = defineStore('alert-store', {
    state: () => {
        return {
            show: false,
            type: 'success',
            title: 'Succès',
            message: '',
        }
    },
    actions: {
        reset() {
            this.show = false
            this.type = 'success'
            this.title = 'Succès'
            this.message = ''
        }
    }
})
