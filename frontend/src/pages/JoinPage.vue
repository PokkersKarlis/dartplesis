<template>
  <q-page class="join-page q-pa-md">
    <div class="join-container q-mx-auto">

      <!-- Header -->
      <div class="join-header q-mb-xl">
        <div class="join-kicker">{{ t('join.kicker') }}</div>
        <h1 class="join-title">{{ t('join.title') }}</h1>
        <p class="join-sub">{{ t('join.subtitle') }}</p>
      </div>

      <!-- Success state -->
      <div v-if="submitted" class="success-card q-pa-xl text-center">
        <q-icon name="check_circle" size="56px" color="positive" />
        <div class="text-h5 text-weight-bold q-mt-md">{{ t('join.success_title') }}</div>
        <p class="text-grey-5 q-mt-sm">{{ t('join.success_body') }}</p>
        <q-btn outline color="primary" :label="t('join.success_back')" to="/" class="q-mt-lg" />
      </div>

      <!-- Form -->
      <q-form v-else ref="formRef" class="join-form" @submit.prevent="onSubmit">

        <!-- Honeypot — invisible to humans, bots fill it in -->
        <input
          v-model="honeypot"
          type="text"
          name="website"
          tabindex="-1"
          autocomplete="off"
          aria-hidden="true"
          class="hp-field"
        />

        <!-- 1. Personal Info -->
        <div class="form-card">
          <div class="form-card-title">
            <q-icon name="badge" size="18px" color="primary" class="q-mr-sm" />
            {{ t('join.section_identity') }}
          </div>
          <div class="form-grid">
            <q-input
              v-model="form.name" outlined dark dense
              :label="t('join.field_name') + ' *'"
              :rules="[v => !!v?.trim() || t('join.err_required')]"
              lazy-rules
            />
            <q-input
              v-model="form.surname" outlined dark dense
              :label="t('join.field_surname') + ' *'"
              :rules="[v => !!v?.trim() || t('join.err_required')]"
              lazy-rules
            />
            <q-input
              v-model="form.email" outlined dark dense type="email"
              :label="t('join.field_email') + ' *'"
              :rules="[
                v => !!v?.trim() || t('join.err_required'),
                v => /.+@.+\..+/.test(v)  || t('join.err_email'),
              ]"
              lazy-rules
            />
            <q-input
              v-model="form.phone" outlined dark dense type="tel"
              :label="t('join.field_phone') + ' *'"
              :rules="[v => !!v?.trim() || t('join.err_required')]"
              lazy-rules
            />
            <q-input
              v-model="form.nickname" outlined dark dense
              :label="t('join.field_nickname')"
              :placeholder="t('join.nickname_ph')"
            />
            <q-input
              v-model="form.dateOfBirth" outlined dark dense type="date"
              :label="t('join.field_dob')"
            />
            <div class="form-field-stack">
              <div class="form-label">{{ t('join.field_category') }}</div>
              <q-btn-toggle
                v-model="form.isJunior"
                :options="[{label: t('join.senior'), value: false}, {label: t('join.junior'), value: true}]"
                toggle-color="primary" color="grey-9" text-color="grey-4"
                unelevated rounded
              />
            </div>
          </div>
        </div>

        <!-- 2. Photo -->
        <div class="form-card">
          <div class="form-card-title">
            <q-icon name="photo_camera" size="18px" color="primary" class="q-mr-sm" />
            {{ t('join.section_photo') }}
          </div>
          <div class="row items-center q-gutter-md">
            <q-avatar size="80px" class="photo-avatar">
              <img v-if="photoPreview" :src="photoPreview" />
              <q-icon v-else name="person" color="grey-6" size="44px" />
            </q-avatar>
            <div>
              <q-btn outline color="grey-5" icon="upload" :label="t('join.photo_upload')" size="sm" @click="fileInputRef?.click()" />
              <q-btn v-if="photoPreview" flat color="negative" icon="delete" :label="t('join.photo_remove')" size="sm" class="q-ml-sm" @click="removePhoto" />
              <div class="text-caption text-grey-6 q-mt-xs">{{ t('join.photo_hint') }}</div>
              <input ref="fileInputRef" type="file" accept="image/*" style="display:none" @change="onFileChange" />
            </div>
          </div>
        </div>

        <!-- 3. Darts Setup -->
        <div class="form-card">
          <div class="form-card-title">
            <q-icon name="sports_bar" size="18px" color="primary" class="q-mr-sm" />
            {{ t('join.section_darts') }}
          </div>
          <div class="form-grid">
            <q-input v-model="form.dartWeight"      outlined dark dense :label="t('join.field_dart_weight')"  :placeholder="t('join.dart_weight_ph')" />
            <q-input v-model="form.dartModel"       outlined dark dense :label="t('join.field_dart_model')"   :placeholder="t('join.dart_model_ph')"  />
            <q-select v-model="form.gripStyle"      outlined dark dense :label="t('join.field_grip')"         :options="GRIP_STYLES" clearable emit-value map-options />
            <q-select
              v-model="form.favoriteDouble" outlined dark dense
              :label="t('join.field_fav_double')"
              :options="DOUBLES" clearable emit-value map-options
              use-input input-debounce="0" @filter="filterDoubles"
            >
              <template #no-option><q-item><q-item-section class="text-grey-6">—</q-item-section></q-item></template>
            </q-select>
            <q-input v-model="form.walkOutSong" outlined dark dense :label="t('join.field_walkout')" :placeholder="t('join.walkout_ph')" class="form-wide" />
            <q-input
              v-model.number="form.highestCheckout" outlined dark dense type="number"
              :label="t('join.field_checkout')" :hint="t('join.checkout_hint')"
              :rules="[v => !v || (v >= 2 && v <= 170) || t('join.err_checkout')]"
              lazy-rules
            />
          </div>
        </div>

        <!-- 4. About you -->
        <div class="form-card">
          <div class="form-card-title">
            <q-icon name="auto_awesome" size="18px" color="primary" class="q-mr-sm" />
            {{ t('join.section_about') }}
          </div>
          <div class="form-grid">
            <q-input v-model="form.dartsIdol"      outlined dark dense :label="t('join.field_idol')"      :placeholder="t('join.idol_ph')" />
            <q-input v-model="form.hobbies"         outlined dark dense :label="t('join.field_hobbies')"   :placeholder="t('join.hobbies_ph')" />
            <q-input v-model="form.preGameRitual"   outlined dark dense :label="t('join.field_ritual')"    type="textarea" autogrow class="form-wide" />
            <q-input v-model="form.careerHighlight" outlined dark dense :label="t('join.field_highlight')" type="textarea" autogrow class="form-wide" />
            <q-input v-model="form.lifeMotto"       outlined dark dense :label="t('join.field_motto')"     type="textarea" autogrow class="form-wide" />
          </div>
        </div>

        <!-- 5. Verification -->
        <div class="form-card captcha-card">
          <div class="form-card-title">
            <q-icon name="security" size="18px" color="primary" class="q-mr-sm" />
            {{ t('join.section_captcha') }}
          </div>
          <p class="captcha-hint">{{ t('join.captcha_hint') }}</p>
          <HCaptcha ref="captchaRef" @verified="onCaptchaVerified" @expired="captchaToken = ''" @error="captchaToken = ''" />
          <transition name="fade">
            <div v-if="captchaError" class="captcha-err">
              <q-icon name="warning" size="16px" class="q-mr-xs" />
              {{ t('join.err_captcha') }}{{ captchaError }}
            </div>
          </transition>
          <div v-if="captchaToken" class="captcha-ok">
            <q-icon name="verified" size="16px" color="positive" class="q-mr-xs" />
            {{ t('join.captcha_ok') }}
          </div>
        </div>

        <!-- Error banner -->
        <q-banner v-if="submitError" class="err-banner" rounded dense>
          <template #avatar><q-icon name="error_outline" color="negative" /></template>
          {{ submitError }}
        </q-banner>

        <!-- Submit -->
        <div class="row justify-end q-mt-md">
          <q-btn
            type="submit"
            color="primary"
            unelevated
            size="lg"
            :loading="submitting"
            :label="t('join.submit')"
            icon-right="send"
            class="submit-btn"
          />
        </div>

      </q-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import HCaptcha from 'src/components/HCaptcha.vue'

const { t } = useI18n()

// ── Constants ─────────────────────────────────────────────────────────────────
const GRIP_STYLES = ['Front', 'Middle', 'Rear']
const ALL_DOUBLES = ['D1','D2','D3','D4','D5','D6','D7','D8','D9','D10',
                     'D11','D12','D13','D14','D15','D16','D17','D18','D19','D20','Bull']
const DOUBLES = ref([...ALL_DOUBLES])

function filterDoubles(val, update) {
  update(() => {
    const q = val.toLowerCase()
    DOUBLES.value = ALL_DOUBLES.filter(d => d.toLowerCase().includes(q))
  })
}

// ── Form state ────────────────────────────────────────────────────────────────
const formRef      = ref(null)
const captchaRef   = ref(null)
const captchaToken = ref('')
const captchaError = ref(false)
const submitError  = ref('')
const submitting   = ref(false)
const submitted    = ref(false)
const honeypot     = ref('')          // Trap field — must stay empty

const fileInputRef = ref(null)
const photoPreview = ref(null)
const photoFile    = ref(null)

// Record page-load time for minimum fill-time check (5 seconds)
const loadedAt = Date.now()

const form = ref({
  name: '', surname: '', email: '', phone: '',
  nickname: '', dateOfBirth: '', isJunior: false,
  dartWeight: '', dartModel: '', gripStyle: null,
  favoriteDouble: null, walkOutSong: '', highestCheckout: null,
  dartsIdol: '', hobbies: '', preGameRitual: '',
  careerHighlight: '', lifeMotto: '',
})

function onCaptchaVerified(token) {
  captchaToken.value = token
  captchaError.value = false
}

function onFileChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) return
  photoFile.value = file
  const reader = new FileReader()
  reader.onload = ev => { photoPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function removePhoto() {
  photoPreview.value = null
  photoFile.value = null
  if (fileInputRef.value) fileInputRef.value.value = ''
}

async function onSubmit() {
  submitError.value  = ''
  captchaError.value = false

  // Field validation
  const valid = await formRef.value?.validate()
  if (!valid) return

  // Captcha always required — no dev bypass
  if (!captchaToken.value) {
    captchaError.value = true
    return
  }

  submitting.value = true
  try {
    const fd = new FormData()

    // Core fields
    const payload = {
      ...form.value,
      captchaToken: captchaToken.value,
      website: honeypot.value,   // honeypot — backend checks this
      _t: String(loadedAt),      // form load timestamp for timing check
    }

    for (const [k, v] of Object.entries(payload)) {
      if (v !== null && v !== undefined && v !== '') {
        fd.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : String(v))
      }
    }

    if (photoFile.value) fd.append('photo', photoFile.value)

    const res  = await fetch('/api/club-requests', { method: 'POST', body: fd })
    const data = await res.json().catch(() => ({}))

    if (res.status === 429) {
      throw new Error(t('join.err_ratelimit'))
    }
    if (!res.ok) {
      throw new Error(data.message ?? t('join.err_generic'))
    }

    submitted.value = true
  } catch (e) {
    submitError.value = e.message
    captchaRef.value?.reset()
    captchaToken.value = ''
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped lang="scss">
.join-page {
  min-height: 100%;
  background:
    radial-gradient(1000px 700px at 10% 5%, rgba(78,205,196,0.07), transparent 55%),
    radial-gradient(800px 600px at 90% 80%, rgba(231,76,60,0.06), transparent 50%),
    #0a0a0a;
}

.join-container { max-width: 780px; }

.join-header { text-align: center; padding: 3rem 0 0; }
.join-kicker  { font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: #4ecdc4; font-weight: 700; }
.join-title   { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; letter-spacing: -0.04em; line-height: 1; color: #f5f5f5; margin: 8px 0 0; }
.join-sub     { color: rgba(255,255,255,0.5); max-width: 520px; margin: 12px auto 0; font-size: 0.95rem; line-height: 1.6; }

// Honeypot — must be invisible but not display:none (some bots check that)
.hp-field {
  position: absolute;
  left: -9999px;
  opacity: 0;
  height: 0;
  padding: 0;
  border: 0;
  overflow: hidden;
  pointer-events: none;
}

.join-form { display: flex; flex-direction: column; gap: 16px; }

.form-card {
  border-radius: 18px;
  border: 1px solid rgba(255,255,255,0.07);
  background: rgba(17,17,17,0.7);
  backdrop-filter: blur(10px);
  padding: 20px 24px;
}

.form-card-title {
  display: flex; align-items: center;
  font-size: 0.78rem; letter-spacing: 0.14em; text-transform: uppercase;
  font-weight: 700; color: rgba(255,255,255,0.6);
  margin-bottom: 16px;
}

.form-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  @media(max-width:600px) { grid-template-columns: 1fr; }
}
.form-wide { grid-column: span 2; @media(max-width:600px) { grid-column: span 1; } }

.form-field-stack { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 0.75rem; color: rgba(255,255,255,0.45); }

.photo-avatar {
  border-radius: 12px; border: 2px solid rgba(255,255,255,0.1);
  background: rgba(255,255,255,0.05); overflow: hidden;
  img { object-fit: cover; width: 100%; height: 100%; }
}

.captcha-card { display: flex; flex-direction: column; align-items: flex-start; gap: 12px; }

.captcha-hint {
  font-size: 0.83rem; color: rgba(255,255,255,0.45);
  margin: 0; line-height: 1.5;
}

.captcha-err {
  display: flex; align-items: center;
  font-size: 0.82rem; color: #e74c3c;
}

.captcha-ok {
  display: flex; align-items: center;
  font-size: 0.82rem; color: #4ecdc4;
}

.err-banner {
  background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3);
  color: rgba(255,255,255,0.88); font-size: 0.85rem;
}

.submit-btn { min-width: 200px; }

.success-card {
  border-radius: 18px; border: 1px solid rgba(78,205,196,0.2);
  background: rgba(78,205,196,0.04);
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
