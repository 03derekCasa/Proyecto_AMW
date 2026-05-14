<template>
  <div class="bg-surface text-on-surface min-h-screen">
    <!-- Top bar -->

    <AppTopBar
        :show-edit-button="true"
        :is-editing="isEditing"
        @toggle-edit="toggleEditForm"
        @logout="handleLogout"
    />

    <!-- Sidebar -->

    <AppSidebar />

    <!-- Main content -->
    <main class="ml-20 pt-32 min-h-screen">
      <!-- Header -->
      <header class="relative px-12 mb-32">
        <div class="w-full h-[512px] bg-surface-container-low relative overflow-hidden">
          <img
              class="w-full h-full object-cover grayscale opacity-70 mix-blend-multiply"
              :src="heroImage"
              alt="Imagen de cabecera"
          />
          <div class="absolute inset-0 bg-gradient-to-b from-transparent to-surface/80"></div>
        </div>

        <div class="absolute bottom-[-80px] left-24 flex items-end gap-12">
          <div class="relative">
            <div class="w-48 h-64 bg-stone-200 overflow-hidden border-8 border-surface shadow-2xl">
              <img
                  class="w-full h-full object-cover"
                  :src="userProfileImage"
                  alt="Imagen de perfil"
              />
            </div>
          </div>

          <div class="mb-8">
            <h2 class="font-notoSerif text-6xl italic text-on-surface mb-2 tracking-tight">
              {{ userName }}
            </h2>

            <p class="font-manrope text-sm text-stone-500 uppercase tracking-widest">
              {{ profile.specialty || 'Artista AMW' }}
            </p>

            <div class="flex gap-8 mt-6">
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

      <!-- Messages -->
      <section v-if="successMessage || errorMessage" class="px-24 mb-8">
        <p
            v-if="successMessage"
            class="text-green-700 bg-green-50 border border-green-200 px-4 py-3 font-manrope text-sm"
        >
          {{ successMessage }}
        </p>

        <p
            v-if="errorMessage"
            class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 font-manrope text-sm"
        >
          {{ errorMessage }}
        </p>
      </section>

      <!-- Hidden edit form -->
      <section v-if="isEditing" class="px-24 mb-32">
        <div class="bg-surface-container-low p-10 border border-outline-variant/30 shadow-xl">
          <div class="flex justify-between items-start mb-10">
            <div>
              <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-3">
                Configuración del perfil
              </p>

              <h3 class="font-notoSerif text-4xl italic text-on-surface">
                Editar perfil artístico
              </h3>
            </div>

            <button
                class="font-manrope text-[10px] uppercase tracking-widest text-stone-500 hover:text-primary transition-colors"
                type="button"
                @click="toggleEditForm"
            >
              Cerrar
            </button>
          </div>

          <form @submit.prevent="saveProfile" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-8">
              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Nombre artístico
                </label>

                <input
                    v-model="profile.artistic_name"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    required
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Especialidad
                </label>

                <input
                    v-model="profile.specialty"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="Arte digital, ilustración, pintura..."
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Biografía
                </label>

                <textarea
                    v-model="profile.biography"
                    class="w-full bg-transparent border border-outline-variant p-4 focus:ring-0 focus:border-primary font-manrope"
                    rows="7"
                    placeholder="Describe tu trayectoria artística, intereses y estilo..."
                ></textarea>
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Imagen de perfil
                </label>

                <input
                    type="file"
                    accept="image/*"
                    @change="handleImageChange"
                    class="font-manrope text-sm"
                />
              </div>
            </div>

            <div class="space-y-8">
              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Instagram
                </label>

                <input
                    v-model="profile.social_links.instagram"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://instagram.com/usuario"
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Behance
                </label>

                <input
                    v-model="profile.social_links.behance"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://behance.net/usuario"
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Web personal
                </label>

                <input
                    v-model="profile.social_links.website"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://miweb.com"
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  TikTok
                </label>

                <input
                    v-model="profile.social_links.tiktok"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://tiktok.com/@usuario"
                />
              </div>

              <div>
                <label class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  YouTube
                </label>

                <input
                    v-model="profile.social_links.youtube"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://youtube.com/@usuario"
                />
              </div>
            </div>

            <div class="lg:col-span-2 flex justify-end gap-4 pt-6 border-t border-outline-variant/30">
              <button
                  class="px-10 py-4 border border-outline-variant text-on-surface font-manrope font-bold uppercase tracking-widest text-xs hover:bg-surface transition-all"
                  type="button"
                  @click="toggleEditForm"
              >
                Cancelar
              </button>

              <button
                  class="px-12 py-4 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform disabled:opacity-60"
                  type="submit"
                  :disabled="loading"
              >
                {{ loading ? 'Guardando...' : 'Guardar cambios' }}
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Bio section -->
      <section class="px-24 mb-32 grid grid-cols-12 gap-12">
        <div class="col-span-4 sticky top-48 self-start">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-6">
            Descripción
          </p>

          <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
            "{{ profile.biography || 'Añade una biografía para presentar tu trabajo artístico dentro de AMW.' }}"
          </div>

          <div class="mt-12 space-y-8">
            <button
                class="w-full py-4 px-12 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10"
                type="button"
            >
              Contactar
            </button>

            <button
                class="w-full py-4 px-12 border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all"
                type="button"
                @click="toggleEditForm"
            >
              {{ isEditing ? 'Ocultar edición' : 'Editar perfil' }}
            </button>

            <div class="flex gap-4">
              <a
                  v-if="profile.social_links.website"
                  :href="profile.social_links.website"
                  target="_blank"
                  class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
              >
                language
              </a>

              <a
                  v-if="profile.social_links.instagram"
                  :href="profile.social_links.instagram"
                  target="_blank"
                  class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
              >
                share
              </a>
            </div>
          </div>
        </div>

        <!-- Gallery -->
        <div class="col-span-8">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">
            Obras seleccionadas
          </p>

          <div v-if="loading && artworks.length === 0" class="font-manrope text-sm text-stone-500">
            Cargando perfil...
          </div>

          <div v-else-if="artworks.length === 0" class="bg-surface-container-low p-12 border border-outline-variant/20">
            <p class="font-notoSerif text-2xl italic mb-4">
              Todavía no tienes obras publicadas.
            </p>

            <p class="font-manrope text-sm text-stone-500">
              Cuando crees publicaciones en AMW, aparecerán aquí como parte de tu portfolio.
            </p>
          </div>

          <div v-else class="grid grid-cols-2 gap-16">
            <div
                v-for="artwork in artworks"
                :key="artwork.id"
                class="space-y-6"
            >
              <div class="bg-surface-container aspect-[3/4] relative overflow-hidden group">
                <img
                    class="w-full h-full object-cover transition-all duration-700 cursor-pointer"
                    :src="artwork.image"
                    :alt="artwork.title"
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
            </div>
          </div>

          <div class="mt-32 flex justify-center">
            <button
                class="px-16 py-4 border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all"
                type="button"
            >
              Ver portfolio completo
            </button>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="px-24 py-32 border-t border-stone-200 bg-surface-container-low">
        <div class="flex justify-between items-start">
          <div class="max-w-md">
            <h4 class="font-notoSerif italic text-3xl mb-6">
              AMW
            </h4>

            <p class="font-manrope text-sm text-stone-500 leading-relaxed">
              Un espacio digital dedicado a la intersección entre las bellas artes y la cultura digital moderna.
              Celebramos al artista como arquitecto principal de la sociedad.
            </p>
          </div>
        </div>
      </footer>
    </main>
  </div>
</template>

<script>
import {
  getProfile,
  updateProfile,
  uploadProfileImage,
} from '@/services/profileService'

import { getMyPosts } from '@/services/postService'
import { logoutUser } from '@/services/authService'

export default {
  name: 'ProfilePage',

  data() {
    return {
      isEditing: false,
      loading: false,
      successMessage: '',
      errorMessage: '',

      profile: {
        artistic_name: '',
        specialty: '',
        biography: '',
        profile_image_url: '',
        social_links: {
          instagram: '',
          behance: '',
          website: '',
          tiktok: '',
          youtube: '',
        },
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
    await this.loadProfile()
    await this.loadMyPosts()
  },

  methods: {
    toggleEditForm() {
      this.isEditing = !this.isEditing
      this.successMessage = ''
      this.errorMessage = ''
    },

    async loadProfile() {
      this.loading = true
      this.errorMessage = ''

      try {
        const response = await getProfile()
        const data = response.data

        this.profile = {
          ...this.profile,
          ...data,
          social_links: {
            ...this.profile.social_links,
            ...(data.social_links || {}),
          },
        }

        this.userName = data.artistic_name || 'Artista AMW'
        this.userProfileImage = data.profile_image_url || this.userProfileImage
      } catch (error) {
        if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
          return
        }

        this.errorMessage = 'No se pudo cargar el perfil.'
      } finally {
        this.loading = false
      }
    },

    async loadMyPosts() {
      try {
        const response = await getMyPosts()

        let posts = []

        if (Array.isArray(response.data?.data)) {
          posts = response.data.data
        } else if (Array.isArray(response.data)) {
          posts = response.data
        } else if (Array.isArray(response.data?.data?.data)) {
          posts = response.data.data.data
        }

        this.artworks = posts.map((post) => ({
          id: post.id,
          image: post.image_url || 'https://placehold.co/600x800?text=AMW',
          title: post.title,
          year: post.created_at
              ? new Date(post.created_at).getFullYear()
              : new Date().getFullYear(),
          medium: post.category?.name || post.type || 'Obra',
        }))

        this.userWorksCount = this.artworks.length
      } catch (error) {
        this.artworks = []
        this.userWorksCount = 0
      }
    },

    async saveProfile() {
      this.loading = true
      this.successMessage = ''
      this.errorMessage = ''

      try {
        const response = await updateProfile({
          artistic_name: this.profile.artistic_name,
          specialty: this.profile.specialty,
          biography: this.profile.biography,
          social_links: this.profile.social_links,
        })

        const data = response.data

        this.profile = {
          ...this.profile,
          ...data,
          social_links: {
            ...this.profile.social_links,
            ...(data.social_links || {}),
          },
        }

        this.userName = data.artistic_name || this.userName
        this.successMessage = 'Perfil actualizado correctamente.'
        this.isEditing = false
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else {
          this.errorMessage = 'No se pudo actualizar el perfil.'
        }
      } finally {
        this.loading = false
      }
    },

    async handleImageChange(event) {
      const file = event.target.files[0]

      if (!file) {
        return
      }

      this.loading = true
      this.successMessage = ''
      this.errorMessage = ''

      try {
        const response = await uploadProfileImage(file)

        this.profile.profile_image_url = response.data.profile_image_url
        this.userProfileImage = response.data.profile_image_url
        this.successMessage = 'Imagen de perfil actualizada correctamente.'
      } catch (error) {
        this.errorMessage = 'No se pudo subir la imagen.'
      } finally {
        this.loading = false
      }
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
</style>