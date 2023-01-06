import {axios} from '@/modules/axios/axios'

/**
 * @typedef {Object} Departement
 * @property {Number} id ID du département
 * @property {string} nom Nom du département
 */

/**
 * @typedef {Object} Commune
 * @property {Number} id ID de la commune
 * @property {string} nom Nom de la commune
 * @property {Departement} departement Département de la commune
 */

/**
 * @typedef {Object} Arrondissement
 * @property {Number} id ID de l'arrondissement
 * @property {string} nom Nom de l'arrondissement
 * @property {boolean} estRemonte Statut de l'arrondissement
 * @property {Commune} departement Département de la commune
 */

/**
 * @typedef {Object} Scrutin
 * @property {Number} id ID du scrutin
 * @property {string} name Libellé du scrutin
 * @property {Number} year Année de dérouler du scrutin
 * @property {string} type Type du scrutin
 * @property {boolean} published Statut du scrutin
 * @property {string} publishedAt Statut du scrutin
 */

/**
 * @typedef {Object} Candidat
 * @property {Number} id ID du candidat
 * @property {string} nom Nom du candidat
 * @property {string} sigle Sigle du candidat
 * @property {Number} position Position du candidat sur le bulletin
 * @property {string} logo Logo du candidat
 * @property {Scrutin} scrutin Scrutin de participation du candidat
 */

/**
 * @typedef {Object} ScrutinData
 * @property {Scrutin} scrutin
 * @property {Candidat[]} candidats Nom du candidat
 * @property {Departement[]} departements Sigle du candidat
 * @property {Commune[]} communes Position du candidat sur le bulletin
 * @property {Arrondissement[]} arrondissements Logo du candidat
 */

/**
 * @typedef {Object} Resultat
 * @property {Number} scrutin ID du scrutin
 * @property {Number} arrondissement ID de l'arrondissement
 * @property {Number} inscrits Nombre d'inscrits dans l'arrondissement
 * @property {Number} votants Nombre de votants dans l'arrondissement
 * @property {Number} nuls Nombre de bulletins nuls dans l'arrondissement
 * @property {Array} suffrages Tableau de nombre de voix obtenu par chaque candidat
 */

/**
 * Permet de récupérer les données du scrutin
 *
 * @param {Number} scrutinId ID du scrutin
 * @return {Promise<ScrutinData>}
 */
export function fetchScrutinData(scrutinId) {
    return axios.get(`/api/scrutin/${scrutinId}/data`)
        .then((response) => {
            return {
                scrutin: response.data.scrutin,
                candidats: response.data.candidats,
                departements: response.data.departements,
                communes: response.data.communes,
                arrondissements: response.data.arrondissements,
            }
        })
}

/**
 * Permet de remonter les résultats à la fermeture des postes de vote par arrondissement
 *
 * @param {Resultat} result
 * @return {Promise<Object>}
 */
export function remonterResultatsParArrondissement({scrutin, arrondissement, inscrits, votants, nuls, suffrages}) {
    return axios.post(`/api/scrutin/${scrutin}/resultats/remonter-par-arrondissement`, {
        arrondissement,
        inscrits,
        votants,
        nuls,
        suffrages,
    }).then(() => {
        return {
            status: 'OK',
        }
    })
}
