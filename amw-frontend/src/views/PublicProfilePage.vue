<template>
  <div class="bg-surface text-on-surface min-h-screen">
    <AppSidebar />

    <!-- Top bar -->

    <AppTopBar @logout="handleLogout" />

    <!-- Contenido Principal -->

    <main class="ml-20 pt-32 min-h-screen" aria-labelledby="public-profile-title">
      <div v-if="loading" class="px-24 py-20 font-manrope text-sm text-stone-500">
        {{ ui.loading }}
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
                    :alt="ui.profileImageOf(userName)"
                />
              </div>
            </div>

            <div class="mb-8">
              <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-3">
                {{ ui.publicProfile }}
              </p>

              <h1
                  id="public-profile-title"
                  class="font-notoSerif text-6xl italic text-on-surface mb-2 tracking-tight"
              >
                {{ userName }}
              </h1>

              <p class="font-manrope text-sm text-stone-500 uppercase tracking-widest">
                {{ profile.specialty || ui.artist }}
              </p>

              <div class="flex gap-8 mt-6" :aria-label="ui.statisticsLabel">
                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    {{ ui.works }}
                  </span>
                  <span class="font-notoSerif text-2xl">
                    {{ userWorksCount }}
                  </span>
                </div>

                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    {{ ui.followers }}
                  </span>
                  <span class="font-notoSerif text-2xl">
                    {{ userFollowers }}
                  </span>
                </div>

                <div class="flex flex-col">
                  <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    {{ ui.following }}
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
              {{ ui.description }}
            </p>

            <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
              "{{ profile.biography || ui.emptyBiography }}"
            </div>

            <div class="mt-12 space-y-8">
              <button
                  class="w-full py-4 px-12 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10 disabled:opacity-60"
                  type="button"
                  :disabled="startingConversation"
                  @click="startConversation"
              >
                {{ startingConversation ? ui.openingChat : ui.sendMessage }}
              </button>

              <div class="flex gap-4">
                <a
                    v-if="profile.social_links?.website"
                    :href="profile.social_links.website"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    :aria-label="ui.openWebsite"
                >
                  language
                </a>

                <a
                    v-if="profile.social_links?.instagram"
                    :href="profile.social_links.instagram"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    :aria-label="ui.openInstagram"
                >
                  share
                </a>

                <a
                    v-if="profile.social_links?.behance"
                    :href="profile.social_links.behance"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
                    :aria-label="ui.openBehance"
                >
                  open_in_new
                </a>
              </div>
            </div>
          </aside>

          <!-- Obras -->
          <div class="col-span-8">
            <h2 class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">
              {{ ui.selectedWorks }}
            </h2>

            <div v-if="artworks.length === 0" class="bg-surface-container-low p-12 border border-outline-variant/20">
              <p class="font-notoSerif text-2xl italic mb-4">
                {{ ui.emptyWorksTitle }}
              </p>

              <p class="font-manrope text-sm text-stone-500">
                {{ ui.emptyWorksText }}
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
                      :alt="ui.artworkImage(artwork.title)"
                  />

                  <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="font-manrope text-white uppercase tracking-widest text-[10px] border border-white px-4 py-2">
                      {{ ui.viewArtwork }}
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
                {{ ui.backToFeed }}
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
                {{ ui.footerDescription }}
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
import { logoutUser } from '@/services/authService'

const publicProfileTexts = {
  es: {
    loading: 'Cargando perfil público...',
    publicProfile: 'Perfil público',
    artist: 'Artista AMW',
    statisticsLabel: 'Estadísticas del perfil público',
    works: 'Obras',
    followers: 'Seguidores',
    following: 'Siguiendo',
    description: 'Descripción',
    emptyBiography: 'Este artista todavía no ha añadido una biografía pública.',
    openingChat: 'Abriendo chat...',
    sendMessage: 'Enviar mensaje',
    openWebsite: 'Abrir página web del artista',
    openInstagram: 'Abrir Instagram del artista',
    openBehance: 'Abrir Behance del artista',
    selectedWorks: 'Obras seleccionadas',
    emptyWorksTitle: 'Este perfil todavía no tiene publicaciones.',
    emptyWorksText: 'Cuando este usuario publique obras en AMW, aparecerán aquí.',
    viewArtwork: 'Ver obra',
    backToFeed: 'Volver al feed',
    footerDescription: 'Perfil público de artista dentro de Art Makes A Way.',
    notFound: 'No se encontró el perfil solicitado.',
    loadError: 'No se pudo cargar el perfil público.',
    conversationReady: 'Conversación preparada correctamente.',
    conversationError: 'No se pudo iniciar la conversación.',
    artwork: 'Obra',
    profileImageOf: (name) => `Imagen de perfil de ${name}`,
    artworkImage: (title) => `Obra ${title}`,
  },
  en: {
    loading: 'Loading public profile...',
    publicProfile: 'Public profile',
    artist: 'AMW Artist',
    statisticsLabel: 'Public profile statistics',
    works: 'Artworks',
    followers: 'Followers',
    following: 'Following',
    description: 'Description',
    emptyBiography: 'This artist has not added a public biography yet.',
    openingChat: 'Opening chat...',
    sendMessage: 'Send message',
    openWebsite: "Open the artist's website",
    openInstagram: "Open the artist's Instagram",
    openBehance: "Open the artist's Behance",
    selectedWorks: 'Selected artworks',
    emptyWorksTitle: 'This profile has no publications yet.',
    emptyWorksText: 'When this user publishes artworks on AMW, they will appear here.',
    viewArtwork: 'View artwork',
    backToFeed: 'Back to feed',
    footerDescription: 'Public artist profile within Art Makes A Way.',
    notFound: 'The requested profile was not found.',
    loadError: 'The public profile could not be loaded.',
    conversationReady: 'Conversation ready.',
    conversationError: 'The conversation could not be started.',
    artwork: 'Artwork',
    profileImageOf: (name) => `Profile image of ${name}`,
    artworkImage: (title) => `Artwork ${title}`,
  },
  fr: {
    loading: 'Chargement du profil public...',
    publicProfile: 'Profil public',
    artist: 'Artiste AMW',
    statisticsLabel: 'Statistiques du profil public',
    works: 'Œuvres',
    followers: 'Abonnés',
    following: 'Abonnements',
    description: 'Description',
    emptyBiography: "Cet artiste n'a pas encore ajouté de biographie publique.",
    openingChat: 'Ouverture du chat...',
    sendMessage: 'Envoyer un message',
    openWebsite: "Ouvrir le site de l'artiste",
    openInstagram: "Ouvrir l'Instagram de l'artiste",
    openBehance: "Ouvrir le Behance de l'artiste",
    selectedWorks: 'Œuvres sélectionnées',
    emptyWorksTitle: "Ce profil n'a pas encore de publications.",
    emptyWorksText: "Lorsque cet utilisateur publiera des œuvres sur AMW, elles apparaîtront ici.",
    viewArtwork: "Voir l'œuvre",
    backToFeed: 'Retour au fil',
    footerDescription: "Profil public d'artiste au sein d'Art Makes A Way.",
    notFound: 'Le profil demandé est introuvable.',
    loadError: "Le profil public n'a pas pu être chargé.",
    conversationReady: 'Conversation prête.',
    conversationError: "La conversation n'a pas pu être démarrée.",
    artwork: 'Œuvre',
    profileImageOf: (name) => `Image de profil de ${name}`,
    artworkImage: (title) => `Œuvre ${title}`,
  },
}

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

  computed: {
    ui() {
      return publicProfileTexts[this.$i18n.locale] || publicProfileTexts.es
    },
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

    async handleLogout() {
      try {
        await logoutUser()
      } catch (error) {
        localStorage.removeItem('amw_token')
        localStorage.removeItem('amw_user')
      }

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
            this.ui.artist

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
          medium: post.category?.name || post.type || this.ui.artwork,
        }))
      } catch (error) {
        if (error.response?.status === 404) {
          this.errorMessage = this.ui.notFound
        } else if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
        } else {
          this.errorMessage = this.ui.loadError
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

        this.successMessage = this.ui.conversationReady

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
          this.actionErrorMessage = this.ui.conversationError
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