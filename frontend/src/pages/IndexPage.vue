<template>
  <q-page class="dk-page">
    <HeroSection />

    <div class="teal-divider" aria-hidden="true" />

    <section class="dk-section teasers reveal">
      <div class="dk-eyebrow">{{ t('home.eyebrow') }}</div>
      <h2 class="dk-title">
        {{ t('home.title') }} <span class="text-teal">DK Dārtplēsis</span>
      </h2>

      <div class="teaser-grid">
        <router-link
          v-for="(teaser, i) in teasers"
          :key="i"
          :to="teaser.to"
          class="teaser-card"
        >
          <span class="teaser-card__icon" aria-hidden="true">
            <q-icon :name="teaser.icon" size="18px" />
          </span>
          <div class="teaser-card__title">{{ teaser.title }}</div>
          <p class="teaser-card__text">{{ teaser.text }}</p>
          <span class="teaser-card__arrow">→</span>
        </router-link>
      </div>
    </section>

    <!-- Supporters strip -->
    <section class="supporters-section dk-section reveal">
      <SupportersBar />
    </section>

    <!-- Latest blog posts -->
    <section v-if="latestPosts.length > 0" class="blog-latest-section dk-section blog-fade-in">
      <div class="section-head row items-end justify-between q-mb-xl">
        <div>
          <div class="dk-eyebrow">{{ t('blog.latest_eyebrow') }}</div>
          <h2 class="dk-title">
            {{ t('blog.latest_title') }} <span class="text-teal">{{ t('blog.latest_accent') }}</span>
          </h2>
        </div>
        <router-link to="/blog" class="view-all-link">{{ t('blog.view_all') }} →</router-link>
      </div>

      <div class="blog-grid">
        <router-link
          v-for="post in latestPosts"
          :key="post.id"
          :to="`/blog/${post.slug}`"
          class="blog-card"
        >
          <div class="blog-card__cover">
            <img v-if="post.coverImage" :src="post.coverImage" :alt="post.title" loading="lazy" />
            <div v-else class="blog-card__cover-placeholder">
              <q-icon name="article" size="40px" color="grey-7" />
            </div>
          </div>
          <div class="blog-card__body">
            <div class="blog-card__date">
              <q-icon name="calendar_today" size="11px" class="q-mr-xs" />
              {{ formatPostDate(post.publishedAt) }}
            </div>
            <div class="blog-card__title">{{ post.title }}</div>
            <p v-if="post.excerpt" class="blog-card__excerpt">{{ post.excerpt }}</p>
            <span class="blog-card__cta">{{ t('blog.read_more') }} →</span>
          </div>
        </router-link>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import HeroSection from 'src/components/sections/HeroSection.vue'
import SupportersBar from 'src/components/SupportersBar.vue'
import { useScrollReveal } from 'src/composables/useScrollReveal'

const { t, tm, locale } = useI18n()
useScrollReveal()

const teaserRoutes = ['/par-mums', '/komanda', '/notikumi', '/kontakti']
const teaserIcons  = ['info', 'groups', 'emoji_events', 'mail']

const teasers = computed(() =>
  tm('home.teasers').map((item, i) => ({ ...item, to: teaserRoutes[i], icon: teaserIcons[i] })),
)

const latestPosts = ref([])

function formatPostDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString(locale.value === 'lv' ? 'lv-LV' : 'en-GB', {
    year: 'numeric', month: 'short', day: 'numeric',
  })
}

onMounted(async () => {
  try {
    const res  = await fetch('/api/blog?page=1', { headers: { Accept: 'application/json' } })
    const data = await res.json()
    latestPosts.value = (data.posts ?? []).slice(0, 3)
  } catch { /* silent — section simply stays hidden */ }
})
</script>

<style scoped lang="scss">
.dk-page {
  background: #0a0a0a;
  min-height: 100vh;
}

.text-teal { color: #4ecdc4; }

.teaser-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5px;
  margin-top: 3rem;
  background: rgba(78, 205, 196, 0.15);
}

.teaser-card {
  background: #161616;
  padding: 2.5rem 2rem;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  position: relative;
  overflow: hidden;
  transition: background 0.3s;

  &::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: #4ecdc4;
    transform: scaleX(0);
    transition: transform 0.3s;
    transform-origin: left;
  }

  &:hover {
    background: #1c1c1c;
    .teaser-card__arrow { transform: translateX(4px); color: #4ecdc4; }
    &::after { transform: scaleX(1); }
  }

  &__icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(78, 205, 196, 0.08);
    border: 1px solid rgba(78, 205, 196, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4ecdc4;
    flex-shrink: 0;
  }

  &__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.6rem;
    letter-spacing: 0.04em;
    color: #f5f5f5;
    line-height: 1;
  }

  &__text {
    font-size: 0.88rem;
    line-height: 1.65;
    color: #888;
    flex: 1;
    margin: 0;
  }

  &__arrow {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.1rem;
    color: #555;
    transition: transform 0.25s, color 0.25s;
    align-self: flex-start;
  }
}

@media (max-width: 900px) { .teaser-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .teaser-grid { grid-template-columns: 1fr; } }

.supporters-section {
  border-top: 1px solid rgba(78, 205, 196, 0.08);
  background: rgba(8, 8, 8, 0.6);
  padding-top: 2.5rem;
  padding-bottom: 2.5rem;
}

// ── Latest blog posts ────────────────────────────────────────────────────────
@keyframes blogFadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

.blog-latest-section {
  border-top: 1px solid rgba(78, 205, 196, 0.08);
}

.blog-fade-in {
  animation: blogFadeUp 0.7s ease both;
}

.section-head {
  flex-wrap: wrap;
  gap: 12px;
}

.view-all-link {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #4ecdc4;
  text-decoration: none;
  transition: opacity 0.2s;
  white-space: nowrap;
  padding-bottom: 4px;
  border-bottom: 1px solid rgba(78, 205, 196, 0.3);

  &:hover { opacity: 0.75; }
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: rgba(78, 205, 196, 0.1);

  @media (max-width: 900px) { grid-template-columns: repeat(2, 1fr); }
  @media (max-width: 560px) { grid-template-columns: 1fr; }
}

.blog-card {
  background: #161616;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  color: inherit;
  overflow: hidden;
  transition: background 0.25s;

  &:hover {
    background: #1c1c1c;

    .blog-card__cover img { transform: scale(1.04); }
    .blog-card__cta { color: #4ecdc4; }
  }

  &__cover {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #111;
    flex-shrink: 0;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.35s ease;
    }
  }

  &__cover-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  &__body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
  }

  &__date {
    font-size: 0.7rem;
    letter-spacing: 0.06em;
    color: rgba(255, 255, 255, 0.38);
    display: flex;
    align-items: center;
  }

  &__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.35rem;
    letter-spacing: 0.04em;
    color: #f5f5f5;
    line-height: 1.15;
  }

  &__excerpt {
    font-size: 0.84rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.48);
    margin: 0;
    flex: 1;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__cta {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(78, 205, 196, 0.6);
    transition: color 0.2s;
    margin-top: 4px;
  }
}
</style>
