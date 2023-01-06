import {axios} from '@/modules/axios/axios'

/**
 * @typedef {Object} UserInfos
 * @property {Number} id ID de l'utilisateur
 * @property {string} nom nom de l'utilisateur
 * @property {string} prenoms Prénoms de l'utilisateur
 * @property {string} email Adresse mail de l'utilisateur
 * @property {Arrondissement} arrondissement Arrondissement supervisé de l'utilisateur
 */

/**
 * @typedef {Object} Arrondissement
 * @property {Number} id ID de l'arrondissement
 * @property {string} nom nom de l'arrondissement
 * @property {string} commune URI de la commune
 * @property {boolean} rapportOuvertureRempli Spécifie si le rapport d'ouverture a déjà été soumis
 */

/**
 * Permet de récupérer les infos d'un utilisateur
 *
 * @return {Promise<UserInfos>}
 */
export function getUserInfos() {
    return axios.get('/api/me').then((response) => {
        return {
            id: response.data.user.id,
            nom: response.data.user.nom,
            prenoms: response.data.user.prenoms,
            email: response.data.user.email,
            arrondissement: {
                id: response.data.user.arrondissementCouvert.id,
                nom: response.data.user.arrondissementCouvert.nom,
                commune: response.data.user.arrondissementCouvert.communeUri,
                rapportOuvertureRempli: response.data.user.arrondissementCouvert.rapportOuvertureRempli,
                // postesTotal: response.data.postesTotal,
                // postesRemontes: response.data.postesRemontes,
                incidentsSignales: response.data.incidentsSignales,
                centresVote: response.data.centresVote,
                postesVote: response.data.postesVote,
            },
            candidats: response.data.candidats,
        }
    })
}
