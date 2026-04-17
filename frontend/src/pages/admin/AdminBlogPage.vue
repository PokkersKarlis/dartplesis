<template>
  <q-page class="admin-page q-pa-lg">

    <!-- Header -->
    <div class="row items-end justify-between q-mb-lg q-col-gutter-md">
      <div>
        <div class="page-kicker">{{ t('admin_blog.kicker') }}</div>
        <div class="page-title">{{ t('admin_blog.title') }}</div>
      </div>
      <div class="row q-gutter-sm items-center">
        <q-select
          v-model="statusFilter"
          :options="statusOptions"
          dense
          standout="bg-grey-10 text-white"
          emit-value map-options
          style="min-width: 140px"
        />
        <q-input
          v-model="search"
          dense
          standout="bg-grey-10 text-white"
          :placeholder="t('admin_blog.search_ph')"
          clearable
          style="min-width: 220px"
          @update:model-value="debouncedLoad"
        >
          <template #prepend><q-icon name="search" /></template>
        </q-input>
        <q-btn color="primary" unelevated icon="add" :label="t('admin_blog.add_btn')" @click="openDialog()" />
      </div>
    </div>

    <!-- Table -->
    <q-card flat class="table-card">
      <q-table
        :rows="posts"
        :columns="columns"
        row-key="id"
        flat
        dark
        :loading="tableLoading"
        :pagination="{ rowsPerPage: 0 }"
        hide-pagination
        :no-data-label="search ? 'No posts match your search.' : 'No posts yet. Create the first one!'"
        class="blog-table"
      >
        <template #body-cell-title="{ row }">
          <q-td>
            <div class="text-weight-bold text-grey-2">{{ row.title }}</div>
            <div class="text-caption text-grey-6 ellipsis" style="max-width: 320px">{{ row.slug }}</div>
          </q-td>
        </template>

        <template #body-cell-status="{ row }">
          <q-td>
            <q-badge :color="row.status === 'published' ? 'teal-7' : 'grey-7'" rounded>
              {{ row.status === 'published' ? t('admin_blog.status_pub') : t('admin_blog.status_draft') }}
            </q-badge>
          </q-td>
        </template>

        <template #body-cell-publishedAt="{ row }">
          <q-td class="text-grey-4">{{ row.publishedAt ? row.publishedAt.slice(0, 10) : '—' }}</q-td>
        </template>

        <template #body-cell-updatedAt="{ row }">
          <q-td class="text-grey-5">{{ row.updatedAt.slice(0, 10) }}</q-td>
        </template>

        <template #body-cell-actions="{ row }">
          <q-td class="text-right">
            <q-btn flat round dense icon="edit"   size="sm" color="grey-5"   @click="openDialog(row)" />
            <q-btn flat round dense icon="delete" size="sm" color="negative" @click="confirmDelete(row)" />
          </q-td>
        </template>
      </q-table>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="row justify-center q-pa-md">
        <q-pagination
          v-model="page"
          :max="totalPages"
          color="primary"
          active-color="primary"
          active-text-color="white"
          boundary-links
          @update:model-value="loadPosts"
        />
      </div>
    </q-card>

    <!-- ── Post dialog ── -->
    <q-dialog v-model="dialogOpen" persistent maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card class="dialog-card">
        <q-bar class="dialog-bar">
          <q-icon name="article" color="primary" />
          <div class="dialog-bar-title">{{ editingId ? t('admin_blog.dialog_edit') : t('admin_blog.dialog_new') }}</div>
          <q-space />
          <q-btn flat round dense icon="close" color="grey-5" v-close-popup />
        </q-bar>

        <q-scroll-area style="height: calc(100vh - 50px)">
          <q-card-section class="dialog-body">
            <q-form ref="formRef" class="q-gutter-y-lg" @submit.prevent="savePost">

              <!-- 1. Content -->
              <section>
                <div class="form-section-title"><q-icon name="edit_note" size="15px" class="q-mr-xs" />{{ t('admin_blog.sec_content') }}</div>

                <div class="q-gutter-y-md q-mt-sm">
                  <!-- Title -->
                  <q-input
                    v-model="form.title"
                    outlined dark dense
                    :label="t('admin_blog.lbl_title')"
                    :rules="[v => !!v?.trim() || 'Required']"
                    lazy-rules
                    @update:model-value="autoSlug"
                  />

                  <!-- Slug -->
                  <q-input
                    v-model="form.slug"
                    outlined dark dense
                    :label="t('admin_blog.lbl_slug')"
                    hint="a-z, 0-9, hyphens only"
                  />

                  <!-- Status + Published At -->
                  <div class="form-grid">
                    <q-select
                      v-model="form.status"
                      outlined dark dense
                      :label="t('admin_blog.lbl_status')"
                      :options="[
                        { label: t('admin_blog.status_draft'), value: 'draft' },
                        { label: t('admin_blog.status_pub'),   value: 'published' },
                      ]"
                      emit-value map-options
                    />
                    <q-input
                      v-model="form.publishedAt"
                      outlined dark dense
                      :label="t('admin_blog.lbl_published')"
                      type="datetime-local"
                    />
                  </div>

                  <!-- Excerpt -->
                  <q-input
                    v-model="form.excerpt"
                    outlined dark dense
                    :label="t('admin_blog.lbl_excerpt')"
                    type="textarea"
                    autogrow
                    hint="Short description shown in blog listing (max ~300 chars)"
                  />

                  <!-- Rich text content -->
                  <div>
                    <div class="form-label q-mb-xs">{{ t('admin_blog.lbl_content') }}</div>
                    <q-editor
                      v-model="form.content"
                      dark
                      min-height="320px"
                      :toolbar="editorToolbar"
                      class="blog-editor"
                    />
                  </div>
                </div>
              </section>

              <!-- 2. Cover Image -->
              <section>
                <div class="form-section-title"><q-icon name="image" size="15px" class="q-mr-xs" />{{ t('admin_blog.sec_cover') }}</div>
                <div class="row items-start q-gutter-md q-mt-sm">
                  <div v-if="coverPreview || form.coverImage" class="cover-preview">
                    <img :src="coverPreview || form.coverImage" />
                  </div>
                  <div>
                    <q-btn
                      outline color="grey-5" icon="upload" :label="t('admin_blog.cover_upload')"
                      size="sm" @click="triggerCoverInput"
                    />
                    <q-btn
                      v-if="coverPreview || form.coverImage"
                      flat color="negative" icon="delete" :label="t('admin_blog.cover_remove')"
                      size="sm" class="q-ml-sm" @click="removeCover"
                    />
                    <div class="text-caption text-grey-6 q-mt-xs">{{ t('admin_blog.cover_hint') }}</div>
                    <input ref="coverInputRef" type="file" accept="image/*" class="hidden-input" @change="onCoverChange" />
                  </div>
                </div>
              </section>

              <!-- 3. SEO Meta -->
              <section>
                <div class="form-section-title"><q-icon name="search" size="15px" class="q-mr-xs" />{{ t('admin_blog.sec_meta') }}</div>
                <div class="q-gutter-y-md q-mt-sm">
                  <q-input v-model="form.metaTitle"       outlined dark dense :label="t('admin_blog.lbl_meta_title')" hint="Falls back to post title if empty" />
                  <q-input v-model="form.metaDescription" outlined dark dense :label="t('admin_blog.lbl_meta_desc')"  type="textarea" autogrow counter maxlength="300" />
                </div>
              </section>

              <div class="row justify-end q-gutter-sm q-pt-sm">
                <q-btn flat color="grey-5" :label="t('admin_blog.cancel')" v-close-popup />
                <q-btn
                  type="submit" color="primary" unelevated icon-right="check"
                  :loading="saving"
                  :label="editingId ? t('admin_blog.save_update') : t('admin_blog.save_create')"
                />
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
          <div class="text-h6">{{ t('admin_blog.delete_title') }}</div>
          <p class="q-mt-sm text-grey-5"><b>{{ deleteTarget?.title }}</b><br>{{ t('admin_blog.delete_body') }}</p>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat color="grey-5" :label="t('admin_blog.cancel')" v-close-popup />
          <q-btn unelevated color="negative" :label="t('admin_blog.delete_confirm')" :loading="deleting" @click="doDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useQuasar } from 'quasar'

const { t } = useI18n()
const $q    = useQuasar()

// ── Editor toolbar ─────────────────────────────────────────────────────────────
const editorToolbar = [
  ['bold', 'italic', 'underline', 'strike'],
  ['blockquote', 'code'],
  [{ header: [1, 2, 3, false] }],
  ['ordered', 'bullet'],
  ['link'],
  ['undo', 'redo'],
]

// ── Status options ──────────────────────────────────────────────────────────────
const statusOptions = computed(() => [
  { label: t('admin_blog.status_all'),   value: '' },
  { label: t('admin_blog.status_draft'), value: 'draft' },
  { label: t('admin_blog.status_pub'),   value: 'published' },
])

// ── Columns ───────────────────────────────────────────────────────────────────
const columns = [
  { name: 'title',       label: 'Title',     field: 'title',       sortable: false, align: 'left'   },
  { name: 'status',      label: 'Status',    field: 'status',      sortable: false, align: 'left'   },
  { name: 'publishedAt', label: 'Published', field: 'publishedAt', sortable: false, align: 'left'   },
  { name: 'updatedAt',   label: 'Updated',   field: 'updatedAt',   sortable: false, align: 'left'   },
  { name: 'actions',     label: '',          field: 'actions',     sortable: false, align: 'right'  },
]

// ── Data ──────────────────────────────────────────────────────────────────────
const posts        = ref([])
const tableLoading = ref(true)
const search       = ref('')
const statusFilter = ref('')
const page         = ref(1)
const totalPages   = ref(1)

let debounceTimer = null
function debouncedLoad() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => { page.value = 1; loadPosts() }, 300)
}

async function loadPosts() {
  tableLoading.value = true
  try {
    const params = new URLSearchParams({ page: page.value, q: search.value })
    if (statusFilter.value) params.set('status', statusFilter.value)
    const res  = await fetch(`/api/admin/blog?${params}`, { credentials: 'include', headers: { Accept: 'application/json' } })
    const data = await res.json()
    posts.value      = data.posts      ?? []
    totalPages.value = data.totalPages ?? 1
  } catch {
    $q.notify({ type: 'negative', message: t('admin_blog.err_load') })
  } finally {
    tableLoading.value = false
  }
}

// Watch statusFilter
watch(statusFilter, () => { page.value = 1; loadPosts() })

// ── Cover image ───────────────────────────────────────────────────────────────
const coverInputRef = ref(null)
const coverPreview  = ref(null)
const coverFile     = ref(null)

function triggerCoverInput() { coverInputRef.value?.click() }

function onCoverChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    $q.notify({ type: 'warning', message: 'File too large. Max 5MB.' })
    return
  }
  coverFile.value = file
  const reader = new FileReader()
  reader.onload = (ev) => { coverPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function removeCover() {
  coverPreview.value    = null
  coverFile.value       = null
  form.value.coverImage = null
  if (coverInputRef.value) coverInputRef.value.value = ''
}

async function uploadCover(postId) {
  if (!coverFile.value) return
  const fd = new FormData()
  fd.append('cover', coverFile.value)
  const res  = await fetch(`/api/admin/blog/${postId}/cover`, {
    method: 'POST', credentials: 'include', body: fd,
  })
  const data = await res.json().catch(() => ({}))
  if (res.ok) {
    const idx = posts.value.findIndex(p => p.id === postId)
    if (idx !== -1) posts.value[idx].coverImage = data.coverImage
  }
}

// ── Slug auto-gen ─────────────────────────────────────────────────────────────
function slugify(str) {
  return str
    .toLowerCase()
    .replace(/[āčēģīķļņšūž]/g, c => ({ ā:'a',č:'c',ē:'e',ģ:'g',ī:'i',ķ:'k',ļ:'l',ņ:'n',š:'s',ū:'u',ž:'z' })[c] ?? c)
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/[\s-]+/g, '-')
}

function autoSlug() {
  if (!editingId.value) {
    form.value.slug = slugify(form.value.title)
  }
}

// ── Form / Dialog ─────────────────────────────────────────────────────────────
const dialogOpen = ref(false)
const saving     = ref(false)
const editingId  = ref(null)
const formRef    = ref(null)

const EMPTY_FORM = () => ({
  title: '', slug: '', content: '', excerpt: '',
  status: 'draft', publishedAt: '',
  coverImage: null,
  metaTitle: '', metaDescription: '',
})

const form = ref(EMPTY_FORM())

function openDialog(post = null) {
  editingId.value    = post?.id ?? null
  coverPreview.value = null
  coverFile.value    = null
  form.value = post
    ? {
        title:           post.title ?? '',
        slug:            post.slug ?? '',
        content:         post.content ?? '',
        excerpt:         post.excerpt ?? '',
        status:          post.status ?? 'draft',
        publishedAt:     post.publishedAt ? post.publishedAt.slice(0, 16) : '',
        coverImage:      post.coverImage ?? null,
        metaTitle:       post.metaTitle ?? '',
        metaDescription: post.metaDescription ?? '',
      }
    : EMPTY_FORM()
  dialogOpen.value = true
  nextTick(() => formRef.value?.resetValidation())
}

async function savePost() {
  const valid = await formRef.value?.validate()
  if (!valid) return

  saving.value = true
  const payload = {
    title:           form.value.title,
    slug:            form.value.slug || null,
    content:         form.value.content,
    excerpt:         form.value.excerpt || null,
    status:          form.value.status,
    publishedAt:     form.value.publishedAt || null,
    metaTitle:       form.value.metaTitle || null,
    metaDescription: form.value.metaDescription || null,
  }

  try {
    const url    = editingId.value ? `/api/admin/blog/${editingId.value}` : '/api/admin/blog'
    const method = editingId.value ? 'PUT' : 'POST'
    const res    = await fetch(url, {
      method, credentials: 'include',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(payload),
    })
    const data = await res.json()
    if (!res.ok) throw new Error(data.message ?? 'Save failed.')

    const savedId = editingId.value ?? data.post.id

    if (editingId.value) {
      const idx = posts.value.findIndex(p => p.id === editingId.value)
      if (idx !== -1) posts.value[idx] = data.post
    } else {
      posts.value.unshift(data.post)
    }

    if (coverFile.value) await uploadCover(savedId)

    dialogOpen.value = false
    $q.notify({ type: 'positive', message: t('admin_blog.saved') })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message || t('admin_blog.err_save') })
  } finally {
    saving.value = false
  }
}

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteDialog = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)

function confirmDelete(post) { deleteTarget.value = post; deleteDialog.value = true }

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    const res = await fetch(`/api/admin/blog/${deleteTarget.value.id}`, {
      method: 'DELETE', credentials: 'include',
    })
    if (!res.ok && res.status !== 204) throw new Error('Delete failed.')
    posts.value      = posts.value.filter(p => p.id !== deleteTarget.value.id)
    deleteDialog.value = false
    $q.notify({ type: 'positive', message: t('admin_blog.deleted') })
  } catch (e) {
    $q.notify({ type: 'negative', message: e.message || t('admin_blog.err_delete') })
  } finally {
    deleting.value = false
  }
}

loadPosts()
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

.blog-table {
  background: transparent;
  :deep(thead tr th) { font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.38); border-bottom: 1px solid rgba(255,255,255,0.07); }
  :deep(tbody tr:hover td) { background: rgba(255,255,255,0.03); }
}

.dialog-card  { background: #111; color: #f5f5f5; max-width: 860px; width: 100%; margin: 0 auto; }
.dialog-bar   { background: #0d0d0d; border-bottom: 1px solid rgba(255,255,255,0.07); padding: 0 16px; min-height: 50px; }
.dialog-bar-title { font-weight: 700; font-size: 0.95rem; margin-left: 8px; }
.dialog-body  { max-width: 860px; width: 100%; margin: 0 auto; }

.form-section-title {
  display: flex; align-items: center;
  font-size: 0.7rem; letter-spacing: 0.16em; text-transform: uppercase;
  color: rgba(255,255,255,0.4); font-weight: 600; margin-bottom: 4px;
}
.form-label { font-size: 0.75rem; color: rgba(255,255,255,0.45); }

.form-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  @media (max-width: 600px) { grid-template-columns: 1fr; }
}

.blog-editor {
  background: #1a1a1a;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,0.12);
  :deep(.q-editor__content) { color: #f5f5f5; min-height: 320px; }
  :deep(.q-editor__toolbar) { background: #222; border-bottom: 1px solid rgba(255,255,255,0.08); }
}

.cover-preview {
  width: 200px;
  height: 112px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.1);
  background: rgba(255,255,255,0.03);
  img { width: 100%; height: 100%; object-fit: cover; }
}

.hidden-input { display: none; }

.confirm-card {
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.07);
  background: #161616;
  color: #f5f5f5;
  min-width: 340px;
}
</style>
