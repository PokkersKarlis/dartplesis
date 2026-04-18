<template>
  <section class="dk-section reveal" id="komanda">
    <div class="dk-eyebrow">{{ t('team.eyebrow') }}</div>
    <h2 class="dk-title">
      {{ t('team.title') }} <span class="text-teal">{{ t('team.title_accent') }}</span>
    </h2>

    <!-- Filter bar — only visible once loaded with players -->
    <div v-if="!loading && !errorMsg && players.length > 0" class="filter-bar q-mt-lg">
      <button
        v-for="f in filters"
        :key="f.key"
        class="filter-btn"
        :class="{ 'filter-btn--active': activeFilter === f.key }"
        @click="activeFilter = f.key"
      >
        {{ f.label }}
        <span class="filter-count">{{ f.count }}</span>
      </button>
    </div>

    <!-- Loading skeletons -->
    <div v-if="loading" class="team-grid">
      <div v-for="n in 4" :key="n" class="player-card player-card--skeleton">
        <div class="skeleton-avatar" />
        <div class="skeleton-line skeleton-line--name" />
        <div class="skeleton-line skeleton-line--role" />
      </div>
    </div>

    <q-banner v-else-if="errorMsg" class="team-err q-mt-lg" rounded dense>
      {{ errorMsg }}
    </q-banner>

    <div v-else-if="players.length === 0" class="team-empty q-mt-xl">
      <div class="team-empty__title">{{ t('team.empty_title') }}</div>
      <div class="team-empty__sub">{{ t('team.empty_sub') }}</div>
    </div>

    <!-- Grid -->
    <div v-else class="team-grid q-mt-lg">
      <button
        v-for="player in filteredPlayers"
        :key="player.id"
        class="player-card"
        :class="{ 'player-card--junior': player.isJunior }"
        @click="openProfile(player)"
      >
        <div class="player-card__body">
          <!-- Photo or initials -->
          <div class="player-avatar" :class="{ 'player-avatar--photo': player.photo }">
            <img v-if="player.photo" :src="photoUrl(player.photo)" :alt="player.name + ' ' + player.surname" />
            <span v-else>{{ initials(player) }}</span>
          </div>

          <div class="player-name">
            {{ player.name }} {{ player.surname }}
            <span v-if="player.nickname" class="player-nickname">"{{ player.nickname }}"</span>
          </div>

          <!-- Chips: roles + junior -->
          <div class="player-roles">
            <span
              v-for="role in player.clubRoles.slice(0, 2)"
              :key="role"
              class="role-chip"
              :class="`role-chip--${role}`"
            >{{ roleLabelI18n(role) }}</span>
            <span v-if="player.clubRoles.length > 2" class="role-chip role-chip--more">
              +{{ player.clubRoles.length - 2 }}
            </span>
            <span v-if="player.isJunior" class="role-chip role-chip--junior">
              {{ t('team.junior') }}
            </span>
          </div>
        </div>
      </button>

      <!-- Join CTA card -->
      <router-link to="/pievienojies" class="player-card player-card--join">
        <div class="player-card__body player-card__body--center">
          <div class="join-plus">+</div>
          <div class="join-label">{{ t('team.join_card') }}</div>
        </div>
      </router-link>
    </div>

    <!-- Profile dialog -->
    <q-dialog v-model="dialogOpen" maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card v-if="selected" class="profile-dialog">
        <!-- Header -->
        <div class="profile-header">
          <div class="profile-photo-wrap">
            <img v-if="selected.photo" :src="photoUrl(selected.photo)" class="profile-photo" :alt="selected.name" />
            <div v-else class="profile-initials">{{ initials(selected) }}</div>
          </div>
          <div class="profile-meta">
            <div class="profile-name">{{ selected.name }} {{ selected.surname }}</div>
            <div v-if="selected.nickname" class="profile-nickname">"{{ selected.nickname }}"</div>
            <div class="profile-tags">
              <span v-if="selected.isJunior" class="role-chip role-chip--junior">{{ t('team.junior') }}</span>
              <span
                v-for="role in selected.clubRoles"
                :key="role"
                class="role-chip"
                :class="`role-chip--${role}`"
              >{{ roleLabelI18n(role) }}</span>
            </div>
          </div>
          <q-btn flat round icon="close" class="profile-close" @click="dialogOpen = false" />
        </div>

        <!-- Stats bar -->
        <div
          v-if="selected.dartWeight || selected.dartModel || selected.favoriteDouble || selected.highestCheckout"
          class="stats-bar"
        >
          <div v-if="selected.dartWeight" class="stat-item">
            <div class="stat-label">{{ t('team.stat_weight') }}</div>
            <div class="stat-val">{{ selected.dartWeight }}</div>
          </div>
          <div v-if="selected.dartModel" class="stat-item">
            <div class="stat-label">{{ t('team.stat_model') }}</div>
            <div class="stat-val">{{ selected.dartModel }}</div>
          </div>
          <div v-if="selected.favoriteDouble" class="stat-item">
            <div class="stat-label">{{ t('team.stat_double') }}</div>
            <div class="stat-val teal">{{ selected.favoriteDouble }}</div>
          </div>
          <div v-if="selected.highestCheckout" class="stat-item">
            <div class="stat-label">{{ t('team.stat_checkout') }}</div>
            <div class="stat-val teal">{{ selected.highestCheckout }}</div>
          </div>
        </div>

        <!-- Bio sections -->
        <div class="profile-body">
          <div v-if="selected.careerHighlight" class="bio-block">
            <div class="bio-label">{{ t('team.bio_highlight') }}</div>
            <p class="bio-text">{{ selected.careerHighlight }}</p>
          </div>
          <div v-if="selected.preGameRitual" class="bio-block">
            <div class="bio-label">{{ t('team.bio_ritual') }}</div>
            <p class="bio-text">{{ selected.preGameRitual }}</p>
          </div>
          <div class="bio-grid">
            <div v-if="selected.dartsIdol" class="bio-inline">
              <span class="bio-label">{{ t('team.bio_idol') }}</span>
              <span class="bio-text">{{ selected.dartsIdol }}</span>
            </div>
            <div v-if="selected.hobbies" class="bio-inline">
              <span class="bio-label">{{ t('team.bio_hobbies') }}</span>
              <span class="bio-text">{{ selected.hobbies }}</span>
            </div>
            <div v-if="selected.walkOutSong" class="bio-inline">
              <span class="bio-label">{{ t('team.bio_walkout') }}</span>
              <span class="bio-text">{{ selected.walkOutSong }}</span>
            </div>
            <div v-if="selected.gripStyle" class="bio-inline">
              <span class="bio-label">{{ t('team.bio_grip') }}</span>
              <span class="bio-text">{{ selected.gripStyle }}</span>
            </div>
          </div>
          <div v-if="selected.lifeMotto" class="bio-motto">
            "{{ selected.lifeMotto }}"
          </div>
        </div>
      </q-card>
    </q-dialog>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, te } = useI18n()

const players    = ref([])
const loading    = ref(true)
const errorMsg   = ref('')
const dialogOpen = ref(false)
const selected   = ref(null)
const activeFilter = ref('all')

// ── Role labels fallback ──────────────────────────────────────────────────────

const ROLE_LABELS = {
  player:           'Player',
  master_player:    'Master Player',
  trainer:          'Trainer',
  master_yoda:      'Master Yoda',
  club_manager:     'Club Manager',
  club_captain:     'Club Captain',
  vice_captain:     'Vice Captain',
  committee_member: 'Committee',
  social_media:     'Social Media',
}

function roleLabelI18n(role) {
  const key = 'team.roles.' + role
  return te(key) ? t(key) : (ROLE_LABELS[role] ?? role)
}

// ── Filter + sort ─────────────────────────────────────────────────────────────

const seniorCount = computed(() => players.value.filter(p => !p.isJunior).length)
const juniorCount = computed(() => players.value.filter(p => p.isJunior).length)

const filters = computed(() => [
  { key: 'all',    label: t('team.filter_all'),     count: players.value.length },
  { key: 'senior', label: t('team.filter_seniors'), count: seniorCount.value },
  { key: 'junior', label: t('team.filter_juniors'), count: juniorCount.value },
])

const filteredPlayers = computed(() => {
  const list = players.value
  if (activeFilter.value === 'senior') return list.filter(p => !p.isJunior)
  if (activeFilter.value === 'junior') return list.filter(p => p.isJunior)
  // default: seniors first, juniors last
  return [...list].sort((a, b) => {
    if (a.isJunior === b.isJunior) return 0
    return a.isJunior ? 1 : -1
  })
})

// ── Helpers ───────────────────────────────────────────────────────────────────

function initials(player) {
  return ((player.name?.[0] ?? '') + (player.surname?.[0] ?? '')).toUpperCase()
}

function photoUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  return path.startsWith('/') ? path : `/${path}`
}

function openProfile(player) {
  selected.value = player
  dialogOpen.value = true
}

// ── Load ──────────────────────────────────────────────────────────────────────

onMounted(async () => {
  try {
    const res = await fetch('/api/players', { headers: { Accept: 'application/json' } })
    const raw = await res.text()
    if (!res.ok) {
      errorMsg.value = t('team.load_error')
      return
    }
    const data = JSON.parse(raw)
    players.value = data.players ?? []
  } catch {
    errorMsg.value = t('team.load_error')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped lang="scss">
.text-teal { color: #4ecdc4; }

// ── Filter bar ────────────────────────────────────────────────────────────────

.filter-bar {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: inherit;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 6px 14px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  background: rgba(17, 17, 17, 0.7);
  color: rgba(255, 255, 255, 0.55);
  cursor: pointer;
  border-radius: 2px;
  transition: border-color 0.2s, color 0.2s, background 0.2s;
  outline: none;

  &:hover {
    border-color: rgba(78, 205, 196, 0.45);
    color: #f5f5f5;
  }

  &--active {
    border-color: #4ecdc4;
    color: #4ecdc4;
    background: rgba(78, 205, 196, 0.07);
  }
}

.filter-count {
  font-size: 0.62rem;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.35);
  letter-spacing: 0;

  .filter-btn--active & { color: rgba(78, 205, 196, 0.6); }
}

// ── Grid ──────────────────────────────────────────────────────────────────────

.team-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2.5rem;
}

.team-err {
  background: rgba(231, 76, 60, 0.10);
  border: 1px solid rgba(231, 76, 60, 0.30);
  color: rgba(245, 245, 245, 0.92);
}

.team-empty {
  border: 1px dashed rgba(78, 205, 196, 0.22);
  background: rgba(17, 17, 17, 0.55);
  padding: 2rem 1.5rem;
}

.team-empty__title {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: #f5f5f5;
}

.team-empty__sub {
  margin-top: 0.5rem;
  color: rgba(255,255,255,0.65);
  line-height: 1.6;
  font-size: 0.92rem;
}

// ── Player card ───────────────────────────────────────────────────────────────

.player-card {
  background: #161616;
  border: 1px solid rgba(78, 205, 196, 0.15);
  border-radius: 0;
  transition: border-color 0.3s, transform 0.3s;
  cursor: pointer;
  text-align: left;
  padding: 0;
  outline: none;
  width: 100%;
  font-family: inherit;

  &:hover, &:focus-visible {
    border-color: #4ecdc4;
    transform: translateY(-4px);
  }

  &--junior {
    border-color: rgba(52, 152, 219, 0.22);

    &:hover, &:focus-visible {
      border-color: #3498db;
    }
  }

  &--join {
    border-style: dashed;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    background: #161616;

    &:hover {
      border-color: #4ecdc4;
      background: #1c1c1c;
      transform: translateY(-4px);
    }
  }

  &--skeleton {
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 12px;
    animation: pulse 1.5s ease-in-out infinite;
  }

  &__body {
    padding: 1.75rem 1.5rem;
  }

  &__body--center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
  }
}

// ── Skeleton ──────────────────────────────────────────────────────────────────

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.4; }
}

.skeleton-avatar {
  width: 192px; height: 192px;
  background: rgba(78, 205, 196, 0.08);
  clip-path: polygon(16px 0%, 100% 0%, calc(100% - 16px) 100%, 0% 100%);
}

.skeleton-line {
  background: rgba(255,255,255,0.06);
  border-radius: 3px;
  height: 14px;

  &--name { width: 70%; }
  &--role { width: 45%; }
}

// ── Avatar ────────────────────────────────────────────────────────────────────

.player-avatar {
  width: 192px;
  height: 192px;
  background: #111;
  border: 2px solid #2a8f89;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 4rem;
  color: #4ecdc4;
  margin-bottom: 1rem;
  clip-path: polygon(16px 0%, 100% 0%, calc(100% - 16px) 100%, 0% 100%);
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.player-card--junior .player-avatar {
  border-color: #2a6489;
  color: #3498db;
}

// ── Player info ───────────────────────────────────────────────────────────────

.player-name {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #f5f5f5;
  margin-bottom: 0.25rem;
  line-height: 1.3;
}

.player-nickname {
  display: block;
  font-size: 0.8rem;
  font-weight: 400;
  color: #4ecdc4;
  letter-spacing: 0.04em;
}

.player-roles {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 8px;
  min-height: 22px;
}

// ── Role chips ────────────────────────────────────────────────────────────────

.role-chip {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 3px 7px;
  border-radius: 2px;
  background: rgba(78, 205, 196, 0.10);
  color: #4ecdc4;
  border: 1px solid rgba(78, 205, 196, 0.25);
  white-space: nowrap;
  line-height: 1.4;

  &--club_captain, &--vice_captain {
    background: rgba(231, 76, 60, 0.10);
    color: #e74c3c;
    border-color: rgba(231, 76, 60, 0.25);
  }

  &--trainer, &--master_yoda {
    background: rgba(241, 196, 15, 0.10);
    color: #f1c40f;
    border-color: rgba(241, 196, 15, 0.25);
  }

  &--club_manager, &--committee_member {
    background: rgba(155, 89, 182, 0.10);
    color: #9b59b6;
    border-color: rgba(155, 89, 182, 0.25);
  }

  &--master_player {
    background: rgba(230, 126, 34, 0.10);
    color: #e67e22;
    border-color: rgba(230, 126, 34, 0.25);
  }

  &--social_media {
    background: rgba(26, 188, 156, 0.10);
    color: #1abc9c;
    border-color: rgba(26, 188, 156, 0.25);
  }

  &--more {
    background: rgba(255,255,255,0.05);
    color: #666;
    border-color: rgba(255,255,255,0.10);
  }

  &--junior {
    background: rgba(52, 152, 219, 0.10);
    color: #3498db;
    border-color: rgba(52, 152, 219, 0.25);
  }
}

// ── Join card ─────────────────────────────────────────────────────────────────

.join-plus {
  font-size: 2rem;
  color: #4ecdc4;
  margin-bottom: 0.5rem;
  line-height: 1;
}

.join-label {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 0.8rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #888;
}

// ── Profile dialog ────────────────────────────────────────────────────────────

.profile-dialog {
  background: #0e0e0e;
  color: #f5f5f5;
  max-width: 640px;
  margin: 0 auto;
  overflow-y: auto;
  border-left: 1px solid rgba(78, 205, 196, 0.12);
  border-right: 1px solid rgba(78, 205, 196, 0.12);
}

.profile-header {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding: 2rem 2rem 1.5rem;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  position: relative;
}

.profile-photo-wrap {
  flex-shrink: 0;
}

.profile-photo {
  width: 80px;
  height: 80px;
  object-fit: cover;
  clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
  border: 2px solid #2a8f89;
}

.profile-initials {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 2rem;
  color: #4ecdc4;
  background: #111;
  border: 2px solid #2a8f89;
  clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
}

.profile-meta {
  flex: 1;
  min-width: 0;
}

.profile-name {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.6rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #f5f5f5;
  line-height: 1.1;
}

.profile-nickname {
  font-size: 0.9rem;
  color: #4ecdc4;
  margin-top: 2px;
}

.profile-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 10px;
}

.profile-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  color: #666;

  &:hover { color: #f5f5f5; }
}

// ── Stats bar ─────────────────────────────────────────────────────────────────

.stats-bar {
  display: flex;
  gap: 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  overflow-x: auto;
  scrollbar-width: none;
  &::-webkit-scrollbar { display: none; }
}

.stat-item {
  flex: 1;
  min-width: 0;
  padding: 1rem 1.25rem;
  border-right: 1px solid rgba(255,255,255,0.07);

  &:last-child { border-right: none; }
}

.stat-label {
  font-size: 0.62rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #555;
  margin-bottom: 3px;
  white-space: nowrap;
}

.stat-val {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: #e0e0e0;
  word-break: break-word;

  &.teal { color: #4ecdc4; }
}

// ── Bio ───────────────────────────────────────────────────────────────────────

.profile-body {
  padding: 1.5rem 2rem 3rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.bio-block {
  .bio-label {
    font-size: 0.65rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #4ecdc4;
    margin-bottom: 6px;
  }

  .bio-text {
    font-size: 0.92rem;
    color: rgba(255,255,255,0.75);
    line-height: 1.65;
    margin: 0;
  }
}

.bio-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;

  @media (max-width: 500px) { grid-template-columns: 1fr; }
}

.bio-inline {
  display: flex;
  flex-direction: column;
  gap: 3px;

  .bio-label {
    font-size: 0.62rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #555;
  }

  .bio-text {
    font-size: 0.88rem;
    color: rgba(255,255,255,0.7);
  }
}

.bio-motto {
  font-family: 'Barlow Condensed', sans-serif;
  font-size: 1.1rem;
  font-style: italic;
  color: rgba(78, 205, 196, 0.7);
  border-left: 2px solid #4ecdc4;
  padding-left: 1rem;
  line-height: 1.5;
}

// ── Responsive ────────────────────────────────────────────────────────────────

@media (max-width: 860px) {
  .team-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
  .team-grid { grid-template-columns: 1fr; }
}
</style>
