<template>
  <div class="min-h-screen bg-[#FAF9F6] text-[#1A1C1A]">
    <AppTopBar @logout="handleLogout" />
    <AppSidebar />

    <main class="ml-20 pt-24 min-h-screen" aria-labelledby="post-detail-title">
      <!-- Cargando -->
      <div
          v-if="loading"
          class="min-h-[calc(100vh-6rem)] flex items-center justify-center font-manrope text-sm text-stone-500"
          role="status"
      >
        {{ $t('postDetail.loading') }}
      </div>

      <!-- Error -->
      <div
          v-else-if="errorMessage"
          class="max-w-2xl mx-auto mt-16 px-6 py-5 border border-red-200 bg-red-50 text-red-700 font-manrope text-sm"
          role="alert"
      >
        <p>{{ errorMessage }}</p>

        <router-link
            to="/feed"
            class="inline-block mt-4 text-primary font-bold uppercase tracking-widest text-[10px]"
        >
          {{ $t('postDetail.backToFeed') }}
        </router-link>
      </div>

      <!-- Contenido del post -->
      <div
          v-else-if="post"
          class="flex flex-col lg:flex-row min-h-[calc(100vh-6rem)]"
      >
        <!-- Imagen principal real del post -->
        <section
            class="w-full lg:w-[62%] min-h-[48vh] lg:min-h-[calc(100vh-6rem)] bg-[#F4F3F1] flex items-center justify-center p-6 lg:p-12"
            :aria-label="$t('postDetail.artworkLabel')"
        >
          <div class="relative w-full h-full flex items-center justify-center">
            <img
                :src="post.image_url || fallbackPostImage"
                :alt="post.title ? $t('postDetail.artworkAlt', { title: post.title }) : $t('postDetail.artworkLabel')"
                class="max-w-full max-h-[calc(100vh-10rem)] object-contain shadow-2xl"
            />

            <div
                v-if="post.id"
                class="absolute bottom-4 left-4 lg:bottom-8 lg:left-8 bg-white/85 backdrop-blur-md px-5 py-3 border-l-4 border-primary"
            >
              <p class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-500">
                {{ $t('postDetail.reference') }}
              </p>

              <p class="font-manrope text-sm font-bold text-stone-900">
                AMW-POST-{{ post.id }}
              </p>
            </div>
          </div>
        </section>

        <!-- Información y comentarios -->
        <aside
            class="w-full lg:w-[38%] bg-[#FAF9F6] border-l border-stone-200 flex flex-col"
            :aria-label="$t('postDetail.artworkLabel')"
        >
          <div class="flex-1 overflow-y-auto px-7 py-8 lg:px-10 lg:py-10 custom-scrollbar">
            <!-- Título y autor -->
            <header class="space-y-6">
              <div class="flex flex-wrap items-center gap-3">
                <span
                    class="px-3 py-1 bg-primary/10 text-primary font-manrope text-[10px] uppercase tracking-widest font-bold"
                >
                  {{ post.category?.name || typeLabel(post.type) }}
                </span>

                <span class="text-stone-500 font-manrope text-[10px] uppercase tracking-widest">
                  {{ formatDate(post.created_at) }}
                </span>
              </div>

              <h1
                  id="post-detail-title"
                  class="font-notoSerif text-4xl lg:text-5xl leading-tight tracking-tight text-stone-900"
              >
                {{ post.title }}
              </h1>

              <!-- Perfil real del creador -->
              <router-link
                  v-if="post.author?.id"
                  :to="`/profiles/${post.author.id}`"
                  class="inline-flex items-center gap-4 group"
                  :aria-label="$t('postDetail.viewProfile')"
              >
                <div class="w-12 h-12 rounded-full overflow-hidden bg-stone-200 shrink-0">
                  <img
                      :src="post.author.profile_image_url || fallbackAvatar"
                      :alt="$t('postDetail.commentAvatar', { name: authorName })"
                      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                  />
                </div>

                <div class="flex flex-col">
                  <span class="font-notoSerif text-lg text-stone-900 group-hover:text-primary transition-colors">
                    {{ authorName }}
                  </span>

                  <span class="font-manrope text-xs text-stone-500">
                    {{ $t('postDetail.viewPortfolio') }}
                  </span>
                </div>
              </router-link>

              <div
                  v-else
                  class="inline-flex items-center gap-4"
              >
                <div class="w-12 h-12 rounded-full overflow-hidden bg-stone-200">
                  <img
                      :src="fallbackAvatar"
                      :alt="$t('postDetail.userAvatar')"
                      class="w-full h-full object-cover"
                  />
                </div>

                <span class="font-notoSerif text-lg">
                  {{ $t('common.artist') }}
                </span>
              </div>
            </header>

            <!-- Likes -->
            <section class="flex items-center justify-between py-6 my-8 border-y border-stone-200">
              <button
                  type="button"
                  class="flex items-center gap-2 hover:text-primary transition-colors disabled:opacity-50"
                  :class="likedByMe ? 'text-primary' : 'text-stone-700'"
                  :disabled="updatingLike"
                  :aria-label="likedByMe ? $t('postDetail.unlike') : $t('postDetail.like')"
                  @click="toggleLike"
              >
                <span
                    class="material-symbols-outlined"
                    :class="likedByMe ? 'liked-icon' : ''"
                    aria-hidden="true"
                >
                  favorite
                </span>

                <span class="font-manrope font-bold">
                  {{ post.likes_count || 0 }}
                </span>
              </button>

              <div class="flex items-center gap-5 text-stone-400">
                <span class="font-manrope text-xs">
                  {{ $t('postDetail.comments') }}: {{ post.comments_count ?? comments.length }}
                </span>
              </div>
            </section>

            <!-- Descripción -->
            <section class="space-y-5 mb-10" aria-label="Descripción de la obra">
              <p
                  v-if="post.description"
                  class="font-manrope text-sm text-stone-700 leading-relaxed whitespace-pre-line"
              >
                {{ post.description }}
              </p>

              <p
                  v-else
                  class="font-manrope text-sm text-stone-400 italic"
              >
                {{ $t('postDetail.noDescription') }}
              </p>

              <div
                  v-if="post.hashtags && post.hashtags.length > 0"
                  class="flex flex-wrap gap-3 pt-2"
                  :aria-label="$t('postModal.hashtags')"
              >
                <span
                    v-for="tag in post.hashtags"
                    :key="tag"
                    class="text-primary font-manrope text-xs font-bold"
                >
                  #{{ tag }}
                </span>
              </div>
            </section>

            <!-- Comentarios -->
            <section class="space-y-6 pt-4" aria-labelledby="comments-title">
              <div class="flex items-baseline justify-between">
                <h2 id="comments-title" class="font-notoSerif text-xl text-stone-900">
                  {{ $t('postDetail.comments') }}
                </h2>

                <span class="font-manrope text-[10px] uppercase tracking-widest text-stone-400">
                  {{ $t('postDetail.visibleComments', { count: comments.length }) }}
                </span>
              </div>

              <p
                  v-if="commentsLoading"
                  class="font-manrope text-sm text-stone-400"
                  role="status"
              >
                {{ $t('postDetail.loadingComments') }}
              </p>

              <p
                  v-else-if="comments.length === 0"
                  class="font-manrope text-sm text-stone-400 italic"
              >
                {{ $t('postDetail.emptyComments') }}
              </p>

              <div v-else class="space-y-6">
                <!-- Cada comentario usa la imagen del perfil guardada en backend -->
                <article
                    v-for="comment in comments"
                    :key="comment.id"
                    class="flex gap-4"
                >
                  <router-link
                      v-if="comment.author?.id"
                      :to="`/profiles/${comment.author.id}`"
                      class="w-9 h-9 rounded-full overflow-hidden bg-stone-200 shrink-0"
                      :aria-label="$t('postDetail.viewProfile')"
                  >
                    <img
                        :src="comment.author.profile_image_url || fallbackAvatar"
                        :alt="$t('postDetail.commentAvatar', { name: getCommentAuthorName(comment) })"
                        class="w-full h-full object-cover"
                    />
                  </router-link>

                  <div
                      v-else
                      class="w-9 h-9 rounded-full overflow-hidden bg-stone-200 shrink-0"
                  >
                    <img
                        :src="fallbackAvatar"
                        :alt="$t('postDetail.userAvatar')"
                        class="w-full h-full object-cover"
                    />
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-baseline gap-2">
                      <span class="font-manrope font-bold text-sm text-stone-900">
                        {{ getCommentAuthorName(comment) }}
                      </span>

                      <span class="text-[10px] text-stone-400 font-manrope">
                        {{ formatCommentDate(comment.created_at) }}
                      </span>
                    </div>

                    <p class="text-sm text-stone-600 leading-relaxed mt-1 break-words">
                      {{ comment.content }}
                    </p>
                  </div>
                </article>
              </div>

              <!-- Formulario de nuevo comentario -->
              <form class="pt-5" @submit.prevent="submitComment">
                <label for="new-comment" class="sr-only">
                  {{ $t('postDetail.writeComment') }}
                </label>

                <div class="relative">
                  <input
                      id="new-comment"
                      name="comment"
                      v-model.trim="newComment"
                      class="w-full bg-white border border-stone-200 rounded-full focus:border-primary focus:ring-0 pl-5 pr-24 py-4 font-manrope text-sm transition-colors placeholder:text-stone-400 placeholder:italic"
                      :placeholder="$t('postDetail.commentPlaceholder')"
                      type="text"
                      maxlength="1000"
                      required
                  />

                  <button
                      type="submit"
                      class="absolute right-5 top-1/2 -translate-y-1/2 text-primary font-manrope text-[10px] uppercase font-bold tracking-widest disabled:opacity-40"
                      :disabled="sendingComment || !newComment.trim()"
                  >
                    {{ sendingComment ? $t('common.sending') : $t('common.publish') }}
                  </button>
                </div>
              </form>

              <p
                  v-if="commentError"
                  class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
                  role="alert"
              >
                {{ commentError }}
              </p>
            </section>
          </div>

          <!-- Acción inferior -->
          <div class="p-7 lg:p-8 bg-[#FAF9F6]/90 backdrop-blur-md border-t border-stone-200">
            <router-link
                v-if="post.author?.id"
                :to="`/profiles/${post.author.id}`"
                class="block w-full text-center bg-gradient-to-r from-primary to-primary-container text-white py-4 px-8 font-manrope text-xs uppercase tracking-widest font-bold hover:opacity-90 transition-opacity active:scale-[0.98]"
            >
              {{ $t('postDetail.viewArtistPortfolio') }}
            </router-link>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script>
import {
  getPost,
  getComments,
  createComment,
  likePost,
  unlikePost,
} from '@/services/postService'
import { logoutUser } from '@/services/authService'

export default {
  name: 'PostDetailPage',

  data() {
    return {
      post: null,
      comments: [],
      loading: true,
      commentsLoading: false,
      updatingLike: false,
      sendingComment: false,
      likedByMe: false,
      newComment: '',
      errorMessage: '',
      commentError: '',

      fallbackPostImage: 'https://placehold.co/1000x1000?text=AMW',
      fallbackAvatar: 'https://placehold.co/100x100?text=AMW',
    }
  },

  computed: {
    authorName() {
      return (
          this.post?.author?.artistic_name ||
          this.post?.author?.name ||
          this.$t('common.artist')
      )
    },
  },

  watch: {
    '$route.params.id': {
      async handler() {
        await this.loadPage()
      },
    },
  },

  async mounted() {
    await this.loadPage()
  },

  methods: {
    async loadPage() {
      this.loading = true
      this.errorMessage = ''
      this.commentError = ''

      try {
        const postId = this.$route.params.id

        const [post, commentResult] = await Promise.all([
          getPost(postId),
          getComments(postId),
        ])

        this.post = post
        this.comments = commentResult.comments
        this.likedByMe = Boolean(post.liked_by_me)
      } catch (error) {
        if (error.response?.status === 404) {
          this.errorMessage = this.$t('postDetail.errors.notFound')
        } else {
          this.errorMessage = this.$t('postDetail.errors.load')
        }
      } finally {
        this.loading = false
      }
    },

    async toggleLike() {
      if (!this.post || this.updatingLike) {
        return
      }

      this.updatingLike = true

      try {
        if (this.likedByMe) {
          await unlikePost(this.post.id)
          this.likedByMe = false
          this.post.likes_count = Math.max((this.post.likes_count || 1) - 1, 0)
        } else {
          await likePost(this.post.id)
          this.likedByMe = true
          this.post.likes_count = (this.post.likes_count || 0) + 1
        }
      } catch (error) {
        this.commentError = this.$t('postDetail.errors.like')
      } finally {
        this.updatingLike = false
      }
    },

    async submitComment() {
      if (!this.newComment.trim() || !this.post) {
        return
      }

      this.sendingComment = true
      this.commentError = ''

      try {
        const comment = await createComment(this.post.id, this.newComment.trim())

        this.comments.unshift(comment)
        this.post.comments_count = (this.post.comments_count || 0) + 1
        this.newComment = ''
      } catch (error) {
        if (error.response?.data?.errors?.content?.[0]) {
          this.commentError = error.response.data.errors.content[0]
        } else {
          this.commentError = this.$t('postDetail.errors.comment')
        }
      } finally {
        this.sendingComment = false
      }
    },

    getCommentAuthorName(comment) {
      return (
          comment.author?.artistic_name ||
          comment.author?.name ||
          this.$t('common.artist')
      )
    },

    formatDate(date) {
      if (!date) {
        return this.$t('common.noDate')
      }

      return new Date(date).toLocaleDateString(this.dateLocale(), {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      })
    },

    formatCommentDate(date) {
      if (!date) {
        return ''
      }

      return new Date(date).toLocaleDateString(this.dateLocale(), {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
      })
    },

    typeLabel(type) {
      const labels = {
        obra: this.$t('postModal.artwork'),
        evento: this.$t('postModal.event'),
        producto: this.$t('postModal.product'),
      }

      return labels[type] || this.$t('postModal.artwork')
    },

    dateLocale() {
      const locales = {
        es: 'es-ES',
        en: 'en-US',
        fr: 'fr-FR',
      }

      return locales[this.$i18n.locale] || 'es-ES'
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
.font-notoSerif {
  font-family: 'Noto Serif', serif;
}

.font-manrope {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.material-symbols-outlined.liked-icon {
  font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e3e2e0;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  overflow: hidden;
  white-space: nowrap;
  border: 0;
  clip: rect(0, 0, 0, 0);
}
</style>