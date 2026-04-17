<template>
  <q-page class="dk-page">
    <div class="contact-layout">
      <JoinSection />
      <ContactSection />
    </div>
  </q-page>
</template>

<script setup>
import JoinSection    from 'src/components/sections/JoinSection.vue'
import ContactSection from 'src/components/sections/ContactSection.vue'
import { useScrollReveal } from 'src/composables/useScrollReveal'

useScrollReveal()
</script>

<style scoped lang="scss">
.dk-page {
  background: #0a0a0a;
  min-height: 100vh;
}

.contact-layout {
  display: grid;
  grid-template-columns: 5fr 7fr;
  align-items: stretch;
  min-height: 100vh;

  // Override JoinSection internal padding to fit the column
  :deep(.join-section) {
    padding: 80px 3rem;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid rgba(78, 205, 196, 0.08);
  }

  // Override ContactSection padding/structure for the column
  :deep(.contact-section) {
    padding: 80px 3rem;
  }
}

@media (max-width: 960px) {
  .contact-layout {
    grid-template-columns: 1fr;
    min-height: unset;

    :deep(.join-section) {
      padding: 80px 5vw;
      height: auto;
      border-right: none;
      border-bottom: 1px solid rgba(78, 205, 196, 0.08);
    }

    :deep(.contact-section) {
      padding: 80px 5vw;
    }
  }
}
</style>
