<template>
  <q-page class="admin-page q-pa-lg">

    <!-- Header -->
    <div class="row items-end justify-between q-mb-lg q-col-gutter-md">
      <div>
        <div class="page-kicker">Membership</div>
        <div class="page-title">
          Club Requests
          <q-badge v-if="pendingCount > 0" color="negative" floating rounded class="q-ml-sm" style="position:relative;top:0;right:0">
            {{ pendingCount }}
          </q-badge>
        </div>
      </div>
    </div>

    <!-- Status tabs -->
    <q-tabs
      v-model="activeTab"
      dense
      class="status-tabs q-mb-md"
      indicator-color="primary"
      active-color="primary"
      align="left"
    >
      <q-tab name="pending"  label="Pending"  />
      <q-tab name="approved" label="Approved" />
      <q-tab name="rejected" label="Rejected" />
      <q-tab name="all"      label="All"      />
    </q-tabs>

    <!-- Table -->
    <q-card flat class="table-card">
      <q-table
        :rows="filteredRequests"
        :columns="columns"
        row-key="id"
        flat dark
        :loading="loading"
        :pagination="{ rowsPerPage: 20 }"
        no-data-label="No requests in this category."
        class="requests-table"
      >
        <!-- Photo -->
        <template #body-cell-photo="{ row }">
          <q-td>
            <q-avatar size="36px">
              <img v-if="row.photo" :src="row.photo" />
              <q-icon v-else name="person" color="grey-6" size="22px" />
            </q-avatar>
          </q-td>
        </template>

        <!-- Name -->
        <template #body-cell-name="{ row }">
          <q-td>
            <div class="text-weight-bold text-grey-2">{{ row.name }} {{ row.surname }}</div>
            <div v-if="row.nickname" class="text-caption text-teal-4">"{{ row.nickname }}"</div>
          </q-td>
        </template>

        <!-- Contact -->
        <template #body-cell-contact="{ row }">
          <q-td>
            <div class="text-grey-3 text-caption">{{ row.email }}</div>
            <div class="text-grey-5 text-caption">{{ row.phone }}</div>
          </q-td>
        </template>

        <!-- Roles -->
        <template #body-cell-roles="{ row }">
          <q-td>
            <div class="row q-gutter-xs">
              <q-badge
                v-for="role in (row.clubRoles ?? []).slice(0,2)"
                :key="role"
                color="grey-8" rounded class="text-grey-3"
              >{{ ROLE_LABELS[role] ?? role }}</q-badge>
              <q-badge v-if="(row.clubRoles ?? []).length > 2" color="grey-9" rounded>
                +{{ row.clubRoles.length - 2 }}
              </q-badge>
            </div>
          </q-td>
        </template>

        <!-- Status -->
        <template #body-cell-status="{ row }">
          <q-td>
            <q-badge :color="statusColor(row.status)" rounded>{{ row.status }}</q-badge>
          </q-td>
        </template>

        <!-- Date -->
        <template #body-cell-createdAt="{ row }">
          <q-td class="text-grey-5 text-caption">{{ formatDate(row.createdAt) }}</q-td>
        </template>

        <!-- Actions -->
        <template #body-cell-actions="{ row }">
          <q-td class="text-right">
            <q-btn flat round dense icon="visibility" size="sm" color="grey-5" @click="openDetail(row)" title="View details" />
            <template v-if="row.status === 'pending'">
              <q-btn flat round dense icon="check_circle" size="sm" color="positive" :loading="approvingId === row.id" @click="approveRequest(row)" title="Approve" />
              <q-btn flat round dense icon="cancel"       size="sm" color="warning"  :loading="rejectingId === row.id"  @click="rejectRequest(row)"  title="Reject" />
            </template>
            <q-btn flat round dense icon="delete" size="sm" color="negative" @click="confirmDelete(row)" title="Delete" />
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Detail dialog -->
    <q-dialog v-model="detailOpen" maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card class="dialog-card" v-if="detailRow">
        <q-bar class="dialog-bar">
          <q-icon name="person" color="primary" />
          <div class="dialog-bar-title">{{ detailRow.name }} {{ detailRow.surname }}</div>
          <q-badge :color="statusColor(detailRow.status)" class="q-ml-sm">{{ detailRow.status }}</q-badge>
          <q-space />
          <q-btn flat round dense icon="close" color="grey-5" v-close-popup />
        </q-bar>

        <q-scroll-area style="height: calc(100vh - 50px)">
          <q-card-section class="detail-body">
            <div class="detail-grid">

              <!-- Photo + identity -->
              <div class="detail-section">
                <div class="detail-section-title">Identity</div>
                <div class="row items-center q-gutter-md q-mb-md">
                  <q-avatar size="80px" class="detail-photo">
                    <img v-if="detailRow.photo" :src="detailRow.photo" />
                    <q-icon v-else name="person" color="grey-6" size="48px" />
                  </q-avatar>
                  <div>
                    <div class="text-h6 text-grey-2">{{ detailRow.name }} {{ detailRow.surname }}</div>
                    <div v-if="detailRow.nickname" class="text-teal-4">"{{ detailRow.nickname }}"</div>
                    <q-badge :color="detailRow.isJunior ? 'teal-7' : 'grey-7'" rounded class="q-mt-xs">{{ detailRow.isJunior ? 'Junior' : 'Senior' }}</q-badge>
                  </div>
                </div>
                <div v-for="f in identityFields" :key="f.key" class="detail-row">
                  <span class="detail-key">{{ f.label }}</span>
                  <span class="detail-val">{{ detailRow[f.key] || '—' }}</span>
                </div>
              </div>

              <!-- Contact -->
              <div class="detail-section">
                <div class="detail-section-title">Contact</div>
                <div class="detail-row"><span class="detail-key">Email</span><span class="detail-val">{{ detailRow.email }}</span></div>
                <div class="detail-row"><span class="detail-key">Phone</span><span class="detail-val">{{ detailRow.phone }}</span></div>
              </div>

              <!-- Club roles -->
              <div class="detail-section">
                <div class="detail-section-title">Club Roles</div>
                <div class="row q-gutter-xs q-mt-xs">
                  <q-badge v-for="r in detailRow.clubRoles" :key="r" color="primary" rounded>{{ ROLE_LABELS[r] ?? r }}</q-badge>
                  <span v-if="!detailRow.clubRoles?.length" class="text-grey-6">None selected</span>
                </div>
              </div>

              <!-- Darts setup -->
              <div class="detail-section">
                <div class="detail-section-title">Darts Setup</div>
                <div v-for="f in dartsFields" :key="f.key" class="detail-row">
                  <span class="detail-key">{{ f.label }}</span>
                  <span class="detail-val">{{ detailRow[f.key] || '—' }}</span>
                </div>
              </div>

              <!-- About -->
              <div class="detail-section detail-section--wide">
                <div class="detail-section-title">About</div>
                <div v-for="f in aboutFields" :key="f.key" class="detail-row detail-row--block">
                  <span class="detail-key">{{ f.label }}</span>
                  <span class="detail-val">{{ detailRow[f.key] || '—' }}</span>
                </div>
              </div>
            </div>

            <!-- Action bar -->
            <div v-if="detailRow.status === 'pending'" class="action-bar q-mt-xl">
              <q-btn
                unelevated color="positive" icon="check_circle" label="Approve → Create Player"
                :loading="approvingId === detailRow.id"
                @click="approveRequest(detailRow)"
              />
              <q-btn
                outline color="warning" icon="cancel" label="Reject"
                :loading="rejectingId === detailRow.id"
                @click="rejectRequest(detailRow)"
              />
            </div>
            <div v-else-if="detailRow.approvedPlayerId" class="action-bar q-mt-xl">
              <q-chip color="positive" icon="check_circle" text-color="white">
                Approved → Player #{{ detailRow.approvedPlayerId }}
              </q-chip>
            </div>
          </q-card-section>
        </q-scroll-area>
      </q-card>
    </q-dialog>

    <!-- Delete confirm -->
    <q-dialog v-model="deleteDialog" persistent>
      <q-card flat class="confirm-card">
        <q-card-section>
          <div class="text-h6">Delete request?</div>
          <p class="q-mt-sm text-grey-5">This will permanently remove the application from <b>{{ deleteTarget?.name }} {{ deleteTarget?.surname }}</b>.</p>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat color="grey-5" label="Cancel" v-close-popup />
          <q-btn unelevated color="negative" label="Delete" :loading="deleting" @click="doDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()

// ── Constants ─────────────────────────────────────────────────────────────────
const ROLE_LABELS = {
  player: 'Player', master_player: 'Master Player', trainer: 'Trainer',
  master_yoda: 'Master Yoda', club_manager: 'Club Manager', club_captain: 'Club Captain',
  vice_captain: 'Vice Captain', committee_member: 'Committee Member', social_media: 'Social Media',
}

const identityFields = [
  { key: 'dateOfBirth', label: 'Date of Birth' },
]
const dartsFields = [
  { key: 'dartWeight',    label: 'Dart Weight'   },
  { key: 'dartModel',     label: 'Dart Model'    },
  { key: 'gripStyle',     label: 'Grip Style'    },
  { key: 'favoriteDouble',label: 'Fav. Double'   },
  { key: 'walkOutSong',   label: 'Walk-out Song' },
  { key: 'highestCheckout', label: 'Best Checkout'},
]
const aboutFields = [
  { key: 'dartsIdol',      label: 'Darts Idol'      },
  { key: 'hobbies',        label: 'Hobbies'          },
  { key: 'preGameRitual',  label: 'Pre-game Ritual'  },
  { key: 'careerHighlight',label: 'Career Highlight' },
  { key: 'lifeMotto',      label: 'Life Motto'       },
]

// ── Columns ───────────────────────────────────────────────────────────────────
const columns = [
  { name: 'photo',     label: '',          field: 'photo',     sortable: false, align: 'center' },
  { name: 'name',      label: 'Name',      field: 'name',      sortable: true,  align: 'left'   },
  { name: 'contact',   label: 'Contact',   field: 'email',     sortable: true,  align: 'left'   },
  { name: 'roles',     label: 'Roles',     field: 'clubRoles', sortable: false, align: 'left'   },
  { name: 'status',    label: 'Status',    field: 'status',    sortable: true,  align: 'center' },
  { name: 'createdAt', label: 'Submitted', field: 'createdAt', sortable: true,  align: 'left'   },
  { name: 'actions',   label: '',          field: 'actions',   sortable: false, align: 'right'  },
]

function statusColor(s) {
  return { pending: 'orange-8', approved: 'positive', rejected: 'negative' }[s] ?? 'grey-7'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d.replace(' ', 'T')).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

// ── Data ──────────────────────────────────────────────────────────────────────
const requests      = ref([])
const pendingCount  = ref(0)
const loading       = ref(true)
const activeTab     = ref('pending')

const filteredRequests = computed(() => {
  if (activeTab.value === 'all') return requests.value
  return requests.value.filter(r => r.status === activeTab.value)
})

async function loadRequests() {
  loading.value = true
  try {
    const res  = await fetch('/api/admin/club-requests', { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json()
    requests.value    = data.requests ?? []
    pendingCount.value = data.pendingCount ?? 0
  } catch {
    $q.notify({ type: 'negative', message: 'Failed to load requests.' })
  } finally {
    loading.value = false
  }
}

// ── Approve ───────────────────────────────────────────────────────────────────
const approvingId = ref(null)

async function approveRequest(row) {
  approvingId.value = row.id
  try {
    const res  = await fetch(`/api/admin/club-requests/${row.id}/approve`, {
      method: 'POST', credentials: 'include', headers: { Accept: 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message ?? 'Approve failed.')

    // Update local state
    const idx = requests.value.findIndex(r => r.id === row.id)
    if (idx !== -1) requests.value[idx] = data.request
    pendingCount.value = Math.max(0, pendingCount.value - 1)
    if (detailRow.value?.id === row.id) detailRow.value = data.request

    $q.notify({ type: 'positive', message: `Player profile created for ${row.name} ${row.surname}.` })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message })
  } finally {
    approvingId.value = null
  }
}

// ── Reject ────────────────────────────────────────────────────────────────────
const rejectingId = ref(null)

async function rejectRequest(row) {
  rejectingId.value = row.id
  try {
    const res  = await fetch(`/api/admin/club-requests/${row.id}/reject`, {
      method: 'POST', credentials: 'include', headers: { Accept: 'application/json' }
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message ?? 'Reject failed.')

    const idx = requests.value.findIndex(r => r.id === row.id)
    if (idx !== -1) requests.value[idx] = data.request
    pendingCount.value = Math.max(0, pendingCount.value - 1)
    if (detailRow.value?.id === row.id) detailRow.value = data.request

    $q.notify({ type: 'info', message: 'Request rejected.' })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message })
  } finally {
    rejectingId.value = null
  }
}

// ── Detail dialog ─────────────────────────────────────────────────────────────
const detailOpen = ref(false)
const detailRow  = ref(null)

function openDetail(row) { detailRow.value = row; detailOpen.value = true }

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteDialog = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)

function confirmDelete(row) { deleteTarget.value = row; deleteDialog.value = true }

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    const res = await fetch(`/api/admin/club-requests/${deleteTarget.value.id}`, {
      method: 'DELETE', credentials: 'include'
    })
    if (!res.ok && res.status !== 204) throw new Error('Delete failed.')
    requests.value = requests.value.filter(r => r.id !== deleteTarget.value.id)
    if (deleteTarget.value.status === 'pending') pendingCount.value = Math.max(0, pendingCount.value - 1)
    deleteDialog.value = false
    if (detailOpen.value && detailRow.value?.id === deleteTarget.value.id) detailOpen.value = false
    $q.notify({ type: 'positive', message: 'Request deleted.' })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message })
  } finally {
    deleting.value = false
  }
}

loadRequests()
</script>

<style scoped lang="scss">
.admin-page { min-height: 100%; background: #111; color: #f5f5f5; }
.page-kicker { font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.4); }
.page-title  { font-size: clamp(1.6rem,3vw,2.2rem); font-weight: 900; letter-spacing: -0.03em; line-height: 1.1; display: flex; align-items: center; }

.status-tabs {
  :deep(.q-tab) { color: rgba(255,255,255,0.45); font-size: 0.82rem; letter-spacing: 0.08em; text-transform: uppercase; }
  :deep(.q-tab--active) { color: #4ecdc4; }
}

.table-card { border-radius: 16px; border: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.02); overflow: hidden; }
.requests-table {
  background: transparent;
  :deep(thead tr th) { font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.38); border-bottom: 1px solid rgba(255,255,255,0.07); }
  :deep(tbody tr:hover td) { background: rgba(255,255,255,0.03); }
}

.dialog-card  { background: #111; color: #f5f5f5; max-width: 860px; width: 100%; margin: 0 auto; }
.dialog-bar   { background: #0d0d0d; border-bottom: 1px solid rgba(255,255,255,0.07); padding: 0 16px; min-height: 50px; }
.dialog-bar-title { font-weight: 700; font-size: 0.95rem; margin-left: 8px; }

.detail-body  { max-width: 860px; margin: 0 auto; }
.detail-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; @media(max-width:700px){ grid-template-columns: 1fr; } }

.detail-section {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 14px;
  padding: 16px;
  &--wide { grid-column: span 2; @media(max-width:700px){ grid-column: span 1; } }
}
.detail-section-title { font-size: 0.68rem; letter-spacing: 0.16em; text-transform: uppercase; color: rgba(255,255,255,0.35); font-weight: 600; margin-bottom: 10px; }

.detail-row {
  display: flex; align-items: flex-start; justify-content: space-between;
  gap: 8px; padding: 4px 0;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  &:last-child { border-bottom: none; }
  &--block { flex-direction: column; gap: 2px; }
}
.detail-key { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.35); white-space: nowrap; }
.detail-val { font-size: 0.88rem; color: rgba(255,255,255,0.78); text-align: right; word-break: break-word; }
.detail-row--block .detail-val { text-align: left; }

.detail-photo { border-radius: 12px; border: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); overflow: hidden; img { object-fit: cover; width: 100%; height: 100%; } }

.action-bar { display: flex; gap: 12px; flex-wrap: wrap; }

.confirm-card { border-radius: 16px; border: 1px solid rgba(255,255,255,0.07); background: #161616; color: #f5f5f5; min-width: 320px; }
</style>
