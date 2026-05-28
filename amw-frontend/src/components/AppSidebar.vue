<template>
  <aside
    class="fixed left-0 top-0 h-full px-3 bg-[#FAF9F6] dark:bg-stone-950 z-50 border-r border-stone-200/40 transition-[width] duration-300 ease-in-out overflow-hidden"
    :class="isExpanded ? 'w-56' : 'w-20'"
    :aria-label="$t('accessibility.sideNavigation')"
    @mouseenter="isExpanded = true"
    @mouseleave="isExpanded = false"
    @focusin="isExpanded = true"
    @focusout="handleFocusOut"
  >
    <div class="h-full flex flex-col py-7">
      <!-- Usuario: el avatar no se desplaza al expandir -->
      <div class="mb-12">
        <router-link
          to="/profile"
          class="sidebar-profile-row"
          :aria-label="$t('accessibility.goTo', { page: $t('nav.profile') })"
        >
          <span class="sidebar-icon-slot">
            <img
              :alt="$t('accessibility.profilePhoto', { name: userName })"
              class="w-10 h-10 rounded-full object-cover shrink-0"
              :src="userProfileImage"
            />
          </span>

          <div
            class="sidebar-label sidebar-profile-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            <h2 class="font-manrope text-base font-extrabold leading-none text-stone-900 dark:text-stone-50 truncate">
              {{ userName }}
            </h2>

            <p class="font-manrope text-[11px] font-bold tracking-[0.08em] text-primary mt-1 truncate">
              {{ formattedUsername }}
            </p>
          </div>
        </router-link>
      </div>

      <!-- Navegación principal -->
      <nav class="flex-1 flex flex-col gap-2" :aria-label="$t('accessibility.sideNavigation')">
        <router-link
          :class="sideLinkClass('/feed')"
          to="/feed"
          :aria-label="$t('accessibility.goTo', { page: $t('nav.feed') })"
          :title="!isExpanded ? $t('nav.feed') : null"
        >
          <span v-if="isActive('/feed')" class="sidebar-active-line" aria-hidden="true"></span>

          <span class="sidebar-icon-slot">
            <span class="material-symbols-outlined" aria-hidden="true">
              palette
            </span>
          </span>

          <span
            class="sidebar-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            {{ $t('nav.feed') }}
          </span>
        </router-link>

        <router-link
          :class="sideLinkClass('/profile')"
          to="/profile"
          :aria-label="$t('accessibility.goTo', { page: $t('nav.profile') })"
          :title="!isExpanded ? $t('nav.profile') : null"
        >
          <span v-if="isActive('/profile')" class="sidebar-active-line" aria-hidden="true"></span>

          <span class="sidebar-icon-slot">
            <span class="material-symbols-outlined" aria-hidden="true">
              brush
            </span>
          </span>

          <span
            class="sidebar-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            {{ $t('nav.profile') }}
          </span>
        </router-link>

        <router-link
          :class="sideLinkClass('/collections')"
          to="/collections"
          :aria-label="$t('accessibility.goTo', { page: $t('nav.collections') })"
          :title="!isExpanded ? $t('nav.collections') : null"
        >
          <span v-if="isActive('/collections')" class="sidebar-active-line" aria-hidden="true"></span>

          <span class="sidebar-icon-slot">
            <span class="material-symbols-outlined" aria-hidden="true">
              gallery_thumbnail
            </span>
          </span>

          <span
            class="sidebar-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            {{ $t('nav.collections') }}
          </span>
        </router-link>

        <router-link
          :class="sideLinkClass('/messages')"
          to="/messages"
          :aria-label="$t('accessibility.goTo', { page: $t('nav.messages') })"
          :title="!isExpanded ? $t('nav.messages') : null"
        >
          <span v-if="isActive('/messages')" class="sidebar-active-line" aria-hidden="true"></span>

          <span class="sidebar-icon-slot">
            <span class="material-symbols-outlined" aria-hidden="true">
              forum
            </span>
          </span>

          <span
            class="sidebar-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            {{ $t('nav.messages') }}
          </span>
        </router-link>
      </nav>

      <!-- Parte inferior -->
      <div class="mt-auto space-y-6">
        <!-- El icono + permanece fijo; solo se despliega el fondo y el texto -->
        <button
          class="sidebar-upload-button"
          :class="{ 'sidebar-upload-expanded': isExpanded }"
          type="button"
          :aria-label="$t('nav.uploadArtwork')"
          :title="!isExpanded ? $t('nav.uploadArtwork') : null"
          @click="showCreatePostModal = true"
        >
          <span class="sidebar-icon-slot sidebar-upload-icon">
            <span class="material-symbols-outlined text-xl" aria-hidden="true">
              add
            </span>
          </span>

          <span
            class="sidebar-label"
            :class="{ 'sidebar-label-visible': isExpanded }"
          >
            {{ $t('nav.uploadArtwork') }}
          </span>
        </button>

        <div class="flex flex-col gap-2 pt-6 border-t border-stone-200 dark:border-stone-800">
          <router-link
            :class="bottomLinkClass('/help')"
            to="/help"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.support') })"
            :title="!isExpanded ? $t('nav.support') : null"
          >
            <span v-if="isActive('/help')" class="sidebar-active-line" aria-hidden="true"></span>

            <span class="sidebar-icon-slot">
              <span class="material-symbols-outlined text-sm" aria-hidden="true">
                support_agent
              </span>
            </span>

            <span
              class="sidebar-label"
              :class="{ 'sidebar-label-visible': isExpanded }"
            >
              {{ $t('nav.support') }}
            </span>
          </router-link>

          <router-link
            :class="bottomLinkClass('/terms')"
            to="/terms"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.terms') })"
            :title="!isExpanded ? $t('nav.terms') : null"
          >
            <span v-if="isActive('/terms')" class="sidebar-active-line" aria-hidden="true"></span>

            <span class="sidebar-icon-slot">
              <span class="material-symbols-outlined text-sm" aria-hidden="true">
                info
              </span>
            </span>

            <span
              class="sidebar-label"
              :class="{ 'sidebar-label-visible': isExpanded }"
            >
              {{ $t('nav.terms') }}
            </span>
          </router-link>
        </div>
      </div>
    </div>
  </aside>

  <CreatePostModal
    v-model="showCreatePostModal"
    @created="handlePostCreated"
  />
</template>

<script>
import api from '@/services/api'
import CreatePostModal from '@/components/CreatePostModal.vue'

export default {
  name: 'AppSidebar',

  components: {
    CreatePostModal,
  },

  data() {
    return {
      isExpanded: false,
      userName: 'Artista AMW',
      userUsername: '',
      userProfileImage: 'https://placehold.co/400x500?text=AMW',
      showCreatePostModal: false,
    }
  },

  computed: {
    formattedUsername() {
      if (!this.userUsername) {
        return '@amw'
      }

      return `@${String(this.userUsername).replace(/^@/, '')}`
    },
  },

  mounted() {
    this.loadSidebarProfile()
  },

  methods: {
    handlePostCreated(post) {
      window.dispatchEvent(new CustomEvent('amw-post-created', { detail: post }))
    },

    handleFocusOut(event) {
      if (!event.currentTarget.contains(event.relatedTarget)) {
        this.isExpanded = false
      }
    },

    async loadSidebarProfile() {
      this.loadUserFromLocalStorage()

      const token = localStorage.getItem('amw_token')

      if (!token) {
        return
      }

      try {
        const response = await api.get('/profile')
        const payload = response.data
        const profileData = payload.data || payload.profile || payload

        this.userName =
          profileData?.artistic_name ||
          this.userName

        this.userUsername =
          profileData?.username ||
          this.userUsername

        this.userProfileImage =
          profileData?.profile_image_url ||
          this.userProfileImage
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

        this.userName =
          user?.profile?.artistic_name ||
          this.userName

        this.userUsername =
          user?.username ||
          this.userUsername

        this.userProfileImage =
          user?.profile?.profile_image_url ||
          this.userProfileImage
      } catch (error) {
        localStorage.removeItem('amw_user')
      }
    },

    isActive(path) {
      if (path === '/feed') {
        return this.$route.path === '/feed' || this.$route.path.startsWith('/posts/')
      }

      return this.$route.path === path
    },

    sideLinkClass(path) {
      const base =
          'sidebar-row font-manrope font-semibold uppercase tracking-[0.16em] text-[11px]'

      if (this.isActive(path)) {
        return `${base} text-[#A900A9] dark:text-[#FF00FF] font-extrabold`
      }

      return `${base} text-stone-600 dark:text-stone-300 hover:text-[#FF00FF]`
    },

    bottomLinkClass(path) {
      const base =
          'sidebar-bottom-row font-manrope font-semibold uppercase tracking-[0.16em] text-[11px]'

      if (this.isActive(path)) {
        return `${base} text-[#A900A9] dark:text-[#FF00FF] font-extrabold`
      }

      return `${base} text-stone-600 dark:text-stone-300 hover:text-[#FF00FF]`
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

/*
 * El sidebar siempre conserva el mismo padding izquierdo.
 * Solo aumenta su anchura, por eso los iconos no saltan.
 */
.sidebar-profile-row,
.sidebar-row,
.sidebar-bottom-row {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
  white-space: nowrap;
  overflow: hidden;
}

.sidebar-profile-row {
  height: 52px;
}

.sidebar-row {
  height: 48px;
  transition: color 0.2s ease;
}

.sidebar-bottom-row {
  height: 42px;
  transition: color 0.2s ease;
}

/*
 * Cada icono ocupa siempre exactamente el mismo carril.
 * Su centro permanece en la misma posición con el sidebar cerrado o abierto.
 */
.sidebar-icon-slot {
  width: 56px;
  min-width: 56px;
  height: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/*
 * El texto no se crea y destruye con v-if.
 * Permanece en el DOM y solo se desliza/aparece.
 */
.sidebar-label {
  display: block;
  max-width: 0;
  opacity: 0;
  overflow: hidden;
  transform: translateX(-10px);
  pointer-events: none;
  transition:
    max-width 0.3s ease,
    opacity 0.18s ease,
    transform 0.3s ease;
}

.sidebar-label-visible {
  max-width: 135px;
  opacity: 1;
  transform: translateX(0);
  pointer-events: auto;
}

.sidebar-profile-label {
  min-width: 0;
}

/*
 * Indicador activo posicionado de forma absoluta.
 * No añade border ni padding al link, de modo que tampoco empuja el icono.
 */
.sidebar-active-line {
  position: absolute;
  left: 0;
  top: 50%;
  width: 4px;
  height: 30px;
  border-radius: 0 9999px 9999px 0;
  background-color: #a900a9;
  transform: translateY(-50%);
}

/*
 * Botón de subir obra:
 * cerrado: solo se ve una pastilla de 44px centrada en el carril del icono.
 * abierto: se extiende el fondo, pero el + mantiene su posición.
 */
.sidebar-upload-button {
  position: relative;
  width: 100%;
  height: 44px;
  min-height: 44px;
  padding: 0;
  border: none;
  background: transparent;
  color: #ffffff;
  font-family: Manrope, sans-serif;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  display: flex;
  align-items: center;
  overflow: hidden;
  cursor: pointer;
}

.sidebar-upload-button::before {
  content: '';
  position: absolute;
  left: 6px;
  top: 0;
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background-color: #a900a9;
  transition:
    left 0.3s ease,
    width 0.3s ease,
    opacity 0.2s ease;
}

.sidebar-upload-expanded::before {
  left: 0;
  width: 100%;
}

.sidebar-upload-button:hover::before {
  opacity: 0.9;
}

.sidebar-upload-button:active::before {
  transform: scale(0.98);
}

.sidebar-upload-icon,
.sidebar-upload-button .sidebar-label {
  position: relative;
  z-index: 1;
}

.sidebar-upload-button:focus-visible,
.sidebar-row:focus-visible,
.sidebar-bottom-row:focus-visible,
.sidebar-profile-row:focus-visible {
  outline: 2px solid #a900a9;
  outline-offset: 2px;
  border-radius: 12px;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
