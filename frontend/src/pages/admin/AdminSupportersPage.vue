<template>
  <q-page class="admin-page q-pa-lg">

    <!-- Header -->
    <div class="row items-end justify-between q-mb-lg">
      <div>
        <div class="page-kicker">Management</div>
        <div class="page-title">Supporters & Sponsors</div>
      </div>
      <q-btn color="primary" unelevated icon="add" label="Add Supporter" @click="openForm(null)" />
    </div>

    <q-banner v-if="listError" class="err-banner q-mb-md" rounded>{{ listError }}</q-banner>

    <!-- Table -->
    <q-card flat class="table-card">
      <q-table
        :rows="supporters"
        :columns="columns"
        row-key="id"
        flat
        dark
        :loading="tableLoading"
        :pagination="{ rowsPerPage: 20 }"
        no-data-label="No supporters yet. Add the first one!"
        class="sup-table"
      >
        <!-- Logo cell -->
        <template #body-cell-logo="{ row }">
          <q-td>
            <div class="logo-cell">
              <img v-if="row.logo" :src="logoUrl(row.logo)" class="logo-thumb" :alt="row.name" />
              <q-icon v-else name="image_not_supported" color="grey-8" size="22px" />
            </div>
          </q-td>
        </template>

        <!-- Name cell -->
        <template #body-cell-name="{ row }">
          <q-td>
            <div class="text-weight-bold text-grey-2">{{ row.name }}</div>
            <a v-if="row.url" :href="row.url" target="_blank" rel="noopener" class="url-link">
              {{ row.url }}
            </a>
          </q-td>
        </template>

        <!-- Sort order cell -->
        <template #body-cell-sortOrder="{ row }">
          <q-td class="text-grey-5 text-center">{{ row.sortOrder }}</q-td>
        </template>

        <!-- Actions cell -->
        <template #body-cell-actions="{ row }">
          <q-td class="text-right">
            <div class="row justify-end q-gutter-xs no-wrap">
              <!-- Logo upload -->
              <label class="upload-icon-btn" title="Upload logo">
                <q-icon name="upload" size="16px" />
                <input type="file" accept="image/*" style="display:none" @change="(e) => uploadLogo(row.id, e)" />
              </label>
              <q-btn flat round dense icon="edit"   size="sm" color="grey-5"   @click="openForm(row)"  title="Edit" />
              <q-btn flat round dense icon="delete" size="sm" color="negative" @click="askDelete(row)" title="Delete" />
            </div>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- ── Add / Edit dialog ────────────────────────────────────────────────── -->
    <q-dialog v-model="formOpen" persistent>
      <q-card class="form-dialog" dark>
        <q-card-section>
          <div class="form-title">{{ formData.id ? 'Edit Supporter' : 'Add Supporter' }}</div>
        </q-card-section>

        <q-card-section class="q-pt-none q-gutter-sm">
          <q-banner v-if="formError" class="err-banner q-mb-xs" rounded dense>{{ formError }}</q-banner>

          <q-input
            v-model="formData.name"
            dark outlined dense
            label="Name *"
            placeholder="e.g. Acme Ltd."
            :error="!!nameErr"
            :error-message="nameErr"
          />

          <q-input
            v-model="formData.url"
            dark outlined dense
            label="Website URL"
            placeholder="https://example.com"
            type="url"
          />

          <q-input
            v-model.number="formData.sortOrder"
            dark outlined dense
            label="Display order"
            type="number"
            min="0"
          />
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md q-pt-none">
          <q-btn flat no-caps label="Cancel" @click="formOpen = false" />
          <q-btn unelevated color="primary" no-caps label="Save" :loading="formSaving" @click="saveForm" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- ── Delete confirm ───────────────────────────────────────────────────── -->
    <q-dialog v-model="deleteOpen" persistent>
      <q-card class="form-dialog" dark>
        <q-card-section>
          <div class="form-title">Delete supporter?</div>
          <div v-if="deleteTarget" class="text-grey-5 q-mt-xs">{{ deleteTarget.name }}</div>
        </q-card-section>
        <q-banner v-if="deleteError" class="err-banner q-mx-md q-mb-sm" rounded dense>{{ deleteError }}</q-banner>
        <q-card-actions align="right" class="q-pa-md q-pt-none">
          <q-btn flat no-caps label="Cancel" @click="deleteOpen = false" />
          <q-btn unelevated color="negative" no-caps label="Delete" :loading="deleteLoading" @click="doDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const columns = [
  { name: 'logo',      label: 'Logo',  field: 'logo',      align: 'left',   style: 'width:70px'  },
  { name: 'name',      label: 'Name',  field: 'name',      align: 'left',   sortable: true        },
  { name: 'sortOrder', label: 'Order', field: 'sortOrder', align: 'center', sortable: true, style: 'width:80px' },
  { name: 'actions',   label: '',      field: 'id',        align: 'right',  style: 'width:130px'  },
]

const supporters   = ref([])
const tableLoading = ref(true)
const listError    = ref('')

async function loadSupporters() {
  tableLoading.value = true
  listError.value    = ''
  try {
    const res  = await fetch('/api/admin/supporters', { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) { listError.value = data.message ?? 'Could not load supporters.'; return }
    supporters.value = data.supporters ?? []
  } catch {
    listError.value = 'Could not load supporters.'
  } finally {
    tableLoading.value = false
  }
}

function logoUrl(path) {
  if (!path) return ''
  return path.startsWith('http') ? path : (path.startsWith('/') ? path : `/${path}`)
}

// ── Form ──────────────────────────────────────────────────────────────────────

const formOpen    = ref(false)
const formSaving  = ref(false)
const formError   = ref('')
const nameErr     = ref('')
const formData    = ref({ id: null, name: '', url: '', sortOrder: 0 })

function openForm(s) {
  formError.value = ''
  nameErr.value   = ''
  formData.value  = s
    ? { id: s.id, name: s.name, url: s.url ?? '', sortOrder: s.sortOrder }
    : { id: null, name: '', url: '', sortOrder: supporters.value.length }
  formOpen.value  = true
}

async function saveForm() {
  nameErr.value   = ''
  formError.value = ''
  if (!formData.value.name.trim()) { nameErr.value = 'Name is required.'; return }

  formSaving.value = true
  try {
    const isEdit = !!formData.value.id
    const url    = isEdit ? `/api/admin/supporters/${formData.value.id}` : '/api/admin/supporters'
    const res    = await fetch(url, {
      method: isEdit ? 'PUT' : 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ name: formData.value.name.trim(), url: formData.value.url.trim() || null, sortOrder: formData.value.sortOrder ?? 0 }),
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) { formError.value = data.message ?? 'Could not save.'; return }
    formOpen.value = false
    await loadSupporters()
  } catch {
    formError.value = 'Could not save.'
  } finally {
    formSaving.value = false
  }
}

// ── Delete ────────────────────────────────────────────────────────────────────

const deleteOpen    = ref(false)
const deleteLoading = ref(false)
const deleteError   = ref('')
const deleteTarget  = ref(null)

function askDelete(s) {
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
      method: 'DELETE', credentials: 'include', headers: { Accept: 'application/json' },
    })
    if (!res.ok && res.status !== 204) {
      const data = await res.json().catch(() => ({}))
      deleteError.value = data.message ?? 'Could not delete.'
      return
    }
    deleteOpen.value   = false
    deleteTarget.value = null
    await loadSupporters()
  } catch {
    deleteError.value = 'Could not delete.'
  } finally {
    deleteLoading.value = false
  }
}

// ── Logo upload ───────────────────────────────────────────────────────────────

async function uploadLogo(id, event) {
  const file = event.target.files?.[0]
  if (!file) return
  const form = new FormData()
  form.append('logo', file)
  try {
    const res = await fetch(`/api/admin/supporters/${id}/logo`, { method: 'POST', credentials: 'include', body: form })
    if (!res.ok) { listError.value = 'Logo upload failed.'; return }
    await loadSupporters()
  } catch {
    listError.value = 'Logo upload failed.'
  } finally {
    event.target.value = ''
  }
}

onMounted(loadSupporters)
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
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.table-card {
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.07);
  background: rgba(255, 255, 255, 0.025);
  overflow: hidden;
}

.logo-cell {
  display: flex;
  align-items: center;
}

.logo-thumb {
  max-width: 60px;
  max-height: 34px;
  object-fit: contain;
}

.url-link {
  font-size: 0.75rem;
  color: #4ecdc4;
  text-decoration: none;
  display: block;
  max-width: 280px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;

  &:hover { text-decoration: underline; }
}

.upload-icon-btn {
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 50%;
  color: rgba(255,255,255,0.45);
  transition: border-color 0.15s, color 0.15s;

  &:hover {
    border-color: #4ecdc4;
    color: #4ecdc4;
  }
}

.form-dialog {
  width: 100%;
  max-width: 440px;
  background: #161616;
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.09);
}

.form-title {
  font-size: 1rem;
  font-weight: 700;
  color: #f5f5f5;
}

.err-banner {
  background: rgba(231, 76, 60, 0.12);
  border: 1px solid rgba(231, 76, 60, 0.35);
  color: rgba(255, 255, 255, 0.9);
}

:deep(.sup-table) {
  background: transparent;

  th { color: rgba(255,255,255,0.4) !important; font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; }
  td { border-bottom-color: rgba(255,255,255,0.05) !important; }
}
</style>
