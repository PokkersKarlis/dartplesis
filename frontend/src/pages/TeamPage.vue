<template>
  <q-page class="dk-page">
    <TeamSection />
  </q-page>
</template>

<script setup>
import TeamSection from 'src/components/sections/TeamSection.vue'
import { useScrollReveal } from 'src/composables/useScrollReveal'

useScrollReveal()
</script>

<style scoped lang="scss">
.dk-page {
  background: #0a0a0a;
  min-height: 100vh;
}
</style>
