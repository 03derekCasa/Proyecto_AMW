<template>
  <div class="bg-background text-on-surface min-h-screen">

    <!-- Sidebar -->

    <AppSidebar />

    <!-- Top App Bar -->

    <AppTopBar @logout="handleLogout" />

    <!-- Main Content -->
    <main class="ml-20 pt-28 sm:pt-32 pb-24 px-4 sm:px-8 lg:px-12 max-w-6xl">
      <!-- Hero Title -->
      <section class="mb-16 sm:mb-24 mt-8 sm:mt-12">
        <h1 class="font-notoSerif italic text-5xl sm:text-[4rem] text-on-surface tracking-tighter">
          {{ ui.boards }}
        </h1>

        <p class="font-manrope uppercase tracking-[0.3em] text-[10px] text-primary mt-2">
          {{ ui.subtitle }}
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
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 border-b border-outline-variant/15 pb-4">
          <div>
            <h2 class="font-notoSerif text-4xl italic text-on-surface">
              {{ ui.likes }}
            </h2>

            <p class="font-manrope uppercase tracking-widest text-[10px] text-stone-500 mt-2">
              {{ filteredLikedPosts.length }} {{ ui.savedPosts }}
            </p>
          </div>

          <button
              class="group flex items-center gap-2 text-primary font-manrope font-bold uppercase tracking-widest text-[11px] mb-1 disabled:opacity-50"
              type="button"
              :disabled="likedPosts.length === 0"
              @click="scrollToAllLikes"
          >
            {{ ui.viewAll }}
            <span class="material-symbols-outlined transition-transform group-hover:translate-x-2" aria-hidden="true">
              arrow_forward
            </span>
          </button>
        </div>

        <div v-if="loading" class="font-manrope text-sm text-stone-500 py-12">
          {{ ui.loading }}
        </div>

        <div v-else-if="filteredLikedPosts.length === 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <div class="col-span-1 lg:col-span-8 relative aspect-[16/9] border border-dashed border-outline-variant/50 rounded-lg flex items-center justify-center group cursor-pointer hover:bg-surface-container-high/50 transition-colors">
            <div class="text-center">
              <span class="material-symbols-outlined text-4xl text-outline-variant/60 group-hover:text-primary transition-colors" aria-hidden="true">
                favorite
              </span>

              <p class="font-manrope text-[10px] uppercase tracking-widest text-outline-variant mt-2">
                {{ ui.emptyLikes }}
              </p>

              <router-link
                  to="/feed"
                  class="inline-flex mt-4 text-primary font-manrope font-bold uppercase tracking-widest text-[10px] hover:underline"
              >
                {{ ui.goToFeed }}
              </router-link>
            </div>
          </div>

          <div class="col-span-1 lg:col-span-4 grid grid-cols-2 lg:block lg:space-y-8 gap-4 lg:gap-0">
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

        <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <!-- Featured liked post -->
          <article
              class="col-span-1 lg:col-span-8 relative aspect-[16/9] rounded-lg overflow-hidden bg-stone-200 group cursor-pointer"
              @click="goToPost(featuredPost.id)"
          >
            <img
                :src="featuredPost.image_url || fallbackImage"
                :alt="`${ui.imageOf} ${featuredPost.title}`"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />

            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-colors"></div>

            <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
              <p class="font-manrope text-[10px] uppercase tracking-widest text-white/70 mb-2">
                {{ featuredPost.category?.name || typeLabel(featuredPost.type) }}
              </p>

              <h3 class="font-notoSerif text-3xl italic">
                {{ featuredPost.title }}
              </h3>

              <p class="font-manrope text-sm text-white/80 mt-2 line-clamp-2">
                {{ featuredPost.description || ui.noDescription }}
              </p>
            </div>

            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
              <button
                  class="bg-white text-primary px-4 py-2 rounded-full text-xs font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                  type="button"
                  @click.stop="removeLike(featuredPost)"
              >
                {{ ui.removeLike }}
              </button>

              <button
                  class="bg-white text-stone-900 px-4 py-2 rounded-full text-xs font-bold shadow-sm hover:bg-stone-900 hover:text-white transition-colors"
                  type="button"
                  @click.stop="goToPost(featuredPost.id)"
              >
                {{ ui.viewMore }}
              </button>
            </div>
          </article>

          <!-- Side liked posts -->
          <div class="col-span-1 lg:col-span-4 grid grid-cols-2 lg:block lg:space-y-8 gap-4 lg:gap-0">
            <article
                v-for="post in sidePosts"
                :key="post.id"
                class="aspect-square rounded-lg overflow-hidden bg-stone-200 relative group cursor-pointer"
                @click="goToPost(post.id)"
            >
              <img
                  :src="post.image_url || fallbackImage"
                  :alt="`${ui.imageOf} ${post.title}`"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              />

              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition-colors"></div>

              <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/70 to-transparent text-white">
                <h3 class="font-notoSerif italic text-lg line-clamp-1">
                  {{ post.title }}
                </h3>

                <p class="font-manrope text-[10px] uppercase tracking-widest text-white/70 mt-1">
                  {{ post.category?.name || typeLabel(post.type) }}
                </p>
              </div>

              <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                    class="bg-white text-primary px-3 py-1 rounded-full text-[10px] font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                    type="button"
                    @click.stop="removeLike(post)"
                >
                  {{ ui.remove }}
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
              {{ ui.allLikes }}
            </h2>

            <p class="font-manrope text-sm text-stone-500 mt-2">
              {{ ui.favourites }}
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
                  :alt="`${ui.imageOf} ${post.title}`"
                  class="w-full h-72 object-cover transition-transform duration-700 group-hover:scale-[1.03]"
              />

              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors"></div>

              <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                <button
                    class="bg-white text-primary px-3 py-1.5 rounded-full text-[10px] font-bold shadow-sm hover:bg-primary hover:text-white transition-colors"
                    type="button"
                    @click.stop="removeLike(post)"
                >
                  {{ ui.removeLike }}
                </button>

                <button
                    class="bg-white text-stone-900 px-3 py-1.5 rounded-full text-[10px] font-bold shadow-sm hover:bg-stone-900 hover:text-white transition-colors"
                    type="button"
                    @click.stop="goToPost(post.id)"
                >
                  {{ ui.info }}
                </button>
              </div>
            </div>

            <div class="p-5">
              <p class="font-manrope uppercase tracking-widest text-[10px] text-primary font-bold mb-2">
                {{ post.category?.name || typeLabel(post.type) }}
              </p>

              <h3 class="font-notoSerif text-2xl italic mb-2">
                {{ post.title }}
              </h3>

              <p class="font-manrope text-sm text-stone-600 line-clamp-3">
                {{ post.description || ui.noDescription }}
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
              class="font-notoSerif italic text-2xl sm:text-3xl px-6 sm:px-20 border border-outline/10 hover:border-primary/50 transition-colors group py-8 inline-block"
          >
            {{ ui.morePublications }}
            <div class="absolute inset-0 bg-primary/5 scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
          </router-link>
        </div>

        <div class="mt-24 border-t border-outline/10 pt-12 flex flex-col sm:flex-row gap-6 justify-between items-center text-stone-400">
          <p class="font-manrope text-[10px] uppercase tracking-[0.2em]">
            © 2026 Art Makes A Way Archive
          </p>

          <div class="flex gap-8 font-manrope text-[10px] uppercase tracking-[0.2em]">
            <router-link class="hover:text-primary transition-colors" to="/terms">
              {{ ui.terms }}
            </router-link>

            <router-link class="hover:text-primary transition-colors" to="/help">
              {{ ui.help }}
            </router-link>

            <router-link class="hover:text-primary transition-colors" to="/profile">
              {{ ui.profile }}
            </router-link>
          </div>
        </div>
      </footer>
    </main>
  </div>
</template>

<script>
import {
  getMyLikes,
  unlikePost,
} from '@/services/postService'
import { logoutUser } from '@/services/authService'

const collectionTexts = {
  es: {
    boards: 'Tableros',
    subtitle: 'Colecciones personales • Archivo AMW',
    likes: 'Likes',
    savedPosts: 'publicaciones • Guardadas por ti',
    viewAll: 'Ver todo',
    loading: 'Cargando tus publicaciones favoritas...',
    emptyLikes: 'Todavía no has dado like a ninguna publicación',
    goToFeed: 'Ir al feed',
    publication: 'Publicación AMW',
    noDescription: 'Sin descripción disponible.',
    removeLike: 'Quitar like',
    viewMore: 'Ver más',
    remove: 'Quitar',
    allLikes: 'Todos tus likes',
    favourites: 'Publicaciones que has marcado como favoritas.',
    info: 'Info',
    morePublications: 'Ver más publicaciones AMW',
    terms: 'Términos',
    help: 'Ayuda',
    profile: 'Perfil',
    imageOf: 'Imagen de',
    noDate: 'Sin fecha',
    artist: 'Artista AMW',
    artwork: 'Obra',
    event: 'Evento',
    product: 'Producto',
    errors: {
      load: 'No se pudieron cargar tus likes.',
      remove: 'No se pudo quitar el like de esta publicación.',
    },
  },
  en: {
    boards: 'Boards',
    subtitle: 'Personal collections • AMW Archive',
    likes: 'Likes',
    savedPosts: 'publications • Saved by you',
    viewAll: 'View all',
    loading: 'Loading your favourite publications...',
    emptyLikes: 'You have not liked any publications yet',
    goToFeed: 'Go to feed',
    publication: 'AMW Publication',
    noDescription: 'No description available.',
    removeLike: 'Remove like',
    viewMore: 'View more',
    remove: 'Remove',
    allLikes: 'All your likes',
    favourites: 'Publications you have marked as favourites.',
    info: 'Info',
    morePublications: 'View more AMW publications',
    terms: 'Terms',
    help: 'Help',
    profile: 'Profile',
    imageOf: 'Image of',
    noDate: 'No date',
    artist: 'AMW Artist',
    artwork: 'Artwork',
    event: 'Event',
    product: 'Product',
    errors: {
      load: 'Your likes could not be loaded.',
      remove: 'The like could not be removed from this publication.',
    },
  },
  fr: {
    boards: 'Tableaux',
    subtitle: 'Collections personnelles • Archive AMW',
    likes: 'J’aime',
    savedPosts: 'publications • Enregistrées par vous',
    viewAll: 'Tout voir',
    loading: 'Chargement de vos publications favorites...',
    emptyLikes: 'Vous n’avez encore aimé aucune publication',
    goToFeed: 'Aller au fil',
    publication: 'Publication AMW',
    noDescription: 'Aucune description disponible.',
    removeLike: 'Retirer le j’aime',
    viewMore: 'Voir plus',
    remove: 'Retirer',
    allLikes: 'Tous vos j’aime',
    favourites: 'Publications que vous avez marquées comme favorites.',
    info: 'Info',
    morePublications: 'Voir plus de publications AMW',
    terms: 'Conditions',
    help: 'Assistance',
    profile: 'Profil',
    imageOf: 'Image de',
    noDate: 'Sans date',
    artist: 'Artiste AMW',
    artwork: 'Œuvre',
    event: 'Événement',
    product: 'Produit',
    errors: {
      load: 'Vos j’aime n’ont pas pu être chargés.',
      remove: 'Le j’aime n’a pas pu être retiré de cette publication.',
    },
  },
}

export default {
  name: 'CollectionsPage',

  data() {
    return {
      likedPosts: [],
      loading: false,
      errorMessage: '',
      search: '',
      fallbackImage: 'https://placehold.co/900x700?text=AMW',
    }
  },

  computed: {
    ui() {
      return collectionTexts[this.$i18n.locale] || collectionTexts.es
    },

    filteredLikedPosts() {
      const searchText = this.search.toLowerCase().trim()

      if (!searchText) {
        return this.likedPosts
      }

      return this.likedPosts.filter((post) => {
        const hashtags = Array.isArray(post.hashtags)
          ? post.hashtags.join(' ')
          : ''

        const text = `${post.title || ''} ${post.description || ''} ${post.type || ''} ${post.category?.name || ''} ${hashtags} ${this.getAuthorName(post)}`.toLowerCase()

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
    await this.loadLikedPosts()
  },

  methods: {

    async loadLikedPosts() {
      this.loading = true
      this.errorMessage = ''

      try {
        const result = await getMyLikes()
        this.likedPosts = result.posts
      } catch (error) {
        this.errorMessage = this.ui.errors.load
      } finally {
        this.loading = false
      }
    },

    async removeLike(post) {
      try {
        await unlikePost(post.id)

        this.likedPosts = this.likedPosts.filter((item) => item.id !== post.id)
      } catch (error) {
        this.errorMessage = this.ui.errors.remove
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
          this.ui.artist
      )
    },

    typeLabel(type) {
      const labels = {
        obra: this.ui.artwork,
        evento: this.ui.event,
        producto: this.ui.product,
      }

      return labels[type] || this.ui.publication
    },

    async handleLogout() {
      try {
        await logoutUser()
      } catch (error) {
        localStorage.removeItem('amw_token')
        localStorage.removeItem('amw_user')
      }

      this.$router.push('/login')
    },

    formatDate(date) {
      if (!date) {
        return this.ui.noDate
      }

      const locales = {
        es: 'es-ES',
        en: 'en-US',
        fr: 'fr-FR',
      }

      return new Date(date).toLocaleDateString(locales[this.$i18n.locale] || 'es-ES', {
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