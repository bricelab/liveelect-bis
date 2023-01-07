<template>
  <v-form
      v-model="form"
      method="post"
  >
    <v-text-field
        :rules="[required]"
        class="mb-2"
        clearable
        label="Numéro de téléphone"
        variant="underlined"
        name="telephone"
        autofocus
        :autocomplete="false"
    ></v-text-field>

    <v-text-field
        :rules="[required]"
        clearable
        label="Code secret"
        placeholder="Entrez votre mot de passe"
        variant="underlined"
        name="code"
        type="password"
    ></v-text-field>

    <input
        name="_csrf_token"
        :value="csrfToken"
        type="hidden"
    />

    <br>

    <v-btn
        :disabled="!form"
        block
        color="green"
        size="large"
        type="submit"
        variant="flat"
    >
      Se connecter
    </v-btn>
  </v-form>
</template>

<script setup>
import {ref} from 'vue'

const form = ref(false)
const csrfToken = ref(window.csrfToken)

const required = (v) => !!v || 'Ce champ est obligatoire.'
</script>

<style scoped>

</style>
