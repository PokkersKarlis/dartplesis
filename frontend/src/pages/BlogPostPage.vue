<template>
  <q-page class="blog-post-page">

    <!-- Loading -->
    <div v-if="loading" class="container q-py-xl">
      <q-skeleton type="rect" height="320px" class="q-mb-lg" style="border-radius:16px" />
      <q-skeleton type="text" width="60%" height="40px" class="q-mb-md" />
      <q-skeleton type="text" width="100%" />
      <q-skeleton type="text" width="95%" />
      <q-skeleton type="text" width="88%" />
    </div>

    <!-- Not found -->
    <div v-else-if="!post" class="container q-py-xl text-center">
      <q-icon name="article" size="64px" color="grey-7" class="q-mb-md" />
      <div class="text-h5 text-grey-4">Post not found</div>
      <router-link to="/blog" class="back-link q-mt-md">{{ t('blog.back') }}</router-link>
    </div>

    <!-- Post content -->
    <template v-else>
      <!-- Cover image -->
      <div v-if="post.coverImage" class="post-cover-hero">
        <img :src="post.coverImage" :alt="post.title" />
        <div class="cover-gradient" />
      </div>

      <div class="container">
        <!-- Back link -->
        <router-link to="/blog" class="back-link q-mt-lg q-mb-md d-block">{{ t('blog.back') }}</router-link>

        <!-- Article -->
        <article class="post-article">
          <header class="post-header">
            <div class="post-meta text-grey-5">
              <q-icon name="calendar_today" size="13px" class="q-mr-xs" />
              <span>{{ t('blog.published') }}: {{ formatDate(post.publishedAt) }}</span>
            </div>
            <h1 class="post-title">{{ post.title }}</h1>
            <p v-if="post.excerpt" class="post-excerpt">{{ post.excerpt }}</p>
          </header>

          <div class="post-content" v-html="post.content" />
        </article>

        <!-- Share buttons -->
        <div class="share-section">
          <div class="share-label">{{ t('blog.share_title') }}</div>
          <div class="share-buttons">
            <a
              :href="shareFb"
              target="_blank"
              rel="noopener noreferrer"
              class="share-btn share-btn--fb"
              :title="t('blog.share_fb')"
              @click.stop
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
              {{ t('blog.share_fb') }}
            </a>
            <a
              :href="shareX"
              target="_blank"
              rel="noopener noreferrer"
              class="share-btn share-btn--x"
              :title="t('blog.share_x')"
              @click.stop
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
              {{ t('blog.share_x') }}
            </a>
            <a
              :href="shareWa"
              target="_blank"
              rel="noopener noreferrer"
              class="share-btn share-btn--wa"
              :title="t('blog.share_wa')"
              @click.stop
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
              </svg>
              {{ t('blog.share_wa') }}
            </a>
            <button
              class="share-btn share-btn--copy"
              :class="{ copied }"
              :title="t('blog.share_copy')"
              @click="copyLink"
            >
              <q-icon :name="copied ? 'check' : 'link'" size="16px" />
              {{ copied ? t('blog.share_copied') : t('blog.share_copy') }}
            </button>
          </div>
        </div>

        <!-- Back link bottom -->
        <div class="q-py-lg">
          <router-link to="/blog" class="back-link">{{ t('blog.back') }}</router-link>
        </div>
      </div>
    </template>

  </q-page>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useMeta } from 'quasar'

const route    = useRoute()
const { t, locale } = useI18n()
const post    = ref(null)
const loading = ref(true)
const copied  = ref(false)

// ── Share URLs ────────────────────────────────────────────────────────────────
const pageUrl = computed(() => {
  if (typeof window === 'undefined') return ''
  return window.location.href
})

const shareFb = computed(() =>
  `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(pageUrl.value)}`,
)
const shareX = computed(() =>
  `https://twitter.com/intent/tweet?url=${encodeURIComponent(pageUrl.value)}&text=${encodeURIComponent(post.value?.title ?? '')}`,
)
const shareWa = computed(() =>
  `https://wa.me/?text=${encodeURIComponent((post.value?.title ?? '') + ' ' + pageUrl.value)}`,
)

async function copyLink() {
  try {
    await navigator.clipboard.writeText(pageUrl.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2500)
  } catch { /* clipboard not available */ }
}

useMeta(() => ({
  title: post.value
    ? `${post.value.metaTitle ?? post.value.title} — DK Dārtplēsis`
    : 'Blog — DK Dārtplēsis',
  meta: {
    description: {
      name: 'description',
      content: post.value?.metaDescription ?? post.value?.excerpt ?? '',
    },
    ogTitle: {
      property: 'og:title',
      content:  post.value ? `${post.value.metaTitle ?? post.value.title} — DK Dārtplēsis` : 'Blog',
    },
    ogDescription: {
      property: 'og:description',
      content:  post.value?.metaDescription ?? post.value?.excerpt ?? '',
    },
    ogImage: {
      property: 'og:image',
      content:  post.value?.coverImage ?? '',
    },
    ogType: { property: 'og:type', content: 'article' },
  },
}))

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString(locale.value === 'lv' ? 'lv-LV' : 'en-GB', {
    year: 'numeric', month: 'long', day: 'numeric',
  })
}

async function loadPost() {
  loading.value = true
  try {
    const res  = await fetch(`/api/blog/${route.params.slug}`, { headers: { Accept: 'application/json' } })
    if (!res.ok) { post.value = null; return }
    const data = await res.json()
    post.value = data.post ?? null
  } catch {
    post.value = null
  } finally {
    loading.value = false
  }
}

loadPost()
</script>

<style scoped lang="scss">
.blog-post-page {
  background: #0d0d0d;
  min-height: 100%;
  color: #f5f5f5;
}

.container {
  max-width: 760px;
  margin: 0 auto;
  padding: 0 24px;
}

.post-cover-hero {
  position: relative;
  width: 100%;
  max-height: 420px;
  overflow: hidden;

  img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    display: block;
  }

  .cover-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 40%, #0d0d0d 100%);
  }
}

.back-link {
  display: inline-flex;
  align-items: center;
  font-size: 0.85rem;
  color: #4ecdc4;
  text-decoration: none;
  font-weight: 600;
  letter-spacing: 0.02em;
  margin: 24px 0 16px;

  &:hover { opacity: 0.8; }
}

.post-article {
  padding-bottom: 0;
}

.post-header {
  margin-bottom: 40px;
}

.post-meta {
  font-size: 0.78rem;
  letter-spacing: 0.04em;
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.post-title {
  font-size: clamp(1.8rem, 4vw, 2.8rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.15;
  color: #f5f5f5;
  margin: 0 0 16px;
}

.post-excerpt {
  font-size: 1.05rem;
  color: rgba(255, 255, 255, 0.55);
  line-height: 1.6;
  margin: 0;
  border-left: 3px solid #4ecdc4;
  padding-left: 16px;
}

// Rich text content styles
.post-content {
  font-size: 1rem;
  line-height: 1.75;
  color: rgba(255, 255, 255, 0.82);

  :deep(h1), :deep(h2), :deep(h3) {
    color: #f5f5f5;
    font-weight: 800;
    letter-spacing: -0.025em;
    line-height: 1.2;
    margin-top: 2em;
    margin-bottom: 0.6em;
  }

  :deep(h1) { font-size: 1.8rem; }
  :deep(h2) { font-size: 1.4rem; }
  :deep(h3) { font-size: 1.15rem; }

  :deep(p) {
    margin: 0 0 1.2em;
  }

  :deep(a) {
    color: #4ecdc4;
    text-decoration: underline;
    &:hover { opacity: 0.8; }
  }

  :deep(blockquote) {
    border-left: 4px solid #4ecdc4;
    margin: 1.5em 0;
    padding: 8px 20px;
    background: rgba(78, 205, 196, 0.06);
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: rgba(255, 255, 255, 0.7);
  }

  :deep(code) {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 0.88em;
    font-family: monospace;
    color: #4ecdc4;
  }

  :deep(pre) {
    background: #1a1a1a;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 16px;
    overflow-x: auto;
    margin: 1.5em 0;

    code {
      background: none;
      padding: 0;
      border-radius: 0;
    }
  }

  :deep(ul), :deep(ol) {
    padding-left: 1.5em;
    margin: 0 0 1.2em;

    li { margin-bottom: 0.4em; }
  }

  :deep(img) {
    max-width: 100%;
    border-radius: 12px;
    margin: 1.5em 0;
  }

  :deep(hr) {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin: 2em 0;
  }

  :deep(strong) { color: #f5f5f5; font-weight: 700; }
}

// ── Share section ──────────────────────────────────────────────────────────────
.share-section {
  margin-top: 48px;
  padding-top: 32px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.share-label {
  font-size: 0.7rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.38);
  font-weight: 600;
  margin-bottom: 14px;
}

.share-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.share-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 16px;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-decoration: none;
  cursor: pointer;
  border: 1px solid transparent;
  transition: opacity 0.18s, transform 0.18s;
  line-height: 1;

  &:hover { opacity: 0.85; transform: translateY(-1px); }

  &--fb {
    background: #1877f2;
    color: #fff;
  }

  &--x {
    background: #000;
    color: #fff;
    border-color: rgba(255, 255, 255, 0.15);
  }

  &--wa {
    background: #25d366;
    color: #fff;
  }

  &--copy {
    background: rgba(255, 255, 255, 0.07);
    color: rgba(255, 255, 255, 0.75);
    border-color: rgba(255, 255, 255, 0.12);

    &.copied {
      background: rgba(78, 205, 196, 0.12);
      color: #4ecdc4;
      border-color: rgba(78, 205, 196, 0.25);
    }
  }
}
</style>
