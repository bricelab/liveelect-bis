<template>
  <v-container>
    <v-form
        ref="form"
        v-model="valid"
        lazy-validation
    >
      <v-row>
        <v-col cols="12">
          <h3 class="text-subtitle-2 text-green">
            <span class="fw-bold">{{ scrutinStore.scrutin.name }}</span>
          </h3>
        </v-col>
        <v-col cols="12">
          <div class="text-h5 text-green">
            Remontée des résultats
          </div>
        </v-col>

        <v-col cols="12" class="mt-5" v-if="alertStore.show">
          <v-alert :type="alertStore.type" :title="alertStore.title" closable>{{ alertStore.message }}</v-alert>
        </v-col>

        <v-col cols="12">
          <v-autocomplete
              label="Département"
              clearable
              clear-icon="mdi-close-circle"
              :items="departements"
              type="object"
              item-title="nom"
              item-value="id"
              variant="underlined"
              v-model="departement"
              @update:modelValue="updateCommuneValues"
          ></v-autocomplete>

          <v-autocomplete
              label="Commune"
              clearable
              clear-icon="mdi-close-circle"
              :items="communes"
              type="object"
              item-title="nom"
              item-value="id"
              variant="underlined"
              v-model="commune"
              @update:modelValue="updateArrondissementValues"
          ></v-autocomplete>

          <v-select
              label="Arrondissement"
              clearable
              clear-icon="mdi-close-circle"
              :items="arrondissements"
              type="object"
              item-title="nom"
              item-value="id"
              variant="underlined"
              v-model="arrondissement"
          ></v-select>

          <v-text-field
              label="Nombre d'inscrits"
              type="number"
              v-model="inscrits"
              clearable
              clear-icon="mdi-close-circle"
              variant="underlined"
          ></v-text-field>

          <v-text-field
              label="Nombre de votants"
              type="number"
              v-model="votants"
              clearable
              clear-icon="mdi-close-circle"
              variant="underlined"
          ></v-text-field>

          <v-text-field
              label="Bulletins nuls"
              type="number"
              v-model="nuls"
              clearable
              clear-icon="mdi-close-circle"
              variant="underlined"
          ></v-text-field>

          <h1 class="text-h5 text-green mt-3 mb-3">Suffrages obtenus</h1>

          <v-row
              v-for="c in scrutinStore.sortedCandidats"
              :key="c.id"
              class="mt-5"
          >
            <v-col cols="4">
              <v-img
                  :src="`/uploads/candidats/logos/${c.logo}`"
                  aspect-ratio="1"
                  cover
                  style="height: 100px; width: 100px;"
              ></v-img>
            </v-col>
            <v-col cols="8">
              <v-text-field
                  :label="c.sigle"
                  type="number"
                  v-model="suffrages[c.id]"
                  clearable
                  clear-icon="mdi-close-circle"
                  variant="underlined"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" class="text-end">

          <v-btn
              color="success"
              :loading="loading"
              :disabled="!valid"
              @click="validate"
          >
            Valider
          </v-btn>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>

<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useAlertStore} from '@/stores/alert-store'
import {useScrutinStore} from '@/stores/scrutin-store'
import {remonterResultatsParArrondissement} from '@/services/scrutin-services'

const router = useRouter()
const alertStore = useAlertStore()
const scrutinStore = useScrutinStore()

alertStore.reset()

const departements = ref([])
const communes = ref([])
const arrondissements = ref([])

communes.value = scrutinStore.communes.filter((cm) => {
  return  scrutinStore.arrondissements.filter((arr) => {
    return arr.commune.id === cm.id && !arr.estRemonte
  }).length > 0
})

departements.value = scrutinStore.departements.filter((dp) => {
  return  communes.value.filter((cm) => {
    return cm.departement.id === dp.id
  }).length > 0
})

const departement = ref('')
const commune = ref('')
const arrondissement = ref('')
const inscrits = ref('')
const votants = ref('')
const nuls = ref('')

const suffrages = ref([])

const form = ref()
const loading = ref(false)
const valid = ref(false)
const rules = [v => v.length >= 3 || 'Minimum 03 caractères']

const updateCommuneValues = () => {
  commune.value = ''
  arrondissement.value = ''
  communes.value = scrutinStore.communes.filter(cm => {
    return  scrutinStore.arrondissements.filter((arr) => {
      return arr.commune.id === cm.id && !arr.estRemonte && cm.departement.id === departement.value
    }).length > 0
  })
}

const updateArrondissementValues = () => {
  arrondissement.value = ''
  arrondissements.value = scrutinStore.arrondissements.filter(arr => !arr.estRemonte && arr.commune.id === commune.value)
}

const validate = async () => {
  valid.value = await form.value.validate()

  if (valid.value) {
    try {
      await remonterResultatsParArrondissement({
        scrutin: scrutinStore.scrutinId,
        arrondissement: arrondissement.value,
        inscrits: inscrits.value,
        votants: votants.value,
        nuls: nuls.value,
        suffrages: suffrages.value,
      })
      await scrutinStore.initialize()
      alertStore.type = 'success'
      alertStore.title = 'Succès'
      alertStore.message = 'Informations soumises avec succès !'
    } catch (e) {
      alertStore.type = 'error'
      alertStore.title = 'Erreur'
      alertStore.message = `${e.response.data.detail}. Veuillez vérifier svp !`
    }
    alertStore.show = true
    loading.value = false
  }
}
</script>

<style scoped>

</style>
