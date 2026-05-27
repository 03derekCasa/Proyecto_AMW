<template>
  <div
      v-if="modelValue"
      class="fixed inset-0 z-[400] bg-black/70 backdrop-blur-sm flex items-center justify-center px-4 py-4 sm:py-6 overflow-y-auto"
      role="dialog"
      aria-modal="true"
      :aria-label="ui.title"
      @click.self="closeModal"
  >
    <section
        class="w-[min(780px,94vw)] max-h-[92vh] bg-[#FAF9F6] rounded-2xl overflow-hidden shadow-2xl flex flex-col"
    >
      <!-- Cabecera más compacta -->
      <header class="shrink-0 flex items-start justify-between gap-4 px-5 py-4 border-b border-stone-200">
        <div>
          <h2 class="text-base sm:text-lg font-bold">
            {{ ui.title }}
          </h2>

          <p class="text-[11px] sm:text-xs text-stone-500 mt-1 max-w-xl">
            {{ ui.help }}
          </p>
        </div>

        <button
            type="button"
            class="text-3xl leading-none text-stone-500 hover:text-primary transition-colors shrink-0"
            :aria-label="ui.close"
            @click="closeModal"
        >
          ×
        </button>
      </header>

      <!-- Zona de recorte reducida -->
      <div class="bg-stone-950 p-3 sm:p-4 flex-1 min-h-0">
        <div class="w-full h-[200px] sm:h-[270px] lg:h-[315px] max-h-[46vh] overflow-hidden">
          <img
              ref="cropImage"
              :src="imageSrc"
              :alt="ui.imageAlt"
              class="max-w-full block"
          />
        </div>
      </div>

      <!-- Acciones inferiores compactas -->
      <footer
          class="shrink-0 px-5 py-4 border-t border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
      >
        <p class="text-[11px] text-stone-500">
          {{ ui.controls }}
        </p>

        <div class="flex gap-3 justify-end">
          <button
              type="button"
              class="px-5 py-2.5 border border-stone-300 rounded-full text-[10px] font-bold uppercase tracking-widest text-stone-600 hover:border-primary hover:text-primary transition-colors"
              @click="closeModal"
          >
            {{ ui.cancel }}
          </button>

          <button
              type="button"
              class="px-5 py-2.5 rounded-full bg-primary text-white text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-opacity"
              @click="confirmCrop"
          >
            {{ ui.apply }}
          </button>
        </div>
      </footer>
    </section>
  </div>
</template>

<script>
import Cropper from 'cropperjs'
import 'cropperjs/dist/cropper.css'

const texts = {
  es: {
    title: 'Ajustar imagen de cabecera',
    help: 'Arrastra la imagen y utiliza la rueda del ratón o los dedos para ajustar el encuadre.',
    close: 'Cerrar editor de cabecera',
    imageAlt: 'Imagen seleccionada para ajustar como cabecera',
    controls: 'La portada se guardará en formato panorámico.',
    cancel: 'Cancelar',
    apply: 'Usar imagen',
  },

  en: {
    title: 'Adjust cover image',
    help: 'Drag the image and use the mouse wheel or your fingers to adjust the framing.',
    close: 'Close cover editor',
    imageAlt: 'Selected image to adjust as profile cover',
    controls: 'The cover will be saved in panoramic format.',
    cancel: 'Cancel',
    apply: 'Use image',
  },

  fr: {
    title: "Ajuster l'image de couverture",
    help: "Faites glisser l'image et utilisez la molette ou vos doigts pour ajuster le cadrage.",
    close: "Fermer l'éditeur de couverture",
    imageAlt: 'Image sélectionnée pour la couverture du profil',
    controls: 'La couverture sera enregistrée au format panoramique.',
    cancel: 'Annuler',
    apply: "Utiliser l'image",
  },
}

export default {
  name: 'CoverImageCropModal',

  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },

    imageSrc: {
      type: String,
      default: '',
    },
  },

  emits: ['update:modelValue', 'cropped'],

  data() {
    return {
      cropper: null,
    }
  },

  computed: {
    ui() {
      return texts[this.$i18n.locale] || texts.es
    },
  },

  watch: {
    modelValue(isOpen) {
      if (isOpen) {
        this.$nextTick(() => {
          this.createCropper()
        })
      } else {
        this.destroyCropper()
      }
    },

    imageSrc(newImageSrc) {
      if (this.cropper && newImageSrc) {
        this.cropper.replace(newImageSrc)
      }
    },
  },

  beforeUnmount() {
    this.destroyCropper()
  },

  methods: {
    createCropper() {
      this.destroyCropper()

      if (!this.$refs.cropImage || !this.imageSrc) {
        return
      }

      this.cropper = new Cropper(this.$refs.cropImage, {
        aspectRatio: 3 / 1,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        responsive: true,
        background: false,
        modal: true,
        guides: false,
        center: true,
        movable: true,
        zoomable: true,
        zoomOnTouch: true,
        zoomOnWheel: true,
        cropBoxMovable: false,
        cropBoxResizable: false,
        toggleDragModeOnDblclick: false,
        rotatable: false,
        scalable: false,
      })
    },

    destroyCropper() {
      if (this.cropper) {
        this.cropper.destroy()
        this.cropper = null
      }
    },

    closeModal() {
      this.$emit('update:modelValue', false)
    },

    confirmCrop() {
      if (!this.cropper) {
        return
      }

      const canvas = this.cropper.getCroppedCanvas({
        width: 1500,
        height: 500,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      })

      canvas.toBlob(
          (blob) => {
            if (!blob) {
              return
            }

            const file = new File(
                [blob],
                `cover-${Date.now()}.jpg`,
                {
                  type: 'image/jpeg',
                  lastModified: Date.now(),
                }
            )

            const previewUrl = URL.createObjectURL(blob)

            this.$emit('cropped', {
              file,
              previewUrl,
            })

            this.$emit('update:modelValue', false)
          },
          'image/jpeg',
          0.9
      )
    },
  },
}
</script>

<style scoped>
:deep(.cropper-view-box),
:deep(.cropper-face) {
  border-radius: 0;
}

:deep(.cropper-line),
:deep(.cropper-point) {
  background-color: #a900a9;
}

:deep(.cropper-view-box) {
  outline-color: #a900a9;
}
</style>