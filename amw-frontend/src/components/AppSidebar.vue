<template>
  <aside
      class="fixed left-0 top-0 h-full bg-[#FAF9F6] dark:bg-stone-950 z-50 border-r border-stone-200/40 transition-all duration-300 ease-in-out overflow-hidden"
      :class="isExpanded ? 'w-72 px-6' : 'w-20 px-3'"
      :aria-label="$t('accessibility.sideNavigation')"
      @mouseenter="isExpanded = true"
      @mouseleave="isExpanded = false"
      @focusin="isExpanded = true"
      @focusout="handleFocusOut"
  >
    <div class="h-full flex flex-col py-7">
      <!-- Usuario -->
      <div class="mb-12">
        <router-link
            to="/profile"
            class="flex items-center mb-2"
            :class="isExpanded ? 'gap-3 px-1' : 'justify-center'"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.profile') })"
        >
          <img
              :alt="$t('accessibility.profilePhoto', { name: userName })"
              class="w-10 h-10 rounded-full object-cover shrink-0"
              :src="userProfileImage"
          />

          <div
              v-if="isExpanded"
              class="overflow-hidden transition-opacity duration-300"
          >
            <h2 class="font-notoSerif text-lg leading-none text-stone-900 dark:text-stone-50 truncate">
              {{ userName }}
            </h2>

            <p class="font-manrope text-[10px] tracking-widest text-primary mt-1 truncate">
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
          <span class="material-symbols-outlined shrink-0" aria-hidden="true">
            palette
          </span>
          <span v-if="isExpanded">{{ $t('nav.feed') }}</span>
        </router-link>

        <router-link
            :class="sideLinkClass('/profile')"
            to="/profile"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.profile') })"
            :title="!isExpanded ? $t('nav.profile') : null"
        >
          <span class="material-symbols-outlined shrink-0" aria-hidden="true">
            brush
          </span>
          <span v-if="isExpanded">{{ $t('nav.profile') }}</span>
        </router-link>

        <router-link
            :class="sideLinkClass('/collections')"
            to="/collections"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.collections') })"
            :title="!isExpanded ? $t('nav.collections') : null"
        >
          <span class="material-symbols-outlined shrink-0" aria-hidden="true">
            gallery_thumbnail
          </span>
          <span v-if="isExpanded">{{ $t('nav.collections') }}</span>
        </router-link>

        <router-link
            :class="sideLinkClass('/messages')"
            to="/messages"
            :aria-label="$t('accessibility.goTo', { page: $t('nav.messages') })"
            :title="!isExpanded ? $t('nav.messages') : null"
        >
          <span class="material-symbols-outlined shrink-0" aria-hidden="true">
            forum
          </span>
          <span v-if="isExpanded">{{ $t('nav.messages') }}</span>
        </router-link>
      </nav>

      <!-- Parte inferior -->
      <div class="mt-auto space-y-6">
        <button
            class="sidebar-upload-button"
            :class="isExpanded ? 'sidebar-upload-expanded' : 'sidebar-upload-collapsed'"
            type="button"
            :aria-label="$t('nav.uploadArtwork')"
            @click="showCreatePostModal = true"
            :title="!isExpanded ? $t('nav.uploadArtwork') : null"
        >
          <span
              v-if="!isExpanded"
              class="material-symbols-outlined text-xl"
              aria-hidden="true"
          >
            add
          </span>

          <span v-else class="whitespace-nowrap">
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
            <span class="material-symbols-outlined text-sm shrink-0" aria-hidden="true">
              support_agent
            </span>
            <span v-if="isExpanded">{{ $t('nav.support') }}</span>
          </router-link>

          <router-link
              :class="bottomLinkClass('/terms')"
              to="/terms"
              :aria-label="$t('accessibility.goTo', { page: $t('nav.terms') })"
              :title="!isExpanded ? $t('nav.terms') : null"
          >
            <span class="material-symbols-outlined text-sm shrink-0" aria-hidden="true">
              info
            </span>
            <span v-if="isExpanded">{{ $t('nav.terms') }}</span>
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
          'flex items-center gap-4 py-3 font-manrope uppercase tracking-widest text-[10px] transition-colors duration-300 whitespace-nowrap'

      if (this.isActive(path)) {
        return `${base} text-[#A900A9] dark:text-[#FF00FF] font-bold border-l-4 border-[#A900A9] ${this.isExpanded ? 'pl-4 translate-x-1' : 'pl-0 justify-center'}`
      }

      return `${base} text-stone-500 dark:text-stone-400 hover:text-[#FF00FF] ${this.isExpanded ? 'pl-5' : 'justify-center'}`
    },

    bottomLinkClass(path) {
      const base =
          'flex items-center gap-4 font-manrope uppercase tracking-widest text-[10px] transition-colors duration-300 whitespace-nowrap py-2'

      if (this.isActive(path)) {
        return `${base} text-[#A900A9] dark:text-[#FF00FF] font-bold border-l-4 border-[#A900A9] ${this.isExpanded ? 'pl-4 translate-x-1' : 'pl-0 justify-center'}`
      }

      return `${base} text-stone-500 dark:text-stone-400 hover:text-[#FF00FF] ${this.isExpanded ? 'pl-5' : 'justify-center'}`
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

.sidebar-upload-button {
  height: 44px;
  min-height: 44px;
  border-radius: 14px;
  border: none;
  background-color: #a900a9;
  color: #ffffff;
  font-family: Manrope, sans-serif;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: pointer;
  transition:
      width 0.3s ease,
      opacity 0.2s ease,
      transform 0.2s ease,
      background-color 0.2s ease;
}

.sidebar-upload-button:hover {
  opacity: 0.9;
}

.sidebar-upload-button:active {
  transform: scale(0.96);
}

.sidebar-upload-collapsed {
  width: 44px;
  margin-left: auto;
  margin-right: auto;
}

.sidebar-upload-expanded {
  width: 100%;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>