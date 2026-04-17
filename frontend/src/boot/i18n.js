import { defineBoot } from '#q-app/wrappers'
import { createI18n } from 'vue-i18n'
import { watch } from 'vue'
import messages from 'src/i18n'

const savedLocale = typeof localStorage !== 'undefined'
  ? (localStorage.getItem('dk_locale') || 'lv')
  : 'lv'

export const i18n = createI18n({
  locale: savedLocale,
  fallbackLocale: 'lv',
  legacy: false,
  messages,
})

function applyLocale(locale) {
  if (typeof document !== 'undefined') {
    document.documentElement.lang = locale === 'en' ? 'en' : 'lv'
  }
}

// Set lang on boot
applyLocale(savedLocale)

export default defineBoot(({ app }) => {
  app.use(i18n)

  // Keep <html lang> in sync when user switches language
  watch(i18n.global.locale, (newLocale) => {
    applyLocale(newLocale)
  })
})
