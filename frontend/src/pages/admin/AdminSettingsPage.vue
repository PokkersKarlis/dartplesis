<template>
  <q-page class="admin-page q-pa-lg">

    <div class="page-header q-mb-xl">
      <div class="page-kicker">{{ t('admin_settings.kicker') }}</div>
      <div class="page-title">{{ t('admin_settings.title') }}</div>
    </div>

    <div class="settings-grid">

      <!-- Account card -->
      <q-card flat class="settings-card">
        <q-card-section>
          <div class="card-title">
            <q-icon name="manage_accounts" size="18px" color="primary" class="q-mr-xs" />
            {{ t('admin_settings.sec_account') }}
          </div>
          <q-separator dark style="opacity: 0.1; margin: 12px 0" />
          <div class="info-row">
            <span class="info-key">{{ t('admin_settings.lbl_email') }}</span>
            <span class="info-val">{{ state.user?.email }}</span>
          </div>
          <div class="info-row">
            <span class="info-key">{{ t('admin_settings.lbl_role') }}</span>
            <span class="info-val">
              <q-badge color="primary" rounded>ROLE_ADMINISTRATOR</q-badge>
            </span>
          </div>
        </q-card-section>
      </q-card>

      <!-- Change password card -->
      <q-card flat class="settings-card">
        <q-card-section>
          <div class="card-title">
            <q-icon name="lock" size="18px" color="primary" class="q-mr-xs" />
            {{ t('admin_settings.sec_password') }}
          </div>
          <q-separator dark style="opacity: 0.1; margin: 12px 0" />

          <q-form class="q-gutter-md" @submit.prevent="changePassword">
            <q-input
              v-model="pwd.current"
              outlined dark dense :label="t('admin_settings.lbl_current_pw')"
              :type="showCurrent ? 'text' : 'password'"
              :rules="[v => !!v || t('admin_settings.pw_required')]"
              lazy-rules
            >
              <template #append>
                <q-icon :name="showCurrent ? 'visibility_off' : 'visibility'" class="cursor-pointer" color="grey-6" @click="showCurrent = !showCurrent" />
              </template>
            </q-input>
            <q-input
              v-model="pwd.next"
              outlined dark dense :label="t('admin_settings.lbl_new_pw')"
              :type="showNext ? 'text' : 'password'"
              :rules="[v => v?.length >= 8 || t('admin_settings.pw_min')]"
              lazy-rules
            >
              <template #append>
                <q-icon :name="showNext ? 'visibility_off' : 'visibility'" class="cursor-pointer" color="grey-6" @click="showNext = !showNext" />
              </template>
            </q-input>
            <q-input
              v-model="pwd.confirm"
              outlined dark dense :label="t('admin_settings.lbl_confirm_pw')"
              :type="showConfirm ? 'text' : 'password'"
              :rules="[v => v === pwd.next || t('admin_settings.pw_match')]"
              lazy-rules
            >
              <template #append>
                <q-icon :name="showConfirm ? 'visibility_off' : 'visibility'" class="cursor-pointer" color="grey-6" @click="showConfirm = !showConfirm" />
              </template>
            </q-input>

            <q-banner v-if="pwdError" class="err-banner" dense rounded>
              <template #avatar><q-icon name="error_outline" color="negative" /></template>
              {{ pwdError }}
            </q-banner>

            <q-btn type="submit" color="primary" unelevated :loading="pwdLoading" :label="t('admin_settings.pw_submit')" />
          </q-form>
        </q-card-section>
      </q-card>

      <!-- System info card -->
      <q-card flat class="settings-card">
        <q-card-section>
          <div class="card-title">
            <q-icon name="info" size="18px" color="primary" class="q-mr-xs" />
            {{ t('admin_settings.sec_system') }}
          </div>
          <q-separator dark style="opacity: 0.1; margin: 12px 0" />
          <div class="info-row">
            <span class="info-key">{{ t('admin_settings.lbl_backend') }}</span>
            <span class="info-val">Symfony 7 / PHP</span>
          </div>
          <div class="info-row">
            <span class="info-key">{{ t('admin_settings.lbl_frontend') }}</span>
            <span class="info-val">Quasar 2 / Vue 3</span>
          </div>
          <div class="info-row">
            <span class="info-key">{{ t('admin_settings.lbl_auth') }}</span>
            <span class="info-val">Session-based (json_login)</span>
          </div>
          <div class="info-row q-mt-md">
            <span class="info-key">{{ t('admin_settings.lbl_admin_url') }}</span>
            <code class="info-code">/#/admin/dashboard</code>
          </div>
        </q-card-section>
      </q-card>

      <!-- Session card -->
      <q-card flat class="settings-card danger-card">
        <q-card-section>
          <div class="card-title text-negative">
            <q-icon name="logout" size="18px" color="negative" class="q-mr-xs" />
            {{ t('admin_settings.sec_session') }}
          </div>
          <q-separator dark style="opacity: 0.1; margin: 12px 0" />
          <p class="info-val q-mb-md">{{ t('admin_settings.session_desc') }}</p>
          <q-btn outline color="negative" icon="logout" :label="t('admin_settings.sign_out')" @click="onLogout" />
        </q-card-section>
      </q-card>

    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useQuasar } from 'quasar'
import { useAuth } from 'src/composables/useAuth'

const { t } = useI18n()

const $q = useQuasar()
const router = useRouter()
const { state, logout } = useAuth()

// ── Password change ────────────────────────────────────────────────────────────
const pwd        = ref({ current: '', next: '', confirm: '' })
const pwdLoading = ref(false)
const pwdError   = ref('')
const showCurrent = ref(false)
const showNext    = ref(false)
const showConfirm = ref(false)

async function changePassword() {
  pwdError.value  = ''
  pwdLoading.value = true
  try {
    const res = await fetch('/api/admin/change-password', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ currentPassword: pwd.value.current, newPassword: pwd.value.next }),
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) throw new Error(data.message ?? 'Password change failed.')
    $q.notify({ type: 'positive', message: 'Password updated successfully.' })
    pwd.value = { current: '', next: '', confirm: '' }
  } catch (e) {
    pwdError.value = e.message
  } finally {
    pwdLoading.value = false
  }
}

async function onLogout() {
  await logout()
  await router.replace('/admin/login')
}
</script>

<style scoped lang="scss">
.admin-page {
  min-height: 100%;
  background: #111;
  color: #f5f5f5;
}

.page-kicker {
  font-size: 11px;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
}
.page-title {
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
  max-width: 900px;
}

.settings-card {
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.07);
  background: rgba(255, 255, 255, 0.03);
}

.danger-card {
  border-color: rgba(231, 76, 60, 0.2);
  background: rgba(231, 76, 60, 0.04);
}

.card-title {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
  font-weight: 700;
  color: #f5f5f5;
}

.info-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 6px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);

  &:last-child { border-bottom: none; }
}

.info-key {
  font-size: 0.78rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.38);
}

.info-val {
  font-size: 0.88rem;
  color: rgba(255, 255, 255, 0.75);
}

.info-code {
  font-family: ui-monospace, monospace;
  font-size: 0.78rem;
  background: rgba(255, 255, 255, 0.06);
  padding: 2px 8px;
  border-radius: 6px;
  color: #4ecdc4;
}

.err-banner {
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.3);
  color: rgba(255, 255, 255, 0.88);
  font-size: 0.85rem;
}
</style>
