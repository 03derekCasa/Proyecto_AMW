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

      <div class="notification-center hidden sm:block">
        <button
            type="button"
            :aria-label="$t('common.notifications')"
            class="notification-trigger text-on-surface cursor-pointer hover:text-primary transition-colors"
            @click="toggleNotifications"
        >
          <span class="material-symbols-outlined" aria-hidden="true">
            notifications
          </span>

          <span
              v-if="unreadNotificationsCount > 0"
              class="notification-badge"
              aria-hidden="true"
          >
            {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
          </span>
        </button>

        <section
            v-if="showNotifications"
            class="notification-panel"
            aria-label="Notificaciones"
        >
          <div class="notification-panel__header">
            <h2>Notificaciones</h2>

            <button
                v-if="unreadNotificationsCount > 0"
                type="button"
                class="notification-panel__read-all"
                @click="readAllNotifications"
            >
              Marcar leídas
            </button>
          </div>

          <p v-if="loadingNotifications" class="notification-panel__empty">
            Cargando...
          </p>

          <p v-else-if="notifications.length === 0" class="notification-panel__empty">
            No tienes notificaciones.
          </p>

          <template v-else>
            <button
                v-for="notification in notifications"
                :key="notification.id"
                type="button"
                class="notification-item"
                :class="{ 'notification-item--unread': !notification.read_at }"
                @click="openNotification(notification)"
            >
              <img
                  v-if="notification.data?.actor?.profile_image_url"
                  :src="notification.data.actor.profile_image_url"
                  :alt="notification.data.actor.name"
                  class="notification-item__avatar"
              />

              <span v-else class="notification-item__avatar notification-item__avatar--empty">
                <span class="material-symbols-outlined text-sm">person</span>
              </span>

              <span class="notification-item__content">
                <span class="notification-item__text">{{ notification.data?.text }}</span>
                <span class="notification-item__time">{{ formatNotificationDate(notification.created_at) }}</span>
              </span>

              <span v-if="!notification.read_at" class="notification-item__dot"></span>
            </button>
          </template>
        </section>
      </div>

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
import {
  getNotifications,
  markNotificationAsRead,
  markAllNotificationsAsRead,
} from '@/services/notificationService'

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
      notifications: [],
      unreadNotificationsCount: 0,
      showNotifications: false,
      loadingNotifications: false,
      notificationInterval: null,
    }
  },

  mounted() {
    document.documentElement.lang = this.$i18n.locale
    this.loadCurrentUser()
    this.loadNotifications()

    this.notificationInterval = window.setInterval(() => {
      this.loadNotifications(false)
    }, 30000)

    document.addEventListener('click', this.handleOutsideNotificationClick)
    window.addEventListener('amw-messages-read', this.loadNotifications)
  },

  beforeUnmount() {
    window.clearInterval(this.notificationInterval)
    document.removeEventListener('click', this.handleOutsideNotificationClick)
    window.removeEventListener('amw-messages-read', this.loadNotifications)
  },

  methods: {
    async loadNotifications(showLoading = true) {
      if (!localStorage.getItem('amw_token')) {
        return
      }

      if (showLoading) {
        this.loadingNotifications = true
      }

      try {
        const payload = await getNotifications()
        this.notifications = payload.data || []
        this.unreadNotificationsCount = Number(payload.unread_count || 0)
      } catch (error) {
        this.notifications = []
        this.unreadNotificationsCount = 0
      } finally {
        this.loadingNotifications = false
      }
    },

    async toggleNotifications() {
      this.showNotifications = !this.showNotifications

      if (this.showNotifications) {
        await this.loadNotifications()
      }
    },

    async openNotification(notification) {
      if (!notification.read_at) {
        await markNotificationAsRead(notification.id)
      }

      this.showNotifications = false
      await this.loadNotifications(false)

      if (notification.data?.url) {
        this.$router.push(notification.data.url)
      }
    },

    async readAllNotifications() {
      await markAllNotificationsAsRead()
      await this.loadNotifications(false)
    },

    handleOutsideNotificationClick(event) {
      if (this.showNotifications && !event.target.closest('.notification-center')) {
        this.showNotifications = false
      }
    },

    formatNotificationDate(date) {
      if (!date) {
        return ''
      }

      return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
      })
    },

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
            this.currentUserName

        this.currentUserImage =
            user?.profile?.profile_image_url ||
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

.notification-center {
  position: relative;
}

.notification-trigger {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.notification-badge {
  position: absolute;
  top: -7px;
  right: -9px;
  min-width: 17px;
  height: 17px;
  padding: 0 4px;
  border: 2px solid #faf9f6;
  border-radius: 9999px;
  background: #a900a9;
  color: #fff;
  font-family: 'Manrope', sans-serif;
  font-size: 9px;
  font-weight: 700;
  line-height: 13px;
  text-align: center;
}

.notification-panel {
  position: absolute;
  top: calc(100% + 1rem);
  right: 0;
  width: min(360px, calc(100vw - 2rem));
  max-height: 460px;
  overflow-y: auto;
  border: 1px solid rgba(120, 113, 108, 0.15);
  border-radius: 1.25rem;
  background: #faf9f6;
  box-shadow: 0 18px 50px rgba(28, 25, 23, 0.16);
}

.notification-panel__header {
  position: sticky;
  top: 0;
  z-index: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.1rem 1.2rem;
  border-bottom: 1px solid #e7e5e4;
  background: #faf9f6;
}

.notification-panel__header h2 {
  font-family: 'Manrope', sans-serif;
  font-size: 0.95rem;
  font-weight: 700;
}

.notification-panel__read-all {
  color: #a900a9;
  font-family: 'Manrope', sans-serif;
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.notification-panel__empty {
  padding: 1.5rem 1.2rem;
  color: #78716c;
  font-family: 'Manrope', sans-serif;
  font-size: 0.84rem;
}

.notification-item {
  position: relative;
  width: 100%;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.95rem 1.2rem;
  text-align: left;
  transition: background 0.18s ease;
}

.notification-item:hover {
  background: #f3f0ed;
}

.notification-item--unread {
  background: rgba(169, 0, 169, 0.045);
}

.notification-item__avatar {
  width: 2.35rem;
  height: 2.35rem;
  flex-shrink: 0;
  border-radius: 9999px;
  object-fit: cover;
  background: #e7e5e4;
}

.notification-item__avatar--empty {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #78716c;
}

.notification-item__content {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.notification-item__text {
  color: #292524;
  font-family: 'Manrope', sans-serif;
  font-size: 0.8rem;
  line-height: 1.4;
}

.notification-item__time {
  color: #a8a29e;
  font-family: 'Manrope', sans-serif;
  font-size: 0.68rem;
}

.notification-item__dot {
  position: absolute;
  top: 1.2rem;
  right: 0.7rem;
  width: 7px;
  height: 7px;
  border-radius: 9999px;
  background: #a900a9;
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