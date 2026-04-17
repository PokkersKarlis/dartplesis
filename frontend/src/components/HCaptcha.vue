<template>
  <div ref="widgetEl" class="hcaptcha-wrap" />
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  sitekey: {
    type: String,
    default: () => import.meta.env.VITE_HCAPTCHA_SITEKEY ?? '10000000-ffff-ffff-ffff-000000000001',
  },
})

const emit = defineEmits(['verified', 'expired', 'error'])

const widgetEl = ref(null)
let widgetId = null

function loadScript() {
  return new Promise((resolve) => {
    if (window.hcaptcha) { resolve(); return }
    window.__hcaptchaOnLoad = () => resolve()
    const s = document.createElement('script')
    s.src    = 'https://js.hcaptcha.com/1/api.js?render=explicit&onload=__hcaptchaOnLoad'
    s.async  = true
    s.defer  = true
    document.head.appendChild(s)
  })
}

onMounted(async () => {
  await loadScript()
  widgetId = window.hcaptcha.render(widgetEl.value, {
    sitekey:           props.sitekey,
    theme:             'dark',
    callback:          (token) => emit('verified', token),
    'expired-callback': ()     => emit('expired'),
    'error-callback':   ()     => emit('error'),
  })
})

onUnmounted(() => {
  if (widgetId !== null && window.hcaptcha) {
    try { window.hcaptcha.reset(widgetId) } catch { /* widget may already be gone */ }
  }
})

function reset() {
  if (widgetId !== null && window.hcaptcha) {
    try { window.hcaptcha.reset(widgetId) } catch { /* widget may already be gone */ }
  }
}

defineExpose({ reset })
</script>

<style scoped>
.hcaptcha-wrap { display: inline-block; }
</style>
