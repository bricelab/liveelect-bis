<template>
  <v-container id="loading__page__container">
    <v-row
        class="fill-height"
        align-content="center"
        justify="center"
    >
      <v-col
          class="text-subtitle-1 text-center"
          cols="12"
      >
        Veuillez patienter svp ...
      </v-col>
      <v-col cols="6">
        <v-progress-linear
            color="deep-purple-accent-4"
            indeterminate
            rounded
            height="7"
        ></v-progress-linear>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
</script>

<style scoped>
#loading__page__container {
  height: 100%;
}
</style>
