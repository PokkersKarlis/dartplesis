<template>
  <footer class="dk-footer">
    <!-- Supporters row -->
    <div class="footer-supporters">
      <SupportersBar variant="footer" :show-label="false" />
    </div>

    <div class="footer-bottom">
    <div class="footer-brand">
      <span class="footer-brand__name">DK <span class="footer-brand__accent">Dārtplēsis</span></span>
      <span class="footer-copy">{{ t('footer.copy') }}</span>
      <span class="footer-dev">
        Developed with <span class="footer-heart">♥</span> in
        <svg class="footer-flag" viewBox="0 0 30 20" aria-label="Latvia" role="img">
          <rect width="30" height="8" fill="#9E3039"/>
          <rect y="8" width="30" height="4" fill="#ffffff"/>
          <rect y="12" width="30" height="8" fill="#9E3039"/>
        </svg>
        &amp; powered by
        <a href="https://pokkers.lv" target="_blank" rel="noopener" class="footer-dev-link">pokkers.lv</a>
      </span>
    </div>

    <div class="footer-social">
      <!-- Facebook -->
      <a :href="fbHref" class="social-btn" aria-label="Facebook" title="Facebook" target="_blank" rel="noopener">
        <svg class="social-icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-337 273 123.5 256" xml:space="preserve">
          <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
          <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
          <g id="SVGRepo_iconCarrier">
            <path d="M-260.9,327.8c0-10.3,9.2-14,19.5-14c10.3,0,21.3,3.2,21.3,3.2l6.6-39.2c0,0-14-4.8-47.4-4.8c-20.5,0-32.4,7.8-41.1,19.3 c-8.2,10.9-8.5,28.4-8.5,39.7v25.7H-337V396h26.5v133h49.6V396h39.3l2.9-38.3h-42.2V327.8z"></path>
          </g>
        </svg>
      </a>
      <!-- WhatsApp -->
      <a :href="waHref" class="social-btn" aria-label="WhatsApp" title="WhatsApp" target="_blank" rel="noopener">
        <svg class="social-icon" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
        </svg>
      </a>
    </div>
    </div>
  </footer>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import SupportersBar from 'src/components/SupportersBar.vue'

const { t } = useI18n()

const fbHref = 'https://www.facebook.com/p/DK-D%C4%81rtpl%C4%93sis-61572260401164/'

// WhatsApp deep-link: tries the app first, falls back to web on desktop.
// Replace PHONENUMBER with the full international number (no + or spaces), e.g. 37112345678
const waPhone = '37122811821'   // ← paste number here, e.g. '37112345678'
const waHref  = waPhone ? `https://wa.me/${waPhone}` : '#'
</script>

<style scoped lang="scss">
.dk-footer {
  background: #080808;
  border-top: 1px solid rgba(78, 205, 196, 0.15);
}

.footer-supporters {
  padding: 1.5rem 5vw;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.footer-bottom {
  padding: 2rem 5vw;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}

.footer-brand {
  display: flex;
  flex-direction: column;
  gap: 4px;

  &__name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.2rem;
    letter-spacing: 0.1em;
    color: #f5f5f5;
  }

  &__accent { color: #4ecdc4; }
}

.footer-copy {
  font-size: 0.78rem;
  color: #555;
}

.footer-dev {
  font-size: 0.72rem;
  color: #444;
  margin-top: 2px;
}

.footer-heart {
  color: #e74c3c;
  font-size: 0.8rem;
}

.footer-flag {
  width: 18px;
  height: 12px;
  border-radius: 2px;
  vertical-align: middle;
  display: inline-block;
  margin: 0 1px;
  position: relative;
  top: -1px;
}

.footer-dev-link {
  color: #4ecdc4;
  text-decoration: none;
  transition: color 0.2s;

  &:hover { color: #6ee0d9; text-decoration: underline; }
}

.footer-social {
  display: flex;
  gap: 1rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 8px;
  border: 1px solid rgba(78, 205, 196, 0.15);
  color: rgba(78, 205, 196, 0.15);
  text-decoration: none;
  transition: all 0.2s;

  &:hover {
    border-color: #4ecdc4;
    color: #4ecdc4;
    background: rgba(78, 205, 196, 0.18);
  }
}

.social-icon {
  width: 18px;
  height: 18px;
  fill: currentColor;
  display: block;
}
</style>
