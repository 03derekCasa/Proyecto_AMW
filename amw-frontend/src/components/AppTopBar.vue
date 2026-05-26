<template>
  <nav
      class="fixed top-0 left-20 right-0 z-40 flex justify-between items-center gap-4 px-4 sm:px-8 lg:px-12 py-5 bg-[#FAF9F6]/70 dark:bg-stone-950/70 backdrop-blur-xl"
      :aria-label="$t('accessibility.topNavigation')"
  >
    <!-- Zona izquierda -->
    <div class="flex items-center gap-8">
      <router-link
          to="/feed"
          class="font-notoSerif italic text-xl text-stone-900 dark:text-stone-50 hover:text-primary transition-colors"
          aria-label="Art Makes A Way"
      >
        AMW
      </router-link>
    </div>

    <!-- Zona derecha -->
    <div class="flex items-center gap-3 sm:gap-6">
      <!-- Selector global de idioma -->
      <div class="relative">
        <label for="language-selector" class="sr-only">
          {{ $t('common.language') }}
        </label>

        <select
            id="language-selector"
            v-model="$i18n.locale"
            class="bg-transparent border-0 focus:ring-0 font-manrope text-[10px] uppercase tracking-widest text-primary font-bold cursor-pointer px-1 py-2"
            :aria-label="$t('accessibility.chooseLanguage')"
            @change="saveLocale"
        >
          <option value="es">ES</option>
          <option value="en">EN</option>
          <option value="fr">FR</option>
        </select>
      </div>

      <button
          type="button"
          :aria-label="$t('common.notifications')"
          class="hidden sm:block text-on-surface cursor-pointer hover:opacity-70 transition-opacity"
      >
        <span class="material-symbols-outlined" aria-hidden="true">
          notifications
        </span>
      </button>

      <!-- Solo aparece en ProfilePage -->
      <button
          v-if="showEditButton"
          class="hidden md:block font-manrope text-[10px] uppercase tracking-widest text-primary font-bold hover:opacity-70 transition-opacity"
          type="button"
          @click="$emit('toggle-edit')"
      >
        {{ isEditing ? $t('nav.closeEdit') : $t('nav.editProfile') }}
      </button>

      <button
          v-if="showLogoutButton"
          class="hidden lg:block font-manrope text-[10px] uppercase tracking-widest text-stone-500 hover:text-primary transition-colors"
          type="button"
          @click="$emit('logout')"
      >
        {{ $t('nav.logout') }}
      </button>
    </div>
  </nav>
</template>

<script>
import api from '@/services/api'

export default {
  name: 'AppTopBar',

  emits: ['toggle-edit', 'logout'],

  props: {
    showEditButton: {
      type: Boolean,
      default: false,
    },

    isEditing: {
      type: Boolean,
      default: false,
    },

    showLogoutButton: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      currentUserName: 'Artista AMW',
      currentUserImage: 'https://placehold.co/100x100?text=AMW',
    }
  },

  mounted() {
    document.documentElement.lang = this.$i18n.locale
    this.loadCurrentUser()
  },

  methods: {
    saveLocale() {
      localStorage.setItem('amw_locale', this.$i18n.locale)
      document.documentElement.lang = this.$i18n.locale
    },

    async loadCurrentUser() {
      this.loadUserFromLocalStorage()

      const token = localStorage.getItem('amw_token')

      if (!token) {
        return
      }

      try {
        const response = await api.get('/profile')
        const payload = response.data
        const profileData = payload.data || payload.profile || payload

        this.currentUserName =
            profileData?.artistic_name ||
            this.currentUserName

        this.currentUserImage =
            profileData?.profile_image_url ||
            this.currentUserImage
      } catch (error) {
        if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
        }
      }
    },

    loadUserFromLocalStorage() {
      const storedUser = localStorage.getItem('amw_user')

      if (!storedUser) {
        return
      }

      try {
        const user = JSON.parse(storedUser)

        this.currentUserName =
            user?.profile?.artistic_name ||
            user?.name ||
            this.currentUserName

        this.currentUserImage =
            user?.profile?.profile_image_url ||
            user?.avatar ||
            this.currentUserImage
      } catch (error) {
        localStorage.removeItem('amw_user')
      }
    },
  },
}
</script>

<style scoped>
.font-notoSerif {
  font-family: 'Noto Serif', serif;
}

.font-manrope {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  white-space: nowrap;
  border: 0;
  clip: rect(0, 0, 0, 0);
}
</style>