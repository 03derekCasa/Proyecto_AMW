<template>
  <div class="bg-surface text-on-surface min-h-screen">
    <AppSidebar />

    <!-- Top bar -->

    <AppTopBar @logout="handleLogout" />

    <!-- Contenido Principal -->

    <main class="ml-20 pt-32 min-h-screen" aria-labelledby="public-profile-title">
      <div v-if="loading" class="px-24 py-20 font-manrope text-sm text-stone-500">
        Cargando perfil público...
      </div>

      <div
          v-else-if="errorMessage"
          class="mx-24 my-20 text-red-700 bg-red-50 border border-red-200 px-4 py-3 font-manrope text-sm"
          role="alert"
      >
        {{ errorMessage }}
      </div>

      <template v-else>
        <!-- Header perfil -->
        <header class="relative px-12 mb-32">
          <div class="w-full h-[512px] bg-surface-container-low relative overflow-hidden">
            <img
                class="w-full h-full object-cover grayscale opacity-70 mix-blend-multiply"
                :src="heroImage"
                alt=""
                aria-hidden="true"
            />

            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-surface/80" aria-hidden="true"></div>
          </div>

          <div class="absolute bottom-[-80px] left-24 flex items-end gap-12">
            <div class="relative">
              <div class="w-48 h-64 bg-stone-200 overflow-hidden border-8 border-surface shadow-2xl">
                <img
                    class="w-full h-full object-cover"
                    :src="userProfileImage"
                    :alt="`Imagen de perfil de ${userName}`"
                />
              </div>
            </div>

            <div class="mb-8">
              <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-3">
                Perfil público
              </p>

              <h1
                  id="public-profile-title"
                  class="font-notoSerif text-6xl italic text-on-surface mb-2 tracking-tight"
              >
                {{ userName }}
              </h1>

              <p class="font-manrope text-sm text-stone-500 uppercase tracking-widest">
                {{ profile.specialty || 'Artista AMW' }}
              </p>

              <div class="flex gap-8 mt-6" aria-label="Estadísticas del perfil público">
                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    Obras
                  </span>
                  <span class="font-notoSerif text-2xl">
                    {{ userWorksCount }}
                  </span>
                </div>

                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    Seguidores
                  </span>
                  <span class="font-notoSerif text-2xl">
                    {{ userFollowers }}
                  </span>
                </div>

                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    Siguiendo
                  </span>
                  <span class="font-notoSerif text-2xl">
                    {{ userFollowing }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </header>

        <section
            v-if="successMessage || actionErrorMessage"
            class="px-24 mb-8"
            aria-live="polite"
            role="status"
        >
          <p
              v-if="successMessage"
              class="text-green-700 bg-green-50 border border-green-200 px-4 py-3 font-manrope text-sm"
          >
            {{ successMessage }}
          </p>

          <p
              v-if="actionErrorMessage"
              class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 font-manrope text-sm"
          >
            {{ actionErrorMessage }}
          </p>
        </section>

        <!-- Contenido -->
        <section class="px-24 mb-32 grid grid-cols-12 gap-12">
          <!-- Columna izquierda -->
          <aside class="col-span-4 sticky top-48 self-start">
            <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-6">
              Descripción
            </p>

            <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
              "{{ profile.biography || 'Este artista todavía no ha añadido una biografía pública.' }}"
            </div>

            <div class="mt-12 space-y-8">
              <button
                  class="w-full py-4 px-12 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10 disabled:opacity-60"
                  type="button"
                  :disabled="startingConversation"
                  @click="startConversation"
              >
                {{ startingConversation ? 'Abriendo chat...' : 'Enviar mensaje' }}
              </button>

              <div class="flex gap-4">
                <a
                    v-if="profile.social_links?.website"
                    :href="profile.social_links.website"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    aria-label="Abrir página web del artista"
                >
                  language
                </a>

                <a
                    v-if="profile.social_links?.instagram"
                    :href="profile.social_links.instagram"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    aria-label="Abrir Instagram del artista"
                >
                  share
                </a>

                <a
                    v-if="profile.social_links?.behance"
                    :href="profile.social_links.behance"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    aria-label="Abrir Behance del artista"
                >
                  open_in_new
                </a>
              </div>
            </div>
          </aside>

          <!-- Obras -->
          <div class="col-span-8">
            <h2 class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">
              Obras seleccionadas
            </h2>

            <div v-if="artworks.length === 0" class="bg-surface-container-low p-12 border border-outline-variant/20">
              <p class="font-notoSerif text-2xl italic mb-4">
                Este perfil todavía no tiene publicaciones.
              </p>

              <p class="font-manrope text-sm text-stone-500">
                Cuando este usuario publique obras en AMW, aparecerán aquí.
              </p>
            </div>

            <div v-else class="grid grid-cols-2 gap-16">
              <article v-for="artwork in artworks" :key="artwork.id" class="space-y-6">
                <div
                    class="bg-surface-container aspect-[3/4] relative overflow-hidden group cursor-pointer"
                    @click="goToPost(artwork.id)"
                >
                  <img
                      class="w-full h-full object-cover transition-all duration-700"
                      :src="artwork.image"
                      :alt="`Obra ${artwork.title}`"
                  />

                  <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="font-manrope text-white uppercase tracking-widest text-[10px] border border-white px-4 py-2">
                      Ver obra
                    </span>
                  </div>
                </div>

                <div class="flex justify-between items-baseline">
                  <h3 class="font-notoSerif text-xl italic">
                    {{ artwork.title }}
                  </h3>

                  <span class="font-manrope text-[10px] text-stone-400 uppercase tracking-widest">
                    {{ artwork.year }} • {{ artwork.medium }}
                  </span>
                </div>
              </article>
            </div>

            <div class="mt-32 flex justify-center">
              <router-link
                  to="/feed"
                  class="px-16 py-4 border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all"
              >
                Volver al feed
              </router-link>
            </div>
          </div>
        </section>

        <footer class="px-24 py-32 border-t border-stone-200 bg-surface-container-low">
          <div class="flex justify-between items-start">
            <div class="max-w-md">
              <h2 class="font-notoSerif italic text-3xl mb-6">
                AMW
              </h2>

              <p class="font-manrope text-sm text-stone-500 leading-relaxed">
                Perfil público de artista dentro de Art Makes A Way.
              </p>
            </div>
          </div>
        </footer>
      </template>
    </main>
  </div>
</template>

<script>
import api from '@/services/api'
import AppSidebar from '@/components/AppSidebar.vue'

export default {
  name: 'PublicProfilePage',

  components: {
    AppSidebar,
  },

  data() {
    return {
      loading: false,
      startingConversation: false,
      errorMessage: '',
      actionErrorMessage: '',
      successMessage: '',

      user: null,

      profile: {
        artistic_name: '',
        specialty: '',
        biography: '',
        profile_image_url: '',
        social_links: {},
      },

      userProfileImage: 'https://placehold.co/400x500?text=AMW',
      userName: 'Artista AMW',
      userWorksCount: 0,
      userFollowers: '0',
      userFollowing: 0,
      heroImage:
          'https://www.guiarepsol.com/content/dam/repsol-guia/contenidos-imagenes/viajar/nos-gusta/nuevas-exposiciones-madrid/gr-cms-media-featured_images-none-867ebd3f-4b8b-4725-afe5-ea38e18aedec-saura-3.jpg',

      artworks: [],
    }
  },

  async mounted() {
    await this.loadPublicProfile()
  },

  watch: {
    '$route.params.id': {
      async handler() {
        await this.loadPublicProfile()
      },
    },
  },

  methods: {

    handleLogout() {
      localStorage.removeItem('amw_token')
      localStorage.removeItem('amw_user')
      this.$router.push('/login')
    },

    async loadPublicProfile() {
      this.loading = true
      this.errorMessage = ''
      this.actionErrorMessage = ''
      this.successMessage = ''

      try {
        const userId = this.$route.params.id
        const response = await api.get(`/users/${userId}/public-profile`)
        const data = response.data.data || response.data

        this.user = data.user
        this.profile = {
          ...this.profile,
          ...(data.profile || {}),
          social_links: data.profile?.social_links || {},
        }

        this.userName =
            this.profile.artistic_name ||
            this.user?.name ||
            'Artista AMW'

        this.userProfileImage =
            this.profile.profile_image_url ||
            this.user?.avatar ||
            this.userProfileImage

        this.userWorksCount = data.stats?.works_count || 0
        this.userFollowers = data.stats?.followers || '0'
        this.userFollowing = data.stats?.following || 0

        this.artworks = (data.posts || []).map((post) => ({
          id: post.id,
          image: post.image_url || 'https://placehold.co/600x800?text=AMW',
          title: post.title,
          year: post.created_at
              ? new Date(post.created_at).getFullYear()
              : new Date().getFullYear(),
          medium: post.category?.name || post.type || 'Obra',
        }))
      } catch (error) {
        if (error.response?.status === 404) {
          this.errorMessage = 'No se encontró el perfil solicitado.'
        } else if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
        } else {
          this.errorMessage = 'No se pudo cargar el perfil público.'
        }
      } finally {
        this.loading = false
      }
    },

    async startConversation() {
      if (!this.user?.id) {
        return
      }

      this.startingConversation = true
      this.actionErrorMessage = ''
      this.successMessage = ''

      try {
        const response = await api.post('/conversations', {
          participant_id: this.user.id,
        })

        const conversation = response.data.data || response.data

        this.successMessage = 'Conversación preparada correctamente.'

        this.$router.push({
          path: '/messages',
          query: {
            conversation: conversation.id,
          },
        })
      } catch (error) {
        if (error.response?.data?.message) {
          this.actionErrorMessage = error.response.data.message
        } else {
          this.actionErrorMessage = 'No se pudo iniciar la conversación.'
        }
      } finally {
        this.startingConversation = false
      }
    },

    goToPost(postId) {
      this.$router.push(`/posts/${postId}`)
    },
  },
}
</script>

<style scoped>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.font-notoSerif {
  font-family: 'Noto Serif', serif;
}

.font-manrope {
  font-family: 'Manrope', sans-serif;
}
</style>