<template>
  <v-app-bar
      color="green"
      prominent
      class="text-center"
  >
    <v-app-bar-title class="text-uppercase">
      Live Elect
    </v-app-bar-title>
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
