<template>
  <div class="login-wrap flex flex-center">
    <q-card class="login-card" flat>

      <q-card-section class="q-pb-sm">
        <div class="row items-center q-gutter-sm q-mb-md">
          <q-icon name="sports_bar" size="32px" color="primary" />
          <div>
            <div class="brand-name">Dārtplēsis</div>
            <div class="brand-sub">Admin Panel</div>
          </div>
        </div>
        <div class="login-title">Sign in</div>
        <p class="login-hint q-mt-xs">
          Not linked from the public site. Administrator credentials required.
        </p>
      </q-card-section>

      <q-separator style="opacity: 0.1" />

      <q-card-section>
        <q-form class="q-gutter-md" @submit.prevent="onSubmit">
          <q-input
            v-model="email"
            outlined
            dense
            dark
            type="email"
            label="Email"
            :rules="[v => !!v || 'Required']"
            lazy-rules
            autocomplete="username"
          />
          <q-input
            v-model="password"
            outlined
            dense
            dark
            :type="showPwd ? 'text' : 'password'"
            label="Password"
            :rules="[v => !!v || 'Required']"
            lazy-rules
            autocomplete="current-password"
          >
            <template #append>
              <q-icon
                :name="showPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                color="grey-6"
                @click="showPwd = !showPwd"
              />
            </template>
          </q-input>

          <q-banner v-if="errorMsg" class="err-banner" rounded dense>
            <template #avatar><q-icon name="error_outline" color="negative" /></template>
            {{ errorMsg }}
          </q-banner>

          <q-btn
            type="submit"
            color="primary"
            class="full-width"
            unelevated
            :loading="loading"
            label="Sign in"
            size="md"
          />
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from 'src/composables/useAuth'

const route = useRoute()
const router = useRouter()
const { login } = useAuth()

const email    = ref('')
const password = ref('')
const showPwd  = ref(false)
const loading  = ref(false)
const errorMsg = ref('')

async function onSubmit() {
  errorMsg.value = ''
  loading.value  = true
  try {
    await login(email.value, password.value)
    const red = route.query.redirect
    await router.replace(typeof red === 'string' && red.startsWith('/admin') ? red : '/admin/dashboard')
  } catch (e) {
    errorMsg.value = e instanceof Error ? e.message : 'Sign in failed. Check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
.login-wrap {
  min-height: 100vh;
  background:
    radial-gradient(800px 600px at 20% 20%, rgba(78, 205, 196, 0.06), transparent 60%),
    radial-gradient(600px 500px at 80% 80%, rgba(231, 76, 60, 0.06), transparent 55%),
    #0a0a0a;
}

.login-card {
  width: 100%;
  max-width: 400px;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.09);
  background: rgba(17, 17, 17, 0.95);
  backdrop-filter: blur(16px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
}

.brand-name {
  font-size: 1rem;
  font-weight: 900;
  letter-spacing: -0.02em;
  color: #f5f5f5;
  line-height: 1.2;
}
.brand-sub {
  font-size: 0.65rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
}

.login-title {
  font-size: 1.5rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #f5f5f5;
}

.login-hint {
  color: rgba(255, 255, 255, 0.45);
  font-size: 0.83rem;
  line-height: 1.5;
  margin: 0;
}

.err-banner {
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.3);
  color: rgba(255, 255, 255, 0.88);
  font-size: 0.85rem;
}
</style>
