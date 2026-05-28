<template>
  <div
      v-if="modelValue"
      class="fixed inset-0 z-[300] bg-black/70 backdrop-blur-sm flex items-center justify-center px-4 py-4 sm:px-6 sm:py-6"
      role="dialog"
      aria-modal="true"
      aria-labelledby="create-post-title"
      @click.self="closeModal"
  >
    <button
        type="button"
        class="fixed top-5 right-6 sm:top-6 sm:right-8 text-white hover:opacity-70 transition-opacity text-4xl leading-none"
        :aria-label="$t('postModal.close')"
        @click="closeModal"
    >
      ×
    </button>

    <section
        class="create-post-panel w-[min(900px,92vw)] bg-[#FAF9F6] rounded-2xl shadow-2xl grid grid-cols-1 lg:grid-cols-[0.95fr_1.05fr]"
    >
      <div class="create-post-media bg-stone-100 flex items-center justify-center relative overflow-hidden">
        <label
            for="post-image"
            class="w-full h-full flex items-center justify-center cursor-pointer group"
        >
          <input
              id="post-image"
              ref="fileInput"
              class="sr-only"
              type="file"
              accept="image/png,image/jpeg,image/jpg,image/webp"
              @change="handleFileChange"
          />

          <img
              v-if="previewUrl"
              :src="previewUrl"
              :alt="form.alt_text || $t('postModal.imagePreview')"
              class="w-full h-full object-contain bg-black"
          />

          <div
              v-else
              class="h-full w-full flex flex-col items-center justify-center text-center px-8"
          >
            <div
                class="w-16 h-16 mx-auto rounded-full bg-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform"
            >
              <span
                  class="material-symbols-outlined text-3xl text-primary"
                  aria-hidden="true"
              >
                add_photo_alternate
              </span>
            </div>

            <p class="font-manrope text-xs font-bold uppercase tracking-widest mt-5 text-stone-800">
              {{ $t('postModal.selectImage') }}
            </p>

            <p class="font-manrope text-[11px] text-stone-500 mt-2">
              {{ $t('postModal.imageHelp') }}
            </p>
          </div>
        </label>

        <button
            v-if="previewUrl"
            type="button"
            class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-white/90 text-stone-900 px-4 py-2 rounded-full text-xs font-bold hover:bg-primary hover:text-white transition-colors"
            @click="openFilePicker"
        >
          {{ $t('postModal.changeImage') }}
        </button>
      </div>

      <form class="create-post-form flex flex-col min-h-0" @submit.prevent="submitPost">
        <header class="shrink-0 px-6 py-4 border-b border-stone-200 flex items-center justify-between">
          <h1 id="create-post-title" class="font-manrope text-lg font-extrabold tracking-tight">
            {{ $t('postModal.create') }}
          </h1>

          <button
              type="submit"
              class="font-manrope text-sm font-bold text-primary hover:opacity-70 transition-opacity disabled:opacity-40"
              :disabled="loading"
          >
            {{ loading ? $t('postModal.uploading') : $t('postModal.share') }}
          </button>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 pt-5 pb-8 space-y-4 custom-scrollbar">
          <div>
            <label
                for="post-title"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              {{ $t('postModal.title') }}
            </label>

            <input
                id="post-title"
                name="title"
                v-model.trim="form.title"
                type="text"
                maxlength="120"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                :placeholder="$t('postModal.titlePlaceholder')"
                required
            />
          </div>

          <div>
            <label
                for="post-caption"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              {{ $t('postModal.caption') }}
            </label>

            <textarea
                id="post-caption"
                name="description"
                v-model="form.caption"
                rows="4"
                maxlength="2200"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm resize-none focus:ring-0 focus:border-primary"
                :placeholder="$t('postModal.captionPlaceholder')"
            ></textarea>

            <p class="text-right text-[10px] text-stone-400 mt-1">
              {{ form.caption.length }}/2200
            </p>
          </div>

          <div>
            <label
                for="post-alt-text"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              {{ $t('postModal.altText') }}
            </label>

            <input
                id="post-alt-text"
                name="alt_text"
                v-model.trim="form.alt_text"
                type="text"
                maxlength="180"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                :placeholder="$t('postModal.altPlaceholder')"
            />

            <p class="text-[10px] text-stone-400 mt-2">
              {{ $t('postModal.altHelp') }}
            </p>
          </div>

          <div>
            <label
                for="post-hashtag"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              {{ $t('postModal.hashtags') }}
            </label>

            <div class="flex gap-2">
              <input
                  id="post-hashtag"
                  name="hashtag"
                  v-model="tagInput"
                  type="text"
                  class="flex-1 bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  :placeholder="$t('postModal.hashtagPlaceholder')"
                  @keydown.enter.prevent="addTag"
              />

              <button
                  type="button"
                  class="px-4 rounded-xl bg-stone-900 text-white font-manrope text-xs font-bold hover:bg-primary transition-colors"
                  @click="addTag"
              >
                {{ $t('postModal.add') }}
              </button>
            </div>

            <div v-if="form.hashtags.length > 0" class="flex flex-wrap gap-2 mt-3">
              <button
                  v-for="tag in form.hashtags"
                  :key="tag"
                  type="button"
                  class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors"
                  :aria-label="`${$t('common.close')} #${tag}`"
                  @click="removeTag(tag)"
              >
                #{{ tag }}
              </button>
            </div>

            <p class="text-[10px] text-stone-400 mt-2">
              {{ $t('postModal.hashtagHelp') }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label
                  for="post-type"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ $t('postModal.type') }}
              </label>

              <select
                  id="post-type"
                  name="type"
                  v-model="form.type"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
              >
                <option value="obra">{{ $t('postModal.artwork') }}</option>
                <option value="evento">{{ $t('postModal.event') }}</option>
                <option value="producto">{{ $t('postModal.product') }}</option>
              </select>
            </div>

            <div>
              <label
                  for="post-category"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ $t('postModal.category') }}
              </label>

              <select
                  id="post-category"
                  name="category_id"
                  v-model="form.category_id"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
              >
                <option :value="null">{{ $t('postModal.noCategory') }}</option>

                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label
                  for="post-technique"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ $t('postModal.technique') }}
              </label>

              <input
                  id="post-technique"
                  name="technique"
                  v-model.trim="form.technique"
                  type="text"
                  maxlength="80"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  :placeholder="$t('postModal.techniquePlaceholder')"
              />
            </div>

            <div>
              <label
                  for="post-year"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ $t('postModal.year') }}
              </label>

              <input
                  id="post-year"
                  name="creation_year"
                  v-model="form.creation_year"
                  type="number"
                  min="1900"
                  :max="currentYear"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  :placeholder="$t('postModal.yearPlaceholder')"
              />
            </div>
          </div>

          <p
              v-if="errorMessage"
              class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm rounded-xl"
              role="alert"
          >
            {{ errorMessage }}
          </p>
        </div>
      </form>
    </section>
  </div>
</template>

<script>
import {
  createPost,
  uploadPostImage,
} from '@/services/postService'
import { getCategories } from '@/services/categoryService'

export default {
  name: 'CreatePostModal',

  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
  },

  emits: ['update:modelValue', 'created'],

  data() {
    return {
      loading: false,
      errorMessage: '',
      previewUrl: '',
      selectedFile: null,
      tagInput: '',
      categories: [],
      currentYear: new Date().getFullYear(),

      form: {
        title: '',
        caption: '',
        alt_text: '',
        hashtags: [],
        type: 'obra',
        category_id: null,
        technique: '',
        creation_year: '',
      },
    }
  },

  watch: {
    async modelValue(isOpen) {
      if (isOpen) {
        await this.loadCategories()
      }
    },
  },

  mounted() {
    window.addEventListener('keydown', this.handleKeydown)
  },

  beforeUnmount() {
    window.removeEventListener('keydown', this.handleKeydown)
    this.clearPreview()
  },

  methods: {
    handleKeydown(event) {
      if (event.key === 'Escape' && this.modelValue) {
        this.closeModal()
      }
    },

    openFilePicker() {
      this.$refs.fileInput.click()
    },

    closeModal() {
      if (this.loading) {
        return
      }

      this.$emit('update:modelValue', false)
    },

    clearPreview() {
      if (this.previewUrl) {
        URL.revokeObjectURL(this.previewUrl)
        this.previewUrl = ''
      }
    },

    resetForm() {
      this.clearPreview()

      this.loading = false
      this.errorMessage = ''
      this.selectedFile = null
      this.tagInput = ''

      this.form = {
        title: '',
        caption: '',
        alt_text: '',
        hashtags: [],
        type: 'obra',
        category_id: null,
        technique: '',
        creation_year: '',
      }

      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
    },

    handleFileChange(event) {
      const file = event.target.files[0]

      if (!file) {
        return
      }

      if (!file.type.startsWith('image/')) {
        this.errorMessage = this.$t('postModal.errors.invalidImage')
        return
      }

      if (file.size > 4 * 1024 * 1024) {
        this.errorMessage = this.$t('postModal.errors.maximumSize')
        return
      }

      this.clearPreview()

      this.selectedFile = file
      this.previewUrl = URL.createObjectURL(file)
      this.errorMessage = ''
    },

    addTag() {
      const normalizedTag = this.tagInput
          .trim()
          .replace(/^#/, '')
          .replace(/[^\p{L}\p{N}_-]/gu, '')
          .toLowerCase()

      if (!normalizedTag) {
        return
      }

      if (this.form.hashtags.includes(normalizedTag)) {
        this.tagInput = ''
        return
      }

      if (this.form.hashtags.length >= 10) {
        this.errorMessage = this.$t('postModal.errors.maximumTags')
        return
      }

      this.form.hashtags.push(normalizedTag)
      this.tagInput = ''
      this.errorMessage = ''
    },

    removeTag(tag) {
      this.form.hashtags = this.form.hashtags.filter((item) => item !== tag)
    },

    async loadCategories() {
      try {
        this.categories = await getCategories()
      } catch (error) {
        this.categories = []
      }
    },

    async submitPost() {
      this.errorMessage = ''

      if (!this.selectedFile) {
        this.errorMessage = this.$t('postModal.errors.selectImage')
        return
      }

      if (!this.form.title.trim()) {
        this.errorMessage = this.$t('postModal.errors.requiredTitle')
        return
      }

      if (
          this.form.creation_year &&
          Number(this.form.creation_year) > this.currentYear
      ) {
        this.errorMessage = this.$t('postModal.errors.invalidYear')
        return
      }

      this.loading = true

      try {
        const uploadedImage = await uploadPostImage(this.selectedFile)

        const postPayload = {
          title: this.form.title.trim(),
          description: this.buildDescription(),
          image_url: uploadedImage.url,
          image_public_id: uploadedImage.publicId,
          type: this.form.type,
          category_id: this.form.category_id,
          is_published: true,
          hashtags: this.form.hashtags,
        }

        const post = await createPost(postPayload)

        /*
         * El evento global se lanza en AppSidebar.vue mediante @created.
         * No se dispara aquí para evitar recargar el Feed dos veces.
         */
        this.$emit('created', post)

        // Cierra el modal justo después de crear correctamente la publicación.
        this.$emit('update:modelValue', false)
        this.resetForm()
      } catch (error) {
        this.errorMessage = this.$t('postModal.errors.create')
      } finally {
        this.loading = false
      }
    },

    buildDescription() {
      const parts = []

      if (this.form.caption.trim()) {
        parts.push(this.form.caption.trim())
      }

      if (this.form.technique.trim()) {
        parts.push(`${this.$t('postModal.savedTechnique')}: ${this.form.technique.trim()}`)
      }

      if (this.form.creation_year) {
        parts.push(`${this.$t('postModal.savedYear')}: ${this.form.creation_year}`)
      }

      if (this.form.alt_text.trim()) {
        parts.push(`${this.$t('postModal.savedAltText')}: ${this.form.alt_text.trim()}`)
      }

      if (this.form.hashtags.length > 0) {
        parts.push(this.form.hashtags.map((tag) => `#${tag}`).join(' '))
      }

      return parts.join('\n\n')
    },
  },
}
</script>

<style scoped>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/*
 * En móvil el panel completo puede desplazarse.
 * En escritorio, la ventana queda limitada al viewport y solo se desplazan
 * los campos del formulario derecho, por lo que siempre se alcanza el final.
 */
.create-post-panel {
  max-height: calc(100vh - 2rem);
  max-height: calc(100dvh - 2rem);
  overflow-y: auto;
}

.create-post-media {
  height: 270px;
  min-height: 270px;
}

.create-post-form {
  min-height: 500px;
}

@media (min-width: 1024px) {
  .create-post-panel {
    height: min(680px, calc(100vh - 3rem));
    height: min(680px, calc(100dvh - 3rem));
    max-height: none;
    overflow: hidden;
  }

  .create-post-media {
    height: 100%;
    min-height: 0;
  }

  .create-post-form {
    height: 100%;
    min-height: 0;
    overflow: hidden;
  }
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e3e2e0;
  border-radius: 10px;
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
</style>