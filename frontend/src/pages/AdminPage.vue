<template>
  <q-page class="admin-page q-pa-md">
    <div class="admin-inner">

      <!-- Header -->
      <div class="row items-center justify-between q-mb-xl">
        <div>
          <div class="admin-kicker">{{ t('admin.kicker') }}</div>
          <h1 class="admin-title">{{ t('admin.title') }}</h1>
          <p v-if="state.user" class="admin-sub q-mt-xs">{{ state.user.email }}</p>
        </div>
        <q-btn outline color="grey-5" no-caps :label="t('auth.logout')" @click="onLogout" />
      </div>

      <!-- ── Supporters section ──────────────────────────────────────────────── -->
      <div class="section-heading row items-center justify-between q-mb-md">
        <div class="section-title">{{ t('admin.supporters.section') }}</div>
        <q-btn
          unelevated color="primary" no-caps size="sm"
          :label="t('admin.supporters.add')"
          icon="add"
          @click="openForm(null)"
        />
      </div>

      <q-banner v-if="supError" class="admin-banner-err q-mb-md" rounded dense>{{ supError }}</q-banner>

      <!-- Loading -->
      <div v-if="supLoading" class="row items-center q-gutter-sm text-grey-5 q-mb-lg">
        <q-spinner color="primary" size="1.1rem" />
        <span>{{ t('admin.loading') }}</span>
      </div>

      <!-- Empty -->
      <div v-else-if="supporters.length === 0" class="admin-empty q-mb-lg">
        {{ t('admin.supporters.empty') }}
      </div>

      <!-- Supporters table -->
      <div v-else class="sup-table q-mb-xl">
        <div
          v-for="s in supporters"
          :key="s.id"
          class="sup-row"
        >
          <!-- Logo -->
          <div class="sup-logo-cell">
            <div v-if="s.logo" class="sup-logo-wrap">
              <img :src="logoUrl(s.logo)" :alt="s.name" class="sup-logo-img" />
            </div>
            <div v-else class="sup-logo-placeholder">
              <q-icon name="image" size="20px" color="grey-7" />
            </div>
          </div>

          <!-- Info -->
          <div class="sup-info">
            <div class="sup-name">{{ s.name }}</div>
            <a v-if="s.url" :href="s.url" target="_blank" rel="noopener" class="sup-url">{{ s.url }}</a>
            <div class="sup-order text-grey-6">#{{ s.sortOrder }}</div>
          </div>

          <!-- Logo upload -->
          <label class="sup-upload-btn" :title="t('admin.supporters.logo')">
            <q-icon name="upload" size="16px" />
            <input type="file" accept="image/*" style="display:none" @change="(e) => uploadLogo(s.id, e)" />
          </label>

          <!-- Edit -->
          <q-btn flat round icon="edit" size="sm" color="grey-5" @click="openForm(s)" />

          <!-- Delete -->
          <q-btn flat round icon="delete" size="sm" color="negative" @click="confirmDelete(s)" />
        </div>
      </div>

      <!-- ── Form dialog ─────────────────────────────────────────────────────── -->
      <q-dialog v-model="formOpen" persistent>
        <q-card class="admin-dialog">
          <q-card-section>
            <div class="form-title">
              {{ formData.id ? t('admin.supporters.edit') : t('admin.supporters.add') }}
            </div>
          </q-card-section>

          <q-card-section class="q-pt-none q-gutter-md">
            <q-banner v-if="formError" class="admin-banner-err q-mb-xs" rounded dense>{{ formError }}</q-banner>

            <q-input
              v-model="formData.name"
              outlined dense
              :label="t('admin.supporters.name') + ' *'"
              :placeholder="t('admin.supporters.name_ph')"
              :error="!!formNameError"
              :error-message="formNameError"
            />

            <q-input
              v-model="formData.url"
              outlined dense
              :label="t('admin.supporters.url')"
              :placeholder="t('admin.supporters.url_ph')"
              type="url"
            />

            <q-input
              v-model.number="formData.sortOrder"
              outlined dense
              :label="t('admin.supporters.sort')"
              type="number"
              min="0"
            />
          </q-card-section>

          <q-card-actions align="right" class="q-pa-md q-pt-none">
            <q-btn flat no-caps :label="t('admin.supporters.cancel')" @click="formOpen = false" />
            <q-btn
              unelevated color="primary" no-caps
              :label="t('admin.supporters.save')"
              :loading="formSaving"
              @click="saveForm"
            />
          </q-card-actions>
        </q-card>
      </q-dialog>

      <!-- ── Delete confirm dialog ──────────────────────────────────────────── -->
      <q-dialog v-model="deleteOpen" persistent>
        <q-card class="admin-dialog">
          <q-card-section>
            <div class="form-title">{{ t('admin.supporters.delete_confirm') }}</div>
            <div v-if="deleteTarget" class="text-grey-5 q-mt-xs">{{ deleteTarget.name }}</div>
          </q-card-section>
          <q-banner v-if="deleteError" class="admin-banner-err q-mx-md q-mb-sm" rounded dense>{{ deleteError }}</q-banner>
          <q-card-actions align="right" class="q-pa-md q-pt-none">
            <q-btn flat no-caps :label="t('admin.supporters.cancel')" @click="deleteOpen = false" />
            <q-btn
              unelevated color="negative" no-caps
              :label="t('admin.supporters.delete')"
              :loading="deleteLoading"
              @click="doDelete"
            />
          </q-card-actions>
        </q-card>
      </q-dialog>

    </div>
  </q-page>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from 'src/composables/useAuth'

const { t } = useI18n()
const router = useRouter()
const { state, logout } = useAuth()

// ── Supporters list ───────────────────────────────────────────────────────────

const supporters = ref([])
const supLoading = ref(true)
const supError   = ref('')

async function loadSupporters() {
  supLoading.value = true
  supError.value   = ''
  try {
    const res  = await fetch('/api/admin/supporters', { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) { supError.value = data.message ?? t('admin.load_error'); return }
    supporters.value = data.supporters ?? []
  } catch {
    supError.value = t('admin.load_error')
  } finally {
    supLoading.value = false
  }
}

// ── Form ──────────────────────────────────────────────────────────────────────

const formOpen    = ref(false)
const formSaving  = ref(false)
const formError   = ref('')
const formNameError = ref('')
const formData    = ref({ id: null, name: '', url: '', sortOrder: 0 })

function openForm(s) {
  formError.value     = ''
  formNameError.value = ''
  formData.value = s
    ? { id: s.id, name: s.name, url: s.url ?? '', sortOrder: s.sortOrder }
    : { id: null, name: '', url: '', sortOrder: supporters.value.length }
  formOpen.value = true
}

async function saveForm() {
  formNameError.value = ''
  formError.value     = ''
  if (!formData.value.name.trim()) {
    formNameError.value = t('admin.supporters.err_required')
    return
  }

  formSaving.value = true
  try {
    const isEdit = !!formData.value.id
    const url    = isEdit ? `/api/admin/supporters/${formData.value.id}` : '/api/admin/supporters'
    const res    = await fetch(url, {
      method: isEdit ? 'PUT' : 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        name:      formData.value.name.trim(),
        url:       formData.value.url.trim() || null,
        sortOrder: formData.value.sortOrder ?? 0,
      }),
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) { formError.value = data.message ?? t('admin.supporters.err_save'); return }
    formOpen.value = false
    await loadSupporters()
  } catch {
    formError.value = t('admin.supporters.err_save')
  } finally {
    formSaving.value = false
  }
}

// ── Delete ────────────────────────────────────────────────────────────────────

const deleteOpen    = ref(false)
const deleteLoading = ref(false)
const deleteError   = ref('')
const deleteTarget  = ref(null)

function confirmDelete(s) {
  deleteTarget.value = s
  deleteError.value  = ''
  deleteOpen.value   = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleteLoading.value = true
  deleteError.value   = ''
  try {
    const res = await fetch(`/api/admin/supporters/${deleteTarget.value.id}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
    if (!res.ok && res.status !== 204) {
      const data = await res.json().catch(() => ({}))
      deleteError.value = data.message ?? t('admin.supporters.err_delete')
      return
    }
    deleteOpen.value = false
    deleteTarget.value = null
    await loadSupporters()
  } catch {
    deleteError.value = t('admin.supporters.err_delete')
  } finally {
    deleteLoading.value = false
  }
}

// ── Logo upload ───────────────────────────────────────────────────────────────

const uploadingId = ref(null)

async function uploadLogo(id, event) {
  const file = event.target.files?.[0]
  if (!file) return
  uploadingId.value = id
  const form = new FormData()
  form.append('logo', file)
  try {
    const res  = await fetch(`/api/admin/supporters/${id}/logo`, {
      method: 'POST',
      credentials: 'include',
      body: form,
    })
    if (!res.ok) { supError.value = t('admin.supporters.err_upload'); return }
    await loadSupporters()
  } catch {
    supError.value = t('admin.supporters.err_upload')
  } finally {
    uploadingId.value = null
    event.target.value = ''
  }
}

function logoUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return path.startsWith('/') ? path : `/${path}`
}

// ── Logout ────────────────────────────────────────────────────────────────────

async function onLogout() {
  await logout()
  await router.replace('/login')
}

onMounted(() => {
  loadSupporters()
})
</script>

<style scoped lang="scss">
.admin-page {
  min-height: 100vh;
  background: #0a0a0a;
  color: #f5f5f5;
}

.admin-inner {
  max-width: 860px;
  margin: 0 auto;
}

.admin-kicker {
  font-size: 11px;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.45);
}

.admin-title {
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  margin: 0;
}

.admin-sub {
  color: rgba(255, 255, 255, 0.45);
  margin: 0;
  font-size: 0.85rem;
}

.section-heading {
  border-bottom: 1px solid rgba(255,255,255,0.07);
  padding-bottom: 10px;
}

.section-title {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.45);
}

.admin-empty {
  font-size: 0.9rem;
  color: rgba(255,255,255,0.35);
  padding: 1rem 0;
}

// ── Supporters table ──────────────────────────────────────────────────────────

.sup-table {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sup-row {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(17,17,17,0.7);
  border: 1px solid rgba(255,255,255,0.06);
  padding: 10px 14px;
  border-radius: 4px;
  transition: border-color 0.2s;

  &:hover { border-color: rgba(78, 205, 196, 0.22); }
}

.sup-logo-cell {
  flex-shrink: 0;
  width: 52px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 3px;
  overflow: hidden;
}

.sup-logo-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.sup-logo-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.sup-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sup-name {
  font-size: 0.92rem;
  font-weight: 600;
  color: #f0f0f0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sup-url {
  font-size: 0.75rem;
  color: #4ecdc4;
  text-decoration: none;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;

  &:hover { text-decoration: underline; }
}

.sup-order {
  font-size: 0.72rem;
}

.sup-upload-btn {
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 3px;
  color: rgba(255,255,255,0.45);
  transition: border-color 0.2s, color 0.2s;
  flex-shrink: 0;

  &:hover {
    border-color: #4ecdc4;
    color: #4ecdc4;
  }
}

// ── Dialogs ───────────────────────────────────────────────────────────────────

.admin-dialog {
  width: 100%;
  max-width: 420px;
  background: #141414;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.08);
}

.form-title {
  font-size: 1rem;
  font-weight: 700;
  color: #f5f5f5;
}

.admin-banner-err {
  background: rgba(231, 76, 60, 0.12);
  border: 1px solid rgba(231, 76, 60, 0.35);
  color: rgba(255, 255, 255, 0.9);
}
</style>
