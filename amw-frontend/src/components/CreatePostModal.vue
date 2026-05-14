<template>
  <div
      v-if="modelValue"
      class="fixed inset-0 z-[300] bg-black/70 backdrop-blur-sm flex items-center justify-center px-6"
      role="dialog"
      aria-modal="true"
      aria-labelledby="create-post-title"
      @click.self="closeModal"
  >
    <!-- Botón cerrar -->
    <button
        type="button"
        class="fixed top-6 right-8 text-white hover:opacity-70 transition-opacity text-4xl leading-none"
        aria-label="Cerrar creación de post"
        @click="closeModal"
    >
      ×
    </button>

    <!-- Ventana modal -->
    <section
        class="w-[min(900px,92vw)] max-h-[82vh] bg-[#FAF9F6] rounded-2xl overflow-hidden shadow-2xl grid grid-cols-1 lg:grid-cols-[0.95fr_1.05fr]"
    >
      <!-- Columna izquierda: imagen -->
      <div class="bg-stone-100 h-[460px] lg:h-[500px] flex items-center justify-center relative overflow-hidden">
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
              :alt="form.alt_text || 'Vista previa de la imagen seleccionada'"
              class="w-full h-full object-contain bg-black"
          />

          <div v-else class="h-full w-full flex flex-col items-center justify-center text-center px-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
              <span class="material-symbols-outlined text-3xl text-primary" aria-hidden="true">
                add_photo_alternate
              </span>
            </div>

            <p class="font-manrope text-xs font-bold uppercase tracking-widest mt-5 text-stone-800">
              Seleccionar imagen
            </p>

            <p class="font-manrope text-[11px] text-stone-500 mt-2">
              JPG, PNG o WEBP. Máximo recomendado: 4 MB.
            </p>
          </div>
        </label>

        <button
            v-if="previewUrl"
            type="button"
            class="absolute bottom-5 left-1/2 -translate-x-1/2 bg-white/90 text-stone-900 px-4 py-2 rounded-full text-xs font-bold hover:bg-primary hover:text-white transition-colors"
            @click="openFilePicker"
        >
          Cambiar imagen
        </button>
      </div>

      <!-- Columna derecha: datos -->
      <form class="flex flex-col h-[460px] lg:h-[500px]" @submit.prevent="submitPost">
        <header class="px-6 py-4 border-b border-stone-200 flex items-center justify-between">
          <h1 id="create-post-title" class="font-manrope text-lg font-extrabold tracking-tight">
            Crear nuevo post
          </h1>

          <button
              type="submit"
              class="font-manrope text-sm font-bold text-primary hover:opacity-70 transition-opacity disabled:opacity-40"
              :disabled="loading"
          >
            {{ loading ? 'Subiendo...' : 'Compartir' }}
          </button>
        </header>

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
          <!-- Título -->
          <div>
            <label
                for="post-title"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              Título de la obra
            </label>

            <input
                id="post-title"
                v-model.trim="form.title"
                type="text"
                maxlength="120"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                placeholder="Ej. Fragmentos de luz"
                required
            />
          </div>

          <!-- Caption / descripción -->
          <div>
            <label
                for="post-caption"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              Caption / descripción
            </label>

            <textarea
                id="post-caption"
                v-model="form.caption"
                rows="4"
                maxlength="2200"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm resize-none focus:ring-0 focus:border-primary"
                placeholder="Explica la inspiración, el proceso creativo, la técnica utilizada o el contexto artístico de la obra..."
            ></textarea>

            <p class="text-right text-[10px] text-stone-400 mt-1">
              {{ form.caption.length }}/2200
            </p>
          </div>

          <!-- Texto alternativo -->
          <div>
            <label
                for="post-alt-text"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              Texto alternativo de la imagen
            </label>

            <input
                id="post-alt-text"
                v-model.trim="form.alt_text"
                type="text"
                maxlength="180"
                class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                placeholder="Describe brevemente lo que aparece en la imagen"
            />

            <p class="text-[10px] text-stone-400 mt-2">
              Recomendado para accesibilidad. Más adelante se puede guardar en backend.
            </p>
          </div>

          <!-- Hashtags -->
          <div>
            <label
                for="post-hashtag"
                class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
            >
              Hashtags / etiquetas
            </label>

            <div class="flex gap-2">
              <input
                  id="post-hashtag"
                  v-model="tagInput"
                  type="text"
                  class="flex-1 bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  placeholder="Ej. pintura, abstracto, zaragoza"
                  @keydown.enter.prevent="addTag"
              />

              <button
                  type="button"
                  class="px-4 rounded-xl bg-stone-900 text-white font-manrope text-xs font-bold hover:bg-primary transition-colors"
                  @click="addTag"
              >
                Añadir
              </button>
            </div>

            <div v-if="form.hashtags.length > 0" class="flex flex-wrap gap-2 mt-3">
              <button
                  v-for="tag in form.hashtags"
                  :key="tag"
                  type="button"
                  class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors"
                  @click="removeTag(tag)"
              >
                #{{ tag }}
              </button>
            </div>

            <p class="text-[10px] text-stone-400 mt-2">
              Pulsa Enter o “Añadir”. Máximo 10 etiquetas.
            </p>
          </div>

          <!-- Tipo y categoría -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label
                  for="post-type"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                Tipo
              </label>

              <select
                  id="post-type"
                  v-model="form.type"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
              >
                <option value="obra">Obra</option>
                <option value="evento">Evento</option>
                <option value="producto">Producto</option>
              </select>
            </div>

            <div>
              <label
                  for="post-category"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                Categoría
              </label>

              <select
                  id="post-category"
                  v-model="form.category_id"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
              >
                <option :value="null">Sin categoría</option>

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

          <!-- Técnica y año -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label
                  for="post-technique"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                Técnica artística
              </label>

              <input
                  id="post-technique"
                  v-model.trim="form.technique"
                  type="text"
                  maxlength="80"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  placeholder="Ej. óleo, digital, fotografía..."
              />
            </div>

            <div>
              <label
                  for="post-year"
                  class="block font-manrope text-[10px] uppercase tracking-widest text-stone-500 mb-2"
              >
                Año de creación
              </label>

              <input
                  id="post-year"
                  v-model="form.creation_year"
                  type="number"
                  min="1900"
                  :max="currentYear"
                  class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-primary"
                  placeholder="Ej. 2026"
              />
            </div>
          </div>

          <!-- Publicar -->
          <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-stone-200">
            <div>
              <p class="font-manrope text-sm font-bold">
                Publicar ahora
              </p>

              <p class="font-manrope text-xs text-stone-500">
                Si está activo, el post aparecerá publicado en AMW.
              </p>
            </div>

            <input
                id="post-is-published"
                name="is_published"
                v-model="form.is_published"
                type="checkbox"
                class="rounded border-stone-300 text-primary focus:ring-primary"
                aria-label="Publicar ahora"
            />
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
        is_published: true,
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
        is_published: true,
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
        this.errorMessage = 'El archivo seleccionado debe ser una imagen.'
        return
      }

      if (file.size > 4 * 1024 * 1024) {
        this.errorMessage = 'La imagen no debería superar los 4 MB.'
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
        this.errorMessage = 'Puedes añadir como máximo 10 hashtags.'
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
        this.errorMessage = 'Selecciona una imagen antes de publicar.'
        return
      }

      if (!this.form.title.trim()) {
        this.errorMessage = 'El título es obligatorio.'
        return
      }

      if (
          this.form.creation_year &&
          Number(this.form.creation_year) > this.currentYear
      ) {
        this.errorMessage = 'El año de creación no puede ser posterior al año actual.'
        return
      }

      this.loading = true

      try {
        const imageUrl = await uploadPostImage(this.selectedFile)

        const postPayload = {
          title: this.form.title.trim(),
          description: this.buildDescription(),
          image_url: imageUrl,
          type: this.form.type,
          category_id: this.form.category_id,
          is_published: this.form.is_published,
          hashtags: this.form.hashtags,
        }

        const post = await createPost(postPayload)

        this.$emit('created', post)
        window.dispatchEvent(new CustomEvent('amw-post-created', { detail: post }))

        this.resetForm()
        this.$emit('update:modelValue', false)
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else {
          this.errorMessage = 'No se pudo crear el post.'
        }
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
        parts.push(`Técnica: ${this.form.technique.trim()}`)
      }

      if (this.form.creation_year) {
        parts.push(`Año de creación: ${this.form.creation_year}`)
      }

      if (this.form.alt_text.trim()) {
        parts.push(`Texto alternativo: ${this.form.alt_text.trim()}`)
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