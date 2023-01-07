<template>
  <v-app-bar
      color="green"
      prominent
      class="text-left text-md-center"
  >
    <v-app-bar-title class="text-uppercase">
      Live Elect
    </v-app-bar-title>

    <v-menu v-if="isConnectedUserPage">
      <template v-slot:activator="{ props }">
        <a href="/logout" class="mr-3">
          <v-icon icon="mdi-logout"></v-icon>
        </a>
      </template>
    </v-menu>
  </v-app-bar>
</template>

<script setup>
defineProps({
  isConnectedUserPage: {
    type: Boolean,
    required: true,
  }
})
</script>

<style scoped>
#logout__menu {
  cursor: pointer;
}
</style>
