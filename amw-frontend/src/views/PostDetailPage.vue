<template>
  <div class="post-detail-page min-h-screen bg-[#FAF9F6] text-[#1A1C1A]">
    <!-- En esta vista se mantiene el sidebar -->
    <AppSidebar />

    <main class="post-detail-main ml-20 min-h-screen" aria-labelledby="post-detail-title">
      <!-- Cargando -->
      <div
        v-if="loading"
        class="min-h-screen flex items-center justify-center font-manrope text-sm text-stone-500"
        role="status"
      >
        {{ $t('postDetail.loading') }}
      </div>

      <!-- Error -->
      <div
        v-else-if="errorMessage"
        class="min-h-screen flex items-center justify-center px-6"
      >
        <div
          class="max-w-2xl w-full rounded-2xl px-6 py-5 border border-red-200 bg-red-50 text-red-700 font-manrope text-sm"
          role="alert"
        >
          <p>{{ errorMessage }}</p>

          <router-link
            to="/feed"
            class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary px-5 py-3 text-white font-bold uppercase tracking-widest text-[10px] hover:opacity-90 transition-opacity"
          >
            <span class="material-symbols-outlined text-base" aria-hidden="true">
              arrow_back
            </span>
            {{ $t('postDetail.backToFeed') }}
          </router-link>
        </div>
      </div>

      <!-- Contenido del post -->
      <div v-else-if="post" class="post-detail-layout">
        <!--
          Panel de imagen:
        -->
        <section
          class="post-media-panel"
          :aria-label="$t('postDetail.artworkLabel')"
        >
          <router-link
            to="/feed"
            class="post-back-button"
            :aria-label="$t('postDetail.backToFeed')"
          >
            <span class="material-symbols-outlined text-xl" aria-hidden="true">
              arrow_back
            </span>
            <span class="hidden sm:inline font-manrope text-[10px] font-bold uppercase tracking-widest">
              {{ $t('postDetail.backToFeed') }}
            </span>
          </router-link>

          <!--
            Marco siempre 1:1:
            object-contain conserva la obra completa.
            El fondo negro rellena el espacio sobrante de imágenes verticales u horizontales.
          -->
          <div class="post-media-frame">
            <img
              :src="post.image_url || fallbackPostImage"
              :alt="post.title
                ? $t('postDetail.artworkAlt', { title: post.title })
                : $t('postDetail.artworkLabel')"
              class="post-media-image"
            />
          </div>
        </section>

        <!-- Panel derecho: toda la información y comentarios desplazan juntos -->
        <aside
          class="post-info-panel custom-scrollbar"
          :aria-label="$t('postDetail.artworkLabel')"
        >
          <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-10">
            <!-- Título y autor -->
            <header class="space-y-6">
              <div class="flex flex-wrap items-center gap-3">
                <span
                  class="rounded-full px-4 py-1.5 bg-primary/10 text-primary font-manrope text-[10px] uppercase tracking-widest font-bold"
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
                    class="w-full h-full object-cover rounded-full transition-transform duration-300 group-hover:scale-105"
                  />
                </div>

                <div class="flex flex-col items-start">
                  <span class="font-manrope text-lg font-bold text-stone-900 group-hover:text-primary transition-colors">
                    {{ authorName }}
                  </span>

                  <span class="font-manrope text-xs font-bold text-primary">
                    {{ authorUsername }}
                  </span>

                  <span
                    class="inline-flex mt-2 rounded-full bg-primary/10 px-3 py-1 text-primary font-manrope text-[10px] font-bold uppercase tracking-widest"
                  >
                    {{ $t('postDetail.viewPortfolio') }}
                  </span>
                </div>
              </router-link>

              <div v-else class="inline-flex items-center gap-4">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-stone-200">
                  <img
                    :src="fallbackAvatar"
                    :alt="$t('postDetail.userAvatar')"
                    class="w-full h-full object-cover rounded-full"
                  />
                </div>

                <span class="font-manrope text-lg font-bold">
                  {{ $t('common.artist') }}
                </span>
              </div>
            </header>

            <!-- Likes y comentarios -->
            <section class="flex items-center justify-between py-6 my-8 border-y border-stone-200">
              <button
                type="button"
                class="flex items-center gap-2 rounded-full px-4 py-2 hover:bg-primary/10 hover:text-primary transition-colors disabled:opacity-50"
                :class="likedByMe ? 'text-primary bg-primary/10' : 'text-stone-700'"
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
            <section class="space-y-5 mb-10" :aria-label="$t('postDetail.artworkLabel')">
              <p
                v-if="post.description"
                class="font-manrope text-sm text-stone-700 leading-relaxed whitespace-pre-line"
              >
                {{ post.description }}
              </p>

              <p v-else class="font-manrope text-sm text-stone-400 italic">
                {{ $t('postDetail.noDescription') }}
              </p>

              <div
                v-if="post.hashtags && post.hashtags.length > 0"
                class="flex flex-wrap gap-2 pt-2"
                :aria-label="$t('postModal.hashtags')"
              >
                <span
                  v-for="tag in post.hashtags"
                  :key="tag"
                  class="rounded-full bg-primary/10 px-3 py-1 text-primary font-manrope text-xs font-bold"
                >
                  #{{ tag }}
                </span>
              </div>
            </section>

            <!-- Comentarios -->
            <section class="space-y-6 pt-4" aria-labelledby="comments-title">
              <div class="flex items-baseline justify-between gap-4">
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
                class="rounded-2xl bg-stone-50 px-5 py-5 font-manrope text-sm text-stone-400 italic"
              >
                {{ $t('postDetail.emptyComments') }}
              </p>

              <div v-else class="space-y-6">
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
                      class="w-full h-full object-cover rounded-full"
                    />
                  </router-link>

                  <div
                    v-else
                    class="w-9 h-9 rounded-full overflow-hidden bg-stone-200 shrink-0"
                  >
                    <img
                      :src="fallbackAvatar"
                      :alt="$t('postDetail.userAvatar')"
                      class="w-full h-full object-cover rounded-full"
                    />
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                      <span class="font-manrope font-bold text-sm text-stone-900">
                        {{ getCommentAuthorName(comment) }}
                      </span>

                      <span class="font-manrope text-[11px] font-bold text-primary">
                        {{ getCommentAuthorUsername(comment) }}
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
                    class="absolute right-5 top-1/2 -translate-y-1/2 rounded-full text-primary font-manrope text-[10px] uppercase font-bold tracking-widest disabled:opacity-40"
                    :disabled="sendingComment || !newComment.trim()"
                  >
                    {{ sendingComment ? $t('common.sending') : $t('common.publish') }}
                  </button>
                </div>
              </form>

              <p
                v-if="commentError"
                class="rounded-xl text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
                role="alert"
              >
                {{ commentError }}
              </p>
            </section>

            <!-- Acción inferior: forma parte del scroll del panel derecho -->
            <div class="pt-10 mt-10 border-t border-stone-200">
              <router-link
                v-if="post.author?.id"
                :to="`/profiles/${post.author.id}`"
                class="block w-full text-center rounded-full bg-gradient-to-r from-primary to-primary-container text-white py-4 px-8 font-manrope text-xs uppercase tracking-widest font-bold hover:opacity-90 transition-opacity active:scale-[0.98]"
              >
                {{ $t('postDetail.viewArtistPortfolio') }}
              </router-link>
            </div>
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
      return this.post?.author?.artistic_name || this.$t('common.artist')
    },

    authorUsername() {
      return this.formatUsername(this.post?.author?.username)
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
      this.commentError = ''

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
      return comment.author?.artistic_name || this.$t('common.artist')
    },

    getCommentAuthorUsername(comment) {
      return this.formatUsername(comment.author?.username)
    },

    formatUsername(username) {
      if (!username) {
        return '@amw'
      }

      return `@${String(username).replace(/^@/, '')}`
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
  },
}
</script>

<style scoped>
.post-detail-page,
.font-notoSerif,
.font-manrope {
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
    "SF Pro Display", "Roboto", "Helvetica Neue", Arial, sans-serif;
}

.post-detail-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.post-media-panel {
  position: relative;
  min-height: min(62vh, 620px);
  padding: 4.5rem 1rem 1.5rem;
  background-color: #f4f3f1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.post-back-button {
  position: absolute;
  top: 1.25rem;
  left: 1.25rem;
  z-index: 10;
  min-height: 42px;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0 1rem;
  border-radius: 9999px;
  background-color: rgba(255, 255, 255, 0.92);
  color: #1a1c1a;
  transition: color 0.2s ease, background-color 0.2s ease;
}

.post-back-button:hover {
  color: #a900a9;
  background-color: #ffffff;
}

.post-media-frame {
  width: min(calc(100% - 1rem), 520px);
  aspect-ratio: 1 / 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #000000;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 20px 48px rgba(0, 0, 0, 0.1);
}

.post-media-image {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  background-color: #000000;
}

.post-info-panel {
  background-color: #faf9f6;
  border-top: 1px solid #e7e5e4;
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
  border-radius: 9999px;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  white-space: nowrap;
  border: 0;
  clip: rect(0, 0, 0, 0);
}

/*
  Vista de escritorio:
  - La página queda anclada al viewport.
  - El panel izquierdo nunca forma parte del desplazamiento.
  - El único contenedor desplazable es el panel derecho.
*/
@media (min-width: 900px) {
  .post-detail-page {
    position: fixed;
    inset: 0;
    height: 100vh;
    height: 100dvh;
    min-height: 0;
    overflow: hidden;
  }

  .post-detail-main {
    height: 100vh;
    height: 100dvh;
    min-height: 0;
    overflow: hidden;
  }

  .post-detail-layout {
    height: 100%;
    min-height: 0;
    flex-direction: row;
    overflow: hidden;
  }

  .post-media-panel {
    width: 58%;
    height: 100%;
    min-height: 0;
    flex-shrink: 0;
    padding: 2rem;
    overflow: hidden;
    overscroll-behavior: none;
  }

  .post-media-frame {
    width: min(calc(100% - 2rem), 72vh, 700px);
  }

  .post-info-panel {
    width: 42%;
    height: 100%;
    min-height: 0;
    overflow-y: auto;
    overscroll-behavior-y: contain;
    border-top: none;
    border-left: 1px solid #e7e5e4;
  }
}

@media (max-width: 640px) {
  .post-media-panel {
    min-height: 420px;
    padding-top: 4.25rem;
  }

  .post-media-frame {
    width: min(calc(100% - 0.5rem), 360px);
  }
}
</style>
