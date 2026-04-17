<template>
  <q-layout view="hHh lpR fFf">
    <NavBar />
    <q-page-container>
      <router-view />
    </q-page-container>
    <AppFooter />
  </q-layout>
</template>

<script setup>
import NavBar    from 'src/components/NavBar.vue'
import AppFooter from 'src/components/AppFooter.vue'
</script>
