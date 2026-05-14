<template>
  <nav
      class="fixed top-0 left-20 right-0 z-40 flex justify-between items-center px-12 py-5 bg-[#FAF9F6]/70 dark:bg-stone-950/70 backdrop-blur-xl"
      aria-label="Navegación superior"
  >
    <!-- Zona izquierda -->
    <div class="flex items-center gap-8">
      <div class="hidden md:flex gap-6">
      </div>
    </div>

    <!-- Zona derecha -->
    <div class="flex items-center gap-6">
      <button
          type="button"
          aria-label="Ver notificaciones"
          class="text-on-surface cursor-pointer hover:opacity-70 transition-opacity"
      >
        <span class="material-symbols-outlined" aria-hidden="true">
          notifications
        </span>
      </button>

      <!-- Solo sale en ProfilePage -->
      <button
          v-if="showEditButton"
          class="font-manrope text-[10px] uppercase tracking-widest text-primary font-bold hover:opacity-70 transition-opacity"
          type="button"
          @click="$emit('toggle-edit')"
      >
        {{ isEditing ? 'Cerrar edición' : 'Editar perfil' }}
      </button>

      <!-- Foto del usuario actual -->
      <router-link
          to="/profile"
          class="w-10 h-10 rounded-full overflow-hidden bg-stone-200 border border-stone-200 hover:ring-2 hover:ring-primary transition-all"
          aria-label="Ir a mi perfil"
      >
        <img
            :src="currentUserImage"
            :alt="`Foto de perfil de ${currentUserName}`"
            class="w-full h-full object-cover"
        />
      </router-link>

      <button
          v-if="showLogoutButton"
          class="font-manrope text-[10px] uppercase tracking-widest text-stone-500 hover:text-primary transition-colors"
          type="button"
          @click="$emit('logout')"
      >
        Cerrar sesión
      </button>
    </div>
  </nav>
</template>

<script>
import api from '@/services/api'

export default {
  name: 'AppTopBar',

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
    this.loadCurrentUser()
  },

  methods: {
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
.topbar-link {
  font-family: Manrope, sans-serif;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: #78716c;
  transition: color 0.2s ease;
}

.topbar-link:hover {
  color: #a900a9;
}

.router-link-active.topbar-link {
  color: #a900a9;
  font-weight: 700;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>