<template>
  <div class="bg-background text-on-surface min-h-screen">

    <!-- Sidebar -->

    <AppSidebar />

    <!-- Top App Bar -->

    <AppTopBar @logout="handleLogout" />

    <!-- Main Content -->
    <main class="ml-20 pt-32 pb-24 px-12 max-w-6xl">
      <!-- Hero Title -->
      <section class="mb-24 mt-12">
        <h1 class="font-notoSerif italic text-[4rem] text-on-surface tracking-tighter">
          Tableros
        </h1>

        <p class="font-manrope uppercase tracking-[0.3em] text-[10px] text-primary mt-2">
          Colecciones personales • Archivo AMW
        </p>
      </section>

      <div
          v-if="errorMessage"
          class="mb-10 text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
          role="alert"
      >
        {{ errorMessage }}
      </div>

      <!-- Collection: Likes -->
      <section class="bg-surface-container-low shadow-sm rounded-xl hover:shadow-md transition-shadow mb-16 p-6">
        <div class="flex items-end justify-between mb-8 border-b border-outline-variant/15 pb-4">
          <div>
            <h2 class="font-notoSerif text-4xl italic text-on-surface">
              Likes
            </h2>

            <p class="font-manrope uppercase tracking-widest text-[10px] text-stone-500 mt-2">
              {{ filteredLikedPosts.length }} publicaciones • Guardadas por ti
            </p>
          </div>

          <button
              class="group flex items-center gap-2 text-primary font-manrope font-bold uppercase tracking-widest text-[11px] mb-1 disabled:opacity-50"
              type="button"
              :disabled="likedPosts.length === 0"
              @click="scrollToAllLikes"
          >
            Ver todo
            <span class="material-symbols-outlined transition-transform group-hover:translate-x-2" aria-hidden="true">
              arrow_forward
            </span>
          </button>
        </div>

        <div v-if="loading" class="font-manrope text-sm text-stone-500 py-12">
          Cargando tus publicaciones favoritas...
        </div>

        <div v-else-if="filteredLikedPosts.length === 0" class="grid grid-cols-12 gap-8">
          <div class="col-span-8 relative aspect-[16/9] border border-dashed border-outline-variant/50 rounded-lg flex items-center justify-center group cursor-pointer hover:bg-surface-container-high/50 transition-colors">
            <div class="text-center">
              <span class="material-symbols-outlined text-4xl text-outline-variant/60 group-hover:text-primary transition-colors" aria-hidden="true">
                favorite
              </span>

              <p class="font-manrope text-[10px] uppercase tracking-widest text-outline-variant mt-2">
                Todavía no has dado like a ninguna publicación
              </p>

              <router-link
                  to="/feed"
                  class="inline-flex mt-4 text-primary font-manrope font-bold uppercase tracking-widest text-[10px] hover:underline"
              >
                Ir al feed
              </router-link>
            </div>
          </div>

          <div class="col-span-4 space-y-8">
            <div class="aspect-square border border-dashed border-outline-variant/50 rounded-lg flex items-center justify-center group hover:bg-surface-container-high/50 transition-colors">
              <span class="material-symbols-outlined text-2xl text-outline-variant/60 group-hover:text-primary transition-colors" aria-hidden="true">
                add
              </span>
            </div>

            <div class="aspect-square border border-dashed border-outline-variant/50 rounded-lg flex items-center justify-center group hover:bg-surface-container-high/50 transition-colors">
              <span class="material-symbols-outlined text-2xl text-outline-variant/60 group-hover:text-primary transition-colors" aria-hidden="true">
                add
              </span>
            </div>
          </div>
        </div>

        <div v-else class="grid grid-cols-12 gap-8">
          <!-- Featured liked post -->
          <article
              class="col-span-8 relative aspect-[16/9] rounded-lg overflow-hidden bg-stone-200 group cursor-pointer"
              @click="goToPost(featuredPost.id)"
          >
            <img
                :src="featuredPost.image_url || fallbackImage"
                :alt="`Imagen de ${featuredPost.title}`"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />

            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-colors"></div>

            <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
              <p class="font-manrope text-[10px] uppercase tracking-widest text-white/70 mb-2">
                {{ featuredPost.category?.name || featuredPost.type || 'Publicación AMW' }}
              </p>

              <h3 class="font-notoSerif text-3xl italic">
                {{ featuredPost.title }}
              </h3>

              <p class="font-manrope text-sm text-white/80 mt-2 line-clamp-2">
                {{ featuredPost.description || 'Sin descripción disponible.' }}
              </p>
            </div>

            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
              <button
                  class="bg-white text-primary px-4 py-2 rounded-full text-xs font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                  type="button"
                  @click.stop="removeLike(featuredPost)"
              >
                Quitar like
              </button>

              <button
                  class="bg-white text-stone-900 px-4 py-2 rounded-full text-xs font-bold shadow-sm hover:bg-stone-900 hover:text-white transition-colors"
                  type="button"
                  @click.stop="goToPost(featuredPost.id)"
              >
                Ver más
              </button>
            </div>
          </article>

          <!-- Side liked posts -->
          <div class="col-span-4 space-y-8">
            <article
                v-for="post in sidePosts"
                :key="post.id"
                class="aspect-square rounded-lg overflow-hidden bg-stone-200 relative group cursor-pointer"
                @click="goToPost(post.id)"
            >
              <img
                  :src="post.image_url || fallbackImage"
                  :alt="`Imagen de ${post.title}`"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              />

              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-colors"></div>

              <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/70 to-transparent text-white">
                <h3 class="font-notoSerif italic text-lg line-clamp-1">
                  {{ post.title }}
                </h3>

                <p class="font-manrope text-[10px] uppercase tracking-widest text-white/70 mt-1">
                  {{ post.category?.name || post.type || 'AMW' }}
                </p>
              </div>

              <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                    class="bg-white text-primary px-3 py-1 rounded-full text-[10px] font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                    type="button"
                    @click.stop="removeLike(post)"
                >
                  Quitar
                </button>
              </div>
            </article>

            <div
                v-if="sidePosts.length < 2"
                class="aspect-square border border-dashed border-outline-variant/50 rounded-lg flex items-center justify-center group hover:bg-surface-container-high/50 transition-colors"
            >
              <span class="material-symbols-outlined text-2xl text-outline-variant/60 group-hover:text-primary transition-colors" aria-hidden="true">
                add
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- All liked posts -->
      <section
          v-if="filteredLikedPosts.length > 0"
          ref="allLikesSection"
          class="mt-20"
      >
        <div class="flex items-end justify-between mb-8">
          <div>
            <h2 class="font-notoSerif text-4xl italic">
              Todos tus likes
            </h2>

            <p class="font-manrope text-sm text-stone-500 mt-2">
              Publicaciones que has marcado como favoritas.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
          <article
              v-for="post in filteredLikedPosts"
              :key="post.id"
              class="bg-white rounded-xl overflow-hidden border border-outline-variant/20 shadow-sm group"
          >
            <div
                class="relative bg-stone-200 cursor-pointer"
                @click="goToPost(post.id)"
            >
              <img
                  :src="post.image_url || fallbackImage"
                  :alt="`Imagen de ${post.title}`"
                  class="w-full h-72 object-cover transition-transform duration-700 group-hover:scale-[1.03]"
              />

              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></div>

              <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                <button
                    class="bg-white text-primary px-3 py-1.5 rounded-full text-[10px] font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                    type="button"
                    @click.stop="removeLike(post)"
                >
                  Quitar like
                </button>

                <button
                    class="bg-white text-stone-900 px-3 py-1.5 rounded-full text-[10px] font-bold shadow-sm hover:bg-stone-900 hover:text-white transition-colors"
                    type="button"
                    @click.stop="goToPost(post.id)"
                >
                  Info
                </button>
              </div>
            </div>

            <div class="p-5">
              <p class="font-manrope uppercase tracking-widest text-[10px] text-primary font-bold mb-2">
                {{ post.category?.name || post.type || 'Sin categoría' }}
              </p>

              <h3 class="font-notoSerif text-2xl italic mb-2">
                {{ post.title }}
              </h3>

              <p class="font-manrope text-sm text-stone-600 line-clamp-3">
                {{ post.description || 'Esta publicación no tiene descripción.' }}
              </p>

              <div class="mt-5 pt-4 border-t border-outline-variant/20 flex justify-between items-center">
                <p class="font-manrope text-[10px] uppercase tracking-widest text-stone-400">
                  {{ getAuthorName(post) }}
                </p>

                <p class="font-manrope text-[10px] uppercase tracking-widest text-stone-400">
                  {{ formatDate(post.created_at) }}
                </p>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- Footer -->
      <footer class="mt-32 text-center">
        <div class="inline-block relative">
          <router-link
              to="/feed"
              class="font-notoSerif italic text-3xl px-20 border border-outline/10 hover:border-primary/50 transition-colors group py-8 inline-block"
          >
            Ver más publicaciones AMW
            <div class="absolute inset-0 bg-primary/5 scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
          </router-link>
        </div>

        <div class="mt-24 border-t border-outline/10 pt-12 flex justify-between items-center text-stone-400">
          <p class="font-manrope text-[10px] uppercase tracking-[0.2em]">
            © 2026 Art Makes A Way Archive
          </p>

          <div class="flex gap-8 font-manrope text-[10px] uppercase tracking-[0.2em]">
            <router-link class="hover:text-primary transition-colors" to="/terms">
              Términos
            </router-link>

            <router-link class="hover:text-primary transition-colors" to="/help">
              Ayuda
            </router-link>

            <router-link class="hover:text-primary transition-colors" to="/profile">
              Perfil
            </router-link>
          </div>
        </div>
      </footer>
    </main>
  </div>
</template>

<script>
import api from '@/services/api'

export default {
  name: 'CollectionsPage',

  data() {
    return {
      likedPosts: [],
      loading: false,
      errorMessage: '',
      search: '',
      fallbackImage: 'https://placehold.co/900x700?text=AMW',

      userProfileImage: 'https://placehold.co/400x500?text=AMW',
      userName: 'Artista AMW',
      profile: {},
    }
  },

  computed: {
    filteredLikedPosts() {
      const searchText = this.search.toLowerCase().trim()

      if (!searchText) {
        return this.likedPosts
      }

      return this.likedPosts.filter((post) => {
        const text = `${post.title || ''} ${post.description || ''} ${post.type || ''} ${post.category?.name || ''} ${this.getAuthorName(post)}`.toLowerCase()

        return text.includes(searchText)
      })
    },

    featuredPost() {
      return this.filteredLikedPosts[0] || null
    },

    sidePosts() {
      return this.filteredLikedPosts.slice(1, 3)
    },
  },

  async mounted() {
    await this.loadSidebarProfile()
    await this.loadLikedPosts()
  },

  methods: {

    async loadSidebarProfile() {
      try {
        const storedUser = localStorage.getItem('amw_user')
        let localUser = null

        if (storedUser) {
          localUser = JSON.parse(storedUser)
        }

        const response = await api.get('/profile')
        const payload = response.data
        const profileData = payload.data || payload.profile || payload

        this.profile = profileData || {}

        this.userName =
            profileData?.artistic_name ||
            localUser?.profile?.artistic_name ||
            localUser?.name ||
            'Artista AMW'

        this.userProfileImage =
            profileData?.profile_image_url ||
            localUser?.profile?.profile_image_url ||
            localUser?.avatar ||
            this.userProfileImage
      } catch (error) {
        const storedUser = localStorage.getItem('amw_user')

        if (storedUser) {
          const localUser = JSON.parse(storedUser)

          this.userName =
              localUser?.profile?.artistic_name ||
              localUser?.name ||
              'Artista AMW'

          this.userProfileImage =
              localUser?.profile?.profile_image_url ||
              localUser?.avatar ||
              this.userProfileImage
        }

        if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
        }
      }
    },

    async loadLikedPosts() {
      this.loading = true
      this.errorMessage = ''

      try {
        const response = await api.get('/my-likes')
        const payload = response.data

        let items = []

        if (Array.isArray(payload.data)) {
          items = payload.data
        } else if (Array.isArray(payload.data?.data)) {
          items = payload.data.data
        } else if (Array.isArray(payload)) {
          items = payload
        }

        this.likedPosts = items
            .map((item) => item.post || item)
            .filter((post) => post && post.id)
      } catch (error) {
        this.errorMessage = 'No se pudieron cargar tus likes.'
      } finally {
        this.loading = false
      }
    },

    async removeLike(post) {
      try {
        await api.delete(`/posts/${post.id}/like`)

        this.likedPosts = this.likedPosts.filter((item) => item.id !== post.id)
      } catch (error) {
        this.errorMessage = 'No se pudo quitar el like de esta publicación.'
      }
    },

    goToPost(postId) {
      this.$router.push(`/posts/${postId}`)
    },

    scrollToAllLikes() {
      if (!this.$refs.allLikesSection) {
        return
      }

      this.$refs.allLikesSection.scrollIntoView({
        behavior: 'smooth',
      })
    },

    getAuthorName(post) {
      return (
          post.author?.artistic_name ||
          post.author?.name ||
          post.user?.profile?.artistic_name ||
          post.user?.name ||
          'Artista AMW'
      )
    },

    handleLogout() {
      localStorage.removeItem('amw_token')
      localStorage.removeItem('amw_user')
      this.$router.push('/login')
    },

    formatDate(date) {
      if (!date) {
        return 'Sin fecha'
      }

      return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
      })
    },
  },
}
</script>

<style scoped>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    vertical-align: middle;
  }

  .font-notoSerif {
    font-family: 'Noto Serif', serif;
  }

  .font-manrope {
    font-family: 'Manrope', sans-serif;
  }

  .line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }
</style>