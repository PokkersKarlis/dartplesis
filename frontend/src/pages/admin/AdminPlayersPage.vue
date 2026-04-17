<template>
  <q-page class="admin-page q-pa-lg">

    <!-- Header -->
    <div class="row items-end justify-between q-mb-lg q-col-gutter-md">
      <div>
        <div class="page-kicker">Management</div>
        <div class="page-title">Players</div>
      </div>
      <div class="row q-gutter-sm items-center">
        <q-input
          v-model="search"
          dense
          standout="bg-grey-10 text-white"
          placeholder="Search players…"
          clearable
          style="min-width: 220px"
        >
          <template #prepend><q-icon name="search" /></template>
        </q-input>
        <q-btn color="primary" unelevated icon="person_add" label="Add Player" @click="openDialog()" />
      </div>
    </div>

    <!-- Table -->
    <q-card flat class="table-card">
      <q-table
        :rows="filteredPlayers"
        :columns="columns"
        row-key="id"
        flat
        dark
        :loading="tableLoading"
        :pagination="{ rowsPerPage: 15 }"
        :no-data-label="search ? 'No players match your search.' : 'No players yet. Add the first one!'"
        class="players-table"
      >
        <template #body-cell-photo="{ row }">
          <q-td>
            <q-avatar size="36px">
              <img v-if="row.photo" :src="row.photo" />
              <q-icon v-else name="person" color="grey-6" size="22px" />
            </q-avatar>
          </q-td>
        </template>

        <template #body-cell-name="{ row }">
          <q-td>
            <div class="text-weight-bold text-grey-2">{{ row.name }} {{ row.surname }}</div>
            <div v-if="row.nickname" class="text-caption text-teal-4">"{{ row.nickname }}"</div>
          </q-td>
        </template>

        <template #body-cell-roles="{ row }">
          <q-td>
            <div class="row q-gutter-xs">
              <q-badge
                v-for="role in (row.clubRoles ?? []).slice(0,2)"
                :key="role"
                color="grey-8"
                rounded
                class="text-grey-3"
              >{{ ROLE_LABELS[role] ?? role }}</q-badge>
              <q-badge v-if="(row.clubRoles ?? []).length > 2" color="grey-9" rounded>
                +{{ row.clubRoles.length - 2 }}
              </q-badge>
            </div>
          </q-td>
        </template>

        <template #body-cell-dob="{ row }">
          <q-td class="text-grey-4">{{ row.dateOfBirth ?? '—' }}</q-td>
        </template>

        <template #body-cell-isJunior="{ row }">
          <q-td>
            <q-badge :color="row.isJunior ? 'teal-7' : 'grey-8'" rounded>
              {{ row.isJunior ? 'Junior' : 'Senior' }}
            </q-badge>
          </q-td>
        </template>

        <template #body-cell-highestCheckout="{ row }">
          <q-td>
            <q-badge v-if="row.highestCheckout" :color="checkoutColor(row.highestCheckout)" rounded>
              {{ row.highestCheckout }}
            </q-badge>
            <span v-else class="text-grey-7">—</span>
          </q-td>
        </template>

        <template #body-cell-actions="{ row }">
          <q-td class="text-right">
            <q-btn flat round dense icon="edit"   size="sm" color="grey-5"    @click="openDialog(row)" />
            <q-btn flat round dense icon="delete" size="sm" color="negative"  @click="confirmDelete(row)" />
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- ── Player dialog ── -->
    <q-dialog v-model="dialogOpen" persistent maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card class="dialog-card">
        <q-bar class="dialog-bar">
          <q-icon name="people" color="primary" />
          <div class="dialog-bar-title">{{ editingId ? 'Edit Player' : 'New Player' }}</div>
          <q-space />
          <q-btn flat round dense icon="close" color="grey-5" v-close-popup />
        </q-bar>

        <q-scroll-area style="height: calc(100vh - 50px)">
          <q-card-section class="dialog-body">
            <q-form ref="formRef" class="q-gutter-y-lg" @submit.prevent="savePlayer">

              <!-- 1. Identity -->
              <section>
                <div class="form-section-title"><q-icon name="badge" size="15px" class="q-mr-xs" />Identity</div>
                <div class="form-grid q-mt-sm">
                  <q-input v-model="form.name"    outlined dark dense label="Name *"    :rules="[v => !!v?.trim() || 'Required']" lazy-rules />
                  <q-input v-model="form.surname"  outlined dark dense label="Surname *" :rules="[v => !!v?.trim() || 'Required']" lazy-rules />
                  <q-input v-model="form.nickname" outlined dark dense label="Nickname"  placeholder="e.g. The Bull" />
                  <q-input v-model="form.dateOfBirth" outlined dark dense label="Date of Birth" type="date" />
                  <div class="form-field-stack">
                    <div class="form-label">Category</div>
                    <q-btn-toggle
                      v-model="form.isJunior"
                      :options="[{label:'Senior', value: false}, {label:'Junior', value: true}]"
                      toggle-color="primary"
                      color="grey-9"
                      text-color="grey-4"
                      unelevated
                      rounded
                    />
                  </div>
                </div>
              </section>

              <!-- 2. Club Roles -->
              <section>
                <div class="form-section-title"><q-icon name="group" size="15px" class="q-mr-xs" />Club Roles</div>
                <div class="q-mt-sm">
                  <q-select
                    v-model="form.clubRoles"
                    outlined dark dense
                    label="Roles in the club"
                    :options="CLUB_ROLE_OPTIONS"
                    multiple
                    use-chips
                    emit-value
                    map-options
                    hint="Select all that apply"
                  />
                </div>
              </section>

              <!-- 3. Photo -->
              <section>
                <div class="form-section-title"><q-icon name="photo_camera" size="15px" class="q-mr-xs" />Photo</div>
                <div class="row items-center q-gutter-md q-mt-sm">
                  <q-avatar size="72px" class="photo-avatar">
                    <img v-if="photoPreview || form.photo" :src="photoPreview || form.photo" />
                    <q-icon v-else name="person" color="grey-6" size="40px" />
                  </q-avatar>
                  <div>
                    <q-btn
                      outline color="grey-5" icon="upload" label="Upload photo"
                      size="sm" @click="triggerFileInput"
                    />
                    <q-btn
                      v-if="photoPreview || form.photo"
                      flat color="negative" icon="delete" label="Remove"
                      size="sm" class="q-ml-sm" @click="removePhoto"
                    />
                    <div class="text-caption text-grey-6 q-mt-xs">JPG, PNG, WebP · max 5MB</div>
                    <input ref="fileInputRef" type="file" accept="image/*" class="hidden-input" @change="onFileChange" />
                  </div>
                </div>
              </section>

              <!-- 4. Darts Setup -->
              <section>
                <div class="form-section-title"><q-icon name="sports_bar" size="15px" class="q-mr-xs" />Darts Setup</div>
                <div class="form-grid q-mt-sm">
                  <q-input v-model="form.dartWeight"     outlined dark dense label="Dart Weight"   placeholder="e.g. 23g" />
                  <q-input v-model="form.dartModel"      outlined dark dense label="Dart Model"    placeholder="e.g. Unicorn Phase 5" />
                  <q-select v-model="form.gripStyle"     outlined dark dense label="Grip Style"    :options="GRIP_STYLES" clearable emit-value map-options />
                  <q-select
                    v-model="form.favoriteDouble"
                    outlined dark dense label="Favourite Double"
                    :options="DOUBLES" clearable emit-value map-options
                    use-input input-debounce="0"
                    @filter="filterDoubles"
                  >
                    <template #no-option><q-item><q-item-section class="text-grey-6">No match</q-item-section></q-item></template>
                  </q-select>
                  <q-input v-model="form.walkOutSong" outlined dark dense label="Walk-out Song" placeholder="e.g. Eye of the Tiger" class="form-grid-wide" />
                </div>
              </section>

              <!-- 5. Stats -->
              <section>
                <div class="form-section-title"><q-icon name="emoji_events" size="15px" class="q-mr-xs" />Stats & Achievements</div>
                <div class="form-grid q-mt-sm">
                  <q-input
                    v-model.number="form.highestCheckout"
                    outlined dark dense label="Highest Checkout"
                    type="number" hint="Max 170"
                    :rules="[v => !v || (v >= 2 && v <= 170) || 'Must be 2–170']"
                    lazy-rules
                  />
                  <q-input v-model="form.careerHighlight" outlined dark dense label="Career Highlight" type="textarea" autogrow class="form-grid-wide" />
                </div>
              </section>

              <!-- 6. Fun Facts -->
              <section>
                <div class="form-section-title"><q-icon name="auto_awesome" size="15px" class="q-mr-xs" />Fun Facts</div>
                <div class="form-grid q-mt-sm">
                  <q-input v-model="form.dartsIdol"    outlined dark dense label="Darts Idol"       placeholder="e.g. Phil Taylor" />
                  <q-input v-model="form.hobbies"       outlined dark dense label="Hobbies"          placeholder="e.g. Football, Gaming" />
                  <q-input v-model="form.preGameRitual" outlined dark dense label="Pre-game Ritual"  type="textarea" autogrow class="form-grid-wide" />
                  <q-input v-model="form.lifeMotto"     outlined dark dense label="Life Motto"        type="textarea" autogrow class="form-grid-wide" />
                </div>
              </section>

              <div class="row justify-end q-gutter-sm q-pt-sm">
                <q-btn flat color="grey-5" label="Cancel" v-close-popup />
                <q-btn type="submit" color="primary" unelevated :loading="saving" :label="editingId ? 'Save Changes' : 'Create Player'" icon-right="check" />
              </div>

            </q-form>
          </q-card-section>
        </q-scroll-area>
      </q-card>
    </q-dialog>

    <!-- Delete confirm -->
    <q-dialog v-model="deleteDialog" persistent>
      <q-card flat class="confirm-card">
        <q-card-section>
          <div class="text-h6">Delete player?</div>
          <p class="q-mt-sm text-grey-5"><b>{{ deleteTarget?.name }} {{ deleteTarget?.surname }}</b> will be permanently removed.</p>
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
import { computed, nextTick, ref } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()

// ── Constants ─────────────────────────────────────────────────────────────────
const GRIP_STYLES = ['Front', 'Middle', 'Rear']
const ALL_DOUBLES = ['D1','D2','D3','D4','D5','D6','D7','D8','D9','D10',
                     'D11','D12','D13','D14','D15','D16','D17','D18','D19','D20','Bull']
const DOUBLES = ref([...ALL_DOUBLES])

const CLUB_ROLE_OPTIONS = [
  { label: 'Player',           value: 'player'           },
  { label: 'Master Player',    value: 'master_player'    },
  { label: 'Trainer',          value: 'trainer'          },
  { label: 'Master Yoda',      value: 'master_yoda'      },
  { label: 'Club Manager',     value: 'club_manager'     },
  { label: 'Club Captain',     value: 'club_captain'     },
  { label: 'Vice Captain',     value: 'vice_captain'     },
  { label: 'Committee Member', value: 'committee_member' },
  { label: 'Social Media',     value: 'social_media'     },
]

const ROLE_LABELS = Object.fromEntries(CLUB_ROLE_OPTIONS.map(o => [o.value, o.label]))

function filterDoubles(val, update) {
  update(() => {
    const q = val.toLowerCase()
    DOUBLES.value = ALL_DOUBLES.filter(d => d.toLowerCase().includes(q))
  })
}

// ── Columns ───────────────────────────────────────────────────────────────────
const columns = [
  { name: 'photo',           label: '',           field: 'photo',           sortable: false, align: 'center' },
  { name: 'name',            label: 'Name',       field: 'name',            sortable: true,  align: 'left'   },
  { name: 'roles',           label: 'Roles',      field: 'clubRoles',       sortable: false, align: 'left'   },
  { name: 'dob',             label: 'Born',       field: 'dateOfBirth',     sortable: true,  align: 'left'   },
  { name: 'isJunior',        label: 'Category',   field: 'isJunior',        sortable: true,  align: 'center' },
  { name: 'favoriteDouble',  label: 'Fav. D',     field: 'favoriteDouble',  sortable: true,  align: 'center' },
  { name: 'dartWeight',      label: 'Weight',     field: 'dartWeight',      sortable: false, align: 'center' },
  { name: 'highestCheckout', label: 'Best CO',    field: 'highestCheckout', sortable: true,  align: 'center' },
  { name: 'actions',         label: '',           field: 'actions',         sortable: false, align: 'right'  },
]

// ── Data ──────────────────────────────────────────────────────────────────────
const players      = ref([])
const tableLoading = ref(true)
const search       = ref('')

const filteredPlayers = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return players.value
  return players.value.filter(p =>
    `${p.name} ${p.surname} ${p.nickname ?? ''}`.toLowerCase().includes(q)
  )
})

function checkoutColor(v) {
  if (v >= 161) return 'amber-8'
  if (v >= 100) return 'teal-6'
  return 'grey-6'
}

async function loadPlayers() {
  tableLoading.value = true
  try {
    const res = await fetch('/api/admin/players', { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json()
    players.value = data.players ?? []
  } catch {
    $q.notify({ type: 'negative', message: 'Failed to load players.' })
  } finally {
    tableLoading.value = false
  }
}

// ── Photo ─────────────────────────────────────────────────────────────────────
const fileInputRef  = ref(null)
const photoPreview  = ref(null)
const photoFile     = ref(null)

function triggerFileInput() { fileInputRef.value?.click() }

function onFileChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    $q.notify({ type: 'warning', message: 'File too large. Max 5MB.' })
    return
  }
  photoFile.value = file
  const reader = new FileReader()
  reader.onload = (ev) => { photoPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function removePhoto() {
  photoPreview.value = null
  photoFile.value    = null
  form.value.photo   = null
  if (fileInputRef.value) fileInputRef.value.value = ''
}

async function uploadPhoto(playerId) {
  if (!photoFile.value) return
  const fd = new FormData()
  fd.append('photo', photoFile.value)
  const res = await fetch(`/api/admin/players/${playerId}/photo`, {
    method: 'POST',
    credentials: 'include',
    body: fd,
  })
  const data = await res.json().catch(() => ({}))
  if (res.ok) {
    const idx = players.value.findIndex(p => p.id === playerId)
    if (idx !== -1) players.value[idx].photo = data.photo
  }
}

// ── Form / Dialog ─────────────────────────────────────────────────────────────
const dialogOpen = ref(false)
const saving     = ref(false)
const editingId  = ref(null)
const formRef    = ref(null)

const EMPTY_FORM = () => ({
  name: '', surname: '', nickname: '', dateOfBirth: '', isJunior: false,
  clubRoles: [],
  photo: null,
  walkOutSong: '', favoriteDouble: null, dartWeight: '',
  dartModel: '', gripStyle: null, highestCheckout: null,
  careerHighlight: '', preGameRitual: '', dartsIdol: '',
  hobbies: '', lifeMotto: '',
})

const form = ref(EMPTY_FORM())

function openDialog(player = null) {
  editingId.value    = player?.id ?? null
  photoPreview.value = null
  photoFile.value    = null
  form.value = player
    ? {
        name: player.name ?? '', surname: player.surname ?? '',
        nickname: player.nickname ?? '', dateOfBirth: player.dateOfBirth ?? '',
        isJunior: player.isJunior ?? false, clubRoles: player.clubRoles ?? [],
        photo: player.photo ?? null,
        walkOutSong: player.walkOutSong ?? '', favoriteDouble: player.favoriteDouble ?? null,
        dartWeight: player.dartWeight ?? '', dartModel: player.dartModel ?? '',
        gripStyle: player.gripStyle ?? null, highestCheckout: player.highestCheckout ?? null,
        careerHighlight: player.careerHighlight ?? '', preGameRitual: player.preGameRitual ?? '',
        dartsIdol: player.dartsIdol ?? '', hobbies: player.hobbies ?? '', lifeMotto: player.lifeMotto ?? '',
      }
    : EMPTY_FORM()
  dialogOpen.value = true
  nextTick(() => formRef.value?.resetValidation())
}

async function savePlayer() {
  const valid = await formRef.value?.validate()
  if (!valid) return

  saving.value = true
  const payload = {
    ...form.value,
    highestCheckout: form.value.highestCheckout || null,
    dateOfBirth: form.value.dateOfBirth || null,
  }
  for (const key of ['nickname','walkOutSong','dartWeight','dartModel',
                      'careerHighlight','preGameRitual','dartsIdol','hobbies','lifeMotto']) {
    if (payload[key] === '') payload[key] = null
  }
  delete payload.photo  // photo handled separately

  try {
    const url    = editingId.value ? `/api/admin/players/${editingId.value}` : '/api/admin/players'
    const method = editingId.value ? 'PUT' : 'POST'
    const res    = await fetch(url, {
      method, credentials: 'include',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(payload),
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message ?? 'Save failed.')

    const savedId = editingId.value ?? data.player.id
    if (editingId.value) {
      const idx = players.value.findIndex(p => p.id === editingId.value)
      if (idx !== -1) players.value[idx] = data.player
    } else {
      players.value.push(data.player)
    }

    // Upload photo if one was selected
    if (photoFile.value) await uploadPhoto(savedId)

    dialogOpen.value = false
    $q.notify({ type: 'positive', message: editingId.value ? 'Player updated.' : 'Player created.' })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message })
  } finally {
    saving.value = false
  }
}

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteDialog = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)

function confirmDelete(player) { deleteTarget.value = player; deleteDialog.value = true }

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    const res = await fetch(`/api/admin/players/${deleteTarget.value.id}`, {
      method: 'DELETE', credentials: 'include',
    })
    if (!res.ok && res.status !== 204) throw new Error('Delete failed.')
    players.value = players.value.filter(p => p.id !== deleteTarget.value.id)
    deleteDialog.value = false
    $q.notify({ type: 'positive', message: 'Player deleted.' })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message })
  } finally {
    deleting.value = false
  }
}

loadPlayers()
</script>

<style scoped lang="scss">
.admin-page { min-height: 100%; background: #111; color: #f5f5f5; }

.page-kicker { font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.4); }
.page-title  { font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 900; letter-spacing: -0.03em; line-height: 1.1; }

.table-card {
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.07);
  background: rgba(255,255,255,0.02);
  overflow: hidden;
}

.players-table {
  background: transparent;
  :deep(thead tr th) { font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.38); border-bottom: 1px solid rgba(255,255,255,0.07); }
  :deep(tbody tr:hover td) { background: rgba(255,255,255,0.03); }
}

.dialog-card { background: #111; color: #f5f5f5; max-width: 760px; width: 100%; margin: 0 auto; }
.dialog-bar  { background: #0d0d0d; border-bottom: 1px solid rgba(255,255,255,0.07); padding: 0 16px; min-height: 50px; }
.dialog-bar-title { font-weight: 700; font-size: 0.95rem; margin-left: 8px; }
.dialog-body { max-width: 760px; width: 100%; margin: 0 auto; }

.form-section-title {
  display: flex; align-items: center;
  font-size: 0.7rem; letter-spacing: 0.16em; text-transform: uppercase;
  color: rgba(255,255,255,0.4); font-weight: 600; margin-bottom: 4px;
}

.form-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  @media (max-width: 600px) { grid-template-columns: 1fr; }
}
.form-grid-wide { grid-column: span 2; @media (max-width: 600px) { grid-column: span 1; } }

.form-field-stack { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 0.75rem; color: rgba(255,255,255,0.45); letter-spacing: 0.04em; }

.photo-avatar {
  border: 2px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  background: rgba(255,255,255,0.05);
  overflow: hidden;
  img { object-fit: cover; width: 100%; height: 100%; }
}

.hidden-input { display: none; }

.confirm-card {
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.07);
  background: #161616;
  color: #f5f5f5;
  min-width: 320px;
}
</style>
