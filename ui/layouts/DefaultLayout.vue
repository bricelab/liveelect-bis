<template>
  <v-app theme="light">

    <AppTopBar :is-connected-user-page="true" />

    <v-main>
      <LoadingPage v-if="!scrutinStore.isInitialized" />
      <router-view v-else />
    </v-main>
  </v-app>
</template>

<script setup>
import {useScrutinStore} from '@/stores/scrutin-store'
import LoadingPage from '@/components/LoadingPage.vue'
import AppTopBar from '@/components/AppTopBar.vue'

const scrutinStore = useScrutinStore()

scrutinStore.initialize()
</script>

<style scoped>
</style>
