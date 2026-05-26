<template>
  <div class="min-h-screen bg-[#FAF9F6] font-sans text-[#1a1a1a] antialiased">
    <AppTopBar @logout="handleLogout" />
    <AppSidebar />

    <main
        class="ml-20 pt-28 px-4 sm:px-6 pb-10 min-h-screen min-w-0"
        aria-labelledby="feed-title"
    >
      <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div class="shrink-0">
          <h1 id="feed-title" class="font-serif text-3xl font-bold">
            {{ $t('feed.title') }}
          </h1>

          <p class="text-sm text-gray-500 mt-2">
            {{ $t('feed.description') }}
          </p>
        </div>

        <nav
            class="flex w-full flex-wrap gap-x-4 gap-y-2 text-sm font-medium text-gray-400 lg:w-auto lg:justify-end"
            :aria-label="$t('feed.title')"
        >
          <button
              class="min-w-[56px] border-b-2 border-transparent pb-1 text-center transition-colors"
              :class="selectedType === 'all'
              ? 'text-[#1a1a1a] !border-[#FF00FF]'
              : 'hover:text-[#1a1a1a]'"
              type="button"
              :aria-pressed="selectedType === 'all'"
              @click="selectedType = 'all'"
          >
            {{ $t('feed.all') }}
          </button>

          <button
              class="min-w-[56px] border-b-2 border-transparent pb-1 text-center transition-colors"
              :class="selectedType === 'obra'
              ? 'text-[#1a1a1a] !border-[#FF00FF]'
              : 'hover:text-[#1a1a1a]'"
              type="button"
              :aria-pressed="selectedType === 'obra'"
              @click="selectedType = 'obra'"
          >
            {{ $t('feed.artworks') }}
          </button>

          <button
              class="min-w-[68px] border-b-2 border-transparent pb-1 text-center transition-colors"
              :class="selectedType === 'evento'
              ? 'text-[#1a1a1a] !border-[#FF00FF]'
              : 'hover:text-[#1a1a1a]'"
              type="button"
              :aria-pressed="selectedType === 'evento'"
              @click="selectedType = 'evento'"
          >
            {{ $t('feed.events') }}
          </button>

          <button
              class="min-w-[82px] border-b-2 border-transparent pb-1 text-center transition-colors"
              :class="selectedType === 'producto'
              ? 'text-[#1a1a1a] !border-[#FF00FF]'
              : 'hover:text-[#1a1a1a]'"
              type="button"
              :aria-pressed="selectedType === 'producto'"
              @click="selectedType = 'producto'"
          >
            {{ $t('feed.products') }}
          </button>
        </nav>
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
        {{ $t('feed.loading') }}
      </div>

      <div
          v-else-if="filteredPosts.length === 0"
          class="bg-white border border-gray-100 rounded-xl p-10 text-center"
      >
        <h2 class="font-serif text-2xl font-bold mb-3">
          {{ $t('feed.emptyTitle') }}
        </h2>

        <p class="text-gray-500 text-sm">
          {{ $t('feed.emptyText') }}
        </p>
      </div>

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
              :alt="$t('feed.postImageAlt', { title: post.title })"
              loading="lazy"
          />

          <div
              class="gallery-overlay absolute inset-0 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 flex flex-col justify-between p-4 transition-opacity duration-300"
          >
            <div class="flex justify-end gap-2">
              <button
                  class="bg-white text-[#1a1a1a] px-4 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-[#FF00FF] hover:text-white transition-colors"
                  type="button"
                  :aria-label="post.liked_by_me ? $t('postDetail.unlike') : $t('postDetail.like')"
                  @click.stop="handleLike(post)"
              >
                {{ post.liked_by_me ? $t('feed.liked') : $t('feed.like') }}
              </button>

              <button
                  class="bg-white text-[#1a1a1a] px-4 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-[#1a1a1a] hover:text-white transition-colors"
                  type="button"
                  @click.stop="goToPost(post.id)"
              >
                {{ $t('feed.details') }}
              </button>
            </div>

            <div class="flex justify-between items-end gap-4 text-white">
              <div>
                <h2 class="text-sm font-semibold leading-tight">
                  {{ post.title }}
                </h2>

                <p class="text-xs opacity-80 mt-1">
                  {{ post.category?.name || typeLabel(post.type) || 'AMW' }}
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
          {{ loading ? $t('feed.loadingMore') : $t('feed.loadMore') }}
        </button>
      </div>
    </main>
  </div>
</template>

<script>
import {
  getPosts,
  likePost,
  unlikePost,
} from '@/services/postService'
import { logoutUser } from '@/services/authService'

export default {
  name: 'FeedPage',

  data() {
    return {
      posts: [],
      loading: false,
      errorMessage: '',
      nextPageUrl: null,
      search: '',
      selectedType: 'all',
      fallbackImage: 'https://placehold.co/800x1000?text=AMW',
    }
  },

  computed: {
    filteredPosts() {
      return this.posts.filter((post) => {
        const matchesType =
            this.selectedType === 'all' || post.type === this.selectedType

        const hashtags = Array.isArray(post.hashtags)
            ? post.hashtags.join(' ')
            : ''

        const searchableText = `
          ${post.title || ''}
          ${post.description || ''}
          ${post.category?.name || ''}
          ${hashtags}
          ${this.getAuthorName(post)}
        `.toLowerCase()

        const matchesSearch = searchableText.includes(this.search.toLowerCase())

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
          liked_by_me: Boolean(post.liked_by_me),
          likes_count: post.likes_count || 0,
        }))

        if (url === '/posts') {
          this.posts = normalizedPosts
        } else {
          this.posts = [...this.posts, ...normalizedPosts]
        }

        this.nextPageUrl = result.nextPageUrl
      } catch (error) {
        this.errorMessage = this.$t('feed.errors.load')
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
      } catch (error) {
        this.errorMessage = this.$t('feed.errors.like')
      }
    },

    goToPost(postId) {
      this.$router.push(`/posts/${postId}`)
    },

    getAuthorName(post) {
      return (
          post.author?.artistic_name ||
          post.author?.name ||
          post.user?.profile?.artistic_name ||
          post.user?.name ||
          this.$t('common.artist')
      )
    },

    typeLabel(type) {
      const labels = {
        obra: this.$t('postModal.artwork'),
        evento: this.$t('postModal.event'),
        producto: this.$t('postModal.product'),
      }

      return labels[type] || type
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