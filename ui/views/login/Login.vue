<template>
  <v-app theme="light">

    <AppTopBar :is-connected-user-page="false" />

    <v-main>
      <v-card class="mx-auto px-6 py-8 mt-5" max-width="344">
        <v-alert type="error" v-if="isError" class="mb-5">{{ error }}</v-alert>
        <LoginForm />
      </v-card>
    </v-main>
  </v-app>
</template>

<script setup>
import {ref} from 'vue'
import AppTopBar from '@/components/AppTopBar.vue'
import LoginForm from '@/components/LoginForm.vue'

const error = ref(null)
const isError = ref(false)

if (window.error) {
  error.value = window.error
  isError.value = true
}
</script>
