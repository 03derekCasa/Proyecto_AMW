<template>
  <div class="min-h-screen bg-[#FAF9F6] font-sans text-[#1a1a1a] antialiased">

    <!-- Top Navigation Bar -->

    <AppTopBar @logout="handleLogout" />

    <!-- Main Content Area -->

    <div class="flex">

      <!-- SideBar -->

      <AppSidebar />

      <!-- Main Content Area -->

      <main class="ml-20 pt-28 p-6 min-h-screen" aria-labelledby="feed-title">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-baseline md:justify-between">
          <div>
            <h1 id="feed-title" class="font-serif text-3xl font-bold">
              Feed
            </h1>

            <p class="text-sm text-gray-500 mt-2">
              Publicaciones reales cargadas desde la base de datos de AMW.
            </p>
          </div>

          <div class="flex gap-4 text-sm font-medium text-gray-400">
            <button
                class="pb-1"
                :class="selectedType === 'all' ? 'text-[#1a1a1a] border-b-2 border-[#FF00FF]' : 'hover:text-[#1a1a1a]'"
                type="button"
                @click="selectedType = 'all'"
            >
              Todo
            </button>

            <button
                class="pb-1"
                :class="selectedType === 'obra' ? 'text-[#1a1a1a] border-b-2 border-[#FF00FF]' : 'hover:text-[#1a1a1a]'"
                type="button"
                @click="selectedType = 'obra'"
            >
              Obras
            </button>

            <button
                class="pb-1"
                :class="selectedType === 'evento' ? 'text-[#1a1a1a] border-b-2 border-[#FF00FF]' : 'hover:text-[#1a1a1a]'"
                type="button"
                @click="selectedType = 'evento'"
            >
              Eventos
            </button>

            <button
                class="pb-1"
                :class="selectedType === 'producto' ? 'text-[#1a1a1a] border-b-2 border-[#FF00FF]' : 'hover:text-[#1a1a1a]'"
                type="button"
                @click="selectedType = 'producto'"
            >
              Productos
            </button>
          </div>
        </div>

        <div
            v-if="errorMessage"
            class="mb-6 text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
            role="alert"
        >
          {{ errorMessage }}
        </div>

        <div
            v-if="loading && posts.length === 0"
            class="text-sm text-gray-500"
            role="status"
        >
          Cargando publicaciones...
        </div>

        <div
            v-else-if="filteredPosts.length === 0"
            class="bg-white border border-gray-100 rounded-xl p-10 text-center"
        >
          <h2 class="font-serif text-2xl font-bold mb-3">
            No hay publicaciones disponibles.
          </h2>

          <p class="text-gray-500 text-sm">
            Cuando existan posts publicados en la base de datos, aparecerán aquí.
          </p>
        </div>

        <!-- Masonry Grid -->
        <div
            v-else
            class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-6 masonry-grid"
        >
          <article
              v-for="post in filteredPosts"
              :key="post.id"
              class="masonry-item relative group overflow-hidden rounded-lg bg-gray-200 mb-6"
          >
            <img
                class="w-full h-auto object-cover transition-all duration-700"
                :src="post.image_url || fallbackImage"
                :alt="`Imagen de la publicación ${post.title}`"
                loading="lazy"
            />

            <div
                class="gallery-overlay absolute inset-0 opacity-0 group-hover:opacity-100 flex flex-col justify-between p-4 transition-opacity duration-300"
            >
              <div class="flex justify-end gap-2">
                <button
                    class="bg-white text-[#1a1a1a] px-4 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-[#FF00FF] hover:text-white transition-colors"
                    type="button"
                    @click.stop="handleLike(post)"
                >
                  {{ post.liked_by_me ? 'Liked' : 'Like' }}
                </button>

                <button
                    class="bg-white text-[#1a1a1a] px-4 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-[#1a1a1a] hover:text-white transition-colors"
                    type="button"
                    @click.stop="openPost(post)"
                >
                  Info
                </button>
              </div>

              <div class="flex justify-between items-end gap-4 text-white">
                <div>
                  <h2 class="text-sm font-semibold leading-tight">
                    {{ post.title }}
                  </h2>

                  <p class="text-xs opacity-80 mt-1">
                    {{ post.category?.name || post.type || 'AMW' }}
                  </p>
                </div>

                <span class="text-xs opacity-80 text-right">
                  {{ getAuthorName(post) }}
                </span>
              </div>
            </div>
          </article>
        </div>

        <div v-if="nextPageUrl" class="mt-10 flex justify-center">
          <button
              class="px-8 py-3 rounded-full bg-[#1a1a1a] text-white text-sm font-medium hover:bg-[#FF00FF] transition-colors disabled:opacity-60"
              type="button"
              :disabled="loading"
              @click="loadMore"
          >
            {{ loading ? 'Cargando...' : 'Cargar más' }}
          </button>
        </div>
      </main>
    </div>

    <!-- Modal de información del post -->
    <div
        v-if="selectedPost"
        class="fixed inset-0 z-[100] bg-black/60 flex items-center justify-center px-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="post-modal-title"
        @click.self="closePost"
    >
      <section class="bg-[#FAF9F6] max-w-4xl w-full max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2">
          <div class="bg-gray-200">
            <img
                class="w-full h-auto object-cover"
                :src="selectedPost.image_url || fallbackImage"
                :alt="`Imagen de la publicación ${selectedPost.title}`"
            />
          </div>

          <div class="p-8 flex flex-col">
            <div class="flex justify-between items-start gap-6 mb-6">
              <div>
                <p class="text-xs uppercase tracking-widest text-[#FF00FF] font-bold mb-2">
                  {{ selectedPost.category?.name || selectedPost.type || 'AMW' }}
                </p>

                <h2 id="post-modal-title" class="font-serif text-3xl font-bold">
                  {{ selectedPost.title }}
                </h2>

                <p class="text-sm text-gray-500 mt-2">
                  {{ getAuthorName(selectedPost) }}
                </p>
              </div>

              <button
                  class="text-gray-500 hover:text-[#1a1a1a] text-2xl"
                  type="button"
                  aria-label="Cerrar información del post"
                  @click="closePost"
              >
                ×
              </button>
            </div>

            <p class="text-gray-700 leading-relaxed mb-8">
              {{ selectedPost.description || 'Esta publicación no tiene descripción.' }}
            </p>

            <div class="mt-auto flex items-center justify-between border-t border-gray-200 pt-6">
              <div class="text-sm text-gray-500">
                <span class="font-bold text-[#1a1a1a]">
                  {{ selectedPost.likes_count || 0 }}
                </span>
                likes
              </div>

              <button
                  class="px-5 py-2 rounded-full bg-[#1a1a1a] text-white text-sm font-medium hover:bg-[#FF00FF] transition-colors"
                  type="button"
                  @click="handleLike(selectedPost)"
              >
                {{ selectedPost.liked_by_me ? 'Quitar like' : 'Dar like' }}
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
  import {
    getPosts,
    likePost,
    unlikePost,
  } from '@/services/postService'
  export default {
    name: 'FeedPage',

    data() {
      return {
        posts: [],
        selectedPost: null,
        loading: false,
        errorMessage: '',
        nextPageUrl: null,
        search: '',
        selectedType: 'all',
        fallbackImage: 'https://placehold.co/800x1000?text=AMW',
        userImage: 'https://placehold.co/100x100?text=AMW',
      }
    },

    computed: {
      filteredPosts() {
        return this.posts.filter((post) => {
          const matchesType =
              this.selectedType === 'all' || post.type === this.selectedType

          const text = `${post.title || ''} ${post.description || ''} ${post.category?.name || ''}`.toLowerCase()
          const matchesSearch = text.includes(this.search.toLowerCase())

          return matchesType && matchesSearch
        })
      },
    },

    async mounted() {
      window.addEventListener('amw-post-created', this.handlePostCreated)
      await this.loadPosts()
    },

    beforeUnmount() {
      window.removeEventListener('amw-post-created', this.handlePostCreated)
    },

    methods: {

      handlePostCreated() {
        this.loadPosts()
      },

      async loadPosts(url = '/posts') {
        this.loading = true
        this.errorMessage = ''

        try {
          const result = await getPosts(url)

          const normalizedPosts = result.posts.map((post) => ({
            ...post,
            liked_by_me: post.liked_by_me || false,
            likes_count: post.likes_count || 0,
          }))

          if (url === '/posts') {
            this.posts = normalizedPosts
          } else {
            this.posts = [...this.posts, ...normalizedPosts]
          }

          this.nextPageUrl = result.nextPageUrl
        } catch (error) {
          this.errorMessage = 'No se pudieron cargar las publicaciones.'
        } finally {
          this.loading = false
        }
      },

      async loadMore() {
        if (!this.nextPageUrl) {
          return
        }

        await this.loadPosts(this.nextPageUrl)
      },

      async handleLike(post) {
        const token = localStorage.getItem('amw_token')

        if (!token) {
          this.$router.push('/login')
          return
        }

        try {
          if (post.liked_by_me) {
            await unlikePost(post.id)
            post.liked_by_me = false
            post.likes_count = Math.max((post.likes_count || 1) - 1, 0)
          } else {
            await likePost(post.id)
            post.liked_by_me = true
            post.likes_count = (post.likes_count || 0) + 1
          }

          if (this.selectedPost && this.selectedPost.id === post.id) {
            this.selectedPost = post
          }
        } catch (error) {
          this.errorMessage = 'No se pudo actualizar el like.'
        }
      },

      openPost(post) {
        this.selectedPost = post
      },

      closePost() {
        this.selectedPost = null
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
    },
  }
</script>

<style scoped>
  .masonry-grid {
    column-gap: 1.5rem;
  }

  .masonry-item {
    break-inside: avoid;
    margin-bottom: 1.5rem;
  }

  .gallery-overlay {
    background: linear-gradient(
        to top,
        rgba(0, 0, 0, 0.55) 0%,
        rgba(0, 0, 0, 0.25) 45%,
        rgba(0, 0, 0, 0.08) 100%
    );
  }

  ::-webkit-scrollbar {
    width: 6px;
  }

  ::-webkit-scrollbar-track {
    background: #FAF9F6;
  }

  ::-webkit-scrollbar-thumb {
    background: #e5e5e5;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #FF00FF;
  }
</style>