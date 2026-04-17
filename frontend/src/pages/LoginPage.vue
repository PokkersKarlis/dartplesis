<template>
  <q-page class="auth-page flex flex-center q-pa-md">
    <q-card class="auth-card" flat bordered>
      <q-card-section>
        <div class="auth-kicker">{{ t('auth.admin_login_kicker') }}</div>
        <div class="auth-title">{{ t('auth.sign_in') }}</div>
        <p class="auth-sub q-mt-sm">{{ t('auth.admin_login_hint') }}</p>
      </q-card-section>

      <q-separator dark />

      <q-card-section>
        <q-form class="q-gutter-md" @submit.prevent="onSubmit">
          <q-input
            v-model="email"
            outlined
            dense
            type="email"
            :label="t('auth.email')"
            :rules="[(v) => !!v || t('contact.form.err_required')]"
            lazy-rules
            autocomplete="username"
          />
          <q-input
            v-model="password"
            outlined
            dense
            type="password"
            :label="t('auth.password')"
            :rules="[(v) => !!v || t('contact.form.err_required')]"
            lazy-rules
            autocomplete="current-password"
          />

          <q-banner v-if="errorMsg" class="auth-err" rounded dense>
            {{ errorMsg }}
          </q-banner>

          <q-btn
            type="submit"
            color="primary"
            class="full-width"
            unelevated
            :loading="loading"
            :label="t('auth.submit')"
          />
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from 'src/composables/useAuth'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMsg = ref('')

async function onSubmit() {
  errorMsg.value = ''
  loading.value = true
  try {
    await login(email.value, password.value)
    const r = route.query.redirect
    const target = typeof r === 'string' && r.startsWith('/') ? r : '/admin'
    await router.replace(target)
  } catch (e) {
    errorMsg.value = e instanceof Error ? e.message : t('auth.login_failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
.auth-page {
  min-height: 100vh;
  background: #0a0a0a;
}

.auth-card {
  width: 100%;
  max-width: 420px;
  border-radius: 18px;
  border-color: rgba(255, 255, 255, 0.08);
  background: rgba(17, 17, 17, 0.92);
  backdrop-filter: blur(10px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}

.auth-kicker {
  font-size: 11px;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}

.auth-title {
  font-size: 1.5rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  color: #f5f5f5;
}

.auth-sub {
  color: rgba(255, 255, 255, 0.65);
  line-height: 1.5;
  margin: 0;
  font-size: 0.9rem;
}

.auth-err {
  background: rgba(231, 76, 60, 0.12);
  border: 1px solid rgba(231, 76, 60, 0.35);
  color: rgba(255, 255, 255, 0.9);
}
</style>
