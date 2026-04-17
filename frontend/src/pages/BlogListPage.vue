<template>
  <q-page class="blog-list-page">

    <!-- Hero -->
    <section class="blog-hero q-py-xl">
      <div class="container text-center">
        <div class="eyebrow">{{ t('blog.eyebrow') }}</div>
        <h1 class="page-title q-mb-sm">
          {{ t('blog.title') }} <span class="accent">{{ t('blog.title_accent') }}</span>
        </h1>
        <p class="page-subtitle">{{ t('blog.subtitle') }}</p>
      </div>
    </section>

    <!-- Content -->
    <section class="blog-content q-py-lg">
      <div class="container">

        <!-- Loading -->
        <div v-if="loading" class="row q-col-gutter-lg">
          <div v-for="n in 6" :key="n" class="col-12 col-sm-6 col-md-4">
            <q-skeleton type="rect" class="card-skeleton" />
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center q-py-xl text-negative">
          {{ t('blog.load_error') }}
        </div>

        <!-- Empty -->
        <div v-else-if="posts.length === 0" class="text-center q-py-xl text-grey-5">
          {{ t('blog.no_posts') }}
        </div>

        <!-- Grid -->
        <div v-else class="row q-col-gutter-lg">
          <div
            v-for="post in posts"
            :key="post.id"
            class="col-12 col-sm-6 col-md-4"
          >
            <router-link :to="`/blog/${post.slug}`" class="post-card-link">
              <article class="post-card">
                <div class="post-cover">
                  <img v-if="post.coverImage" :src="post.coverImage" :alt="post.title" loading="lazy" />
                  <div v-else class="cover-placeholder">
                    <q-icon name="article" size="48px" color="grey-7" />
                  </div>
                </div>
                <div class="post-body">
                  <div class="post-meta text-grey-5">
                    <q-icon name="calendar_today" size="12px" class="q-mr-xs" />
                    {{ formatDate(post.publishedAt) }}
                  </div>
                  <h2 class="post-title">{{ post.title }}</h2>
                  <p v-if="post.excerpt" class="post-excerpt">{{ post.excerpt }}</p>
                  <span class="read-more">{{ t('blog.read_more') }} →</span>
                </div>
              </article>
            </router-link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="row justify-center q-mt-xl">
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

      </div>
    </section>

  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useMeta } from 'quasar'

const { t, locale } = useI18n()

const posts      = ref([])
const loading    = ref(true)
const error      = ref(false)
const page       = ref(1)
const totalPages = ref(1)

useMeta(() => ({
  title: `${t('blog.title')} ${t('blog.title_accent')} — DK Dārtplēsis`,
  meta: {
    description: { name: 'description', content: t('blog.subtitle') },
    ogTitle:     { property: 'og:title', content: `Blog — DK Dārtplēsis` },
    ogType:      { property: 'og:type',  content: 'website' },
  },
}))

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString(locale.value === 'lv' ? 'lv-LV' : 'en-GB', {
    year: 'numeric', month: 'short', day: 'numeric',
  })
}

async function loadPosts() {
  loading.value = true
  error.value   = false
  try {
    const res  = await fetch(`/api/blog?page=${page.value}`, { headers: { Accept: 'application/json' } })
    const data = await res.json()
    posts.value      = data.posts      ?? []
    totalPages.value = data.totalPages ?? 1
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

loadPosts()
</script>

<style scoped lang="scss">
.blog-list-page {
  background: #0d0d0d;
  min-height: 100%;
  color: #f5f5f5;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
}

.blog-hero {
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  padding-top: 64px;
  padding-bottom: 48px;
}

.eyebrow {
  font-size: 0.7rem;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #4ecdc4;
  font-weight: 600;
  margin-bottom: 12px;
}

.page-title {
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.1;
  margin: 0;
  color: #f5f5f5;
  .accent { color: #4ecdc4; }
}

.page-subtitle {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 12px;
  max-width: 520px;
  margin-left: auto;
  margin-right: auto;
}

.card-skeleton {
  height: 320px;
  border-radius: 16px;
}

.post-card-link {
  display: block;
  text-decoration: none;
  color: inherit;
}

.post-card {
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  overflow: hidden;
  transition: transform 0.2s, border-color 0.2s;
  height: 100%;
  display: flex;
  flex-direction: column;

  &:hover {
    transform: translateY(-3px);
    border-color: rgba(78, 205, 196, 0.3);
  }
}

.post-cover {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #1a1a1a;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
  }

  .post-card:hover & img {
    transform: scale(1.04);
  }
}

.cover-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.post-body {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.post-meta {
  font-size: 0.72rem;
  letter-spacing: 0.04em;
  display: flex;
  align-items: center;
}

.post-title {
  font-size: 1.1rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1.3;
  color: #f5f5f5;
  margin: 0;
}

.post-excerpt {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.5);
  line-height: 1.55;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

.read-more {
  font-size: 0.8rem;
  color: #4ecdc4;
  font-weight: 600;
  letter-spacing: 0.03em;
  margin-top: auto;
}
</style>
