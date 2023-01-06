import {defineStore} from 'pinia'
import {fetchScrutinData} from '@/services/scrutin-services'

export const useScrutinStore = defineStore('scrutin-store', {
    state: () => {
        return {
            scrutinId: 1,
            scrutin: {},
            candidats: [],
            departements: [],
            communes: [],
            arrondissements: [],
            isInitialized: false,
        }
    },
    getters: {
        sortedCandidats() {
            return this.candidats.sort((a, b) => {
                if (a.position < b.position) {
                    return -1;
                }
                if (a.position > b.position) {
                    return 1;
                }
                return 0;
            })
        },
    },
    actions: {
        async initialize() {
            this.isInitialized = false

            const data = await fetchScrutinData(this.scrutinId)

            this.scrutin = data.scrutin
            this.candidats = data.candidats
            this.departements = data.departements
            this.communes = data.communes
            this.arrondissements = data.arrondissements

            this.isInitialized = true
        }
    }
})
