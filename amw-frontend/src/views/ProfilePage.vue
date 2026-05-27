<template>
  <div class="profile-page bg-surface text-on-surface min-h-screen">
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
        <div class="w-full h-[512px] bg-surface-container-low relative overflow-hidden rounded-3xl">
          <img
              class="w-full h-full object-cover rounded-3xl grayscale opacity-70 mix-blend-multiply"
              :src="displayCoverImage"
              :alt="ui.headerImage"
          />
          <div class="absolute inset-0 rounded-3xl bg-gradient-to-b from-transparent to-surface/80"></div>
        </div>

        <div class="absolute bottom-[-80px] left-24 flex items-end gap-12">
          <div class="relative">
            <div class="w-48 h-64 bg-stone-200 overflow-hidden rounded-2xl border-8 border-surface shadow-2xl">
              <img
                  class="w-full h-full object-cover rounded-xl"
                  :src="displayProfileImage"
                  :alt="ui.profileImage"
              />
            </div>
          </div>

          <div class="mb-8">
            <h2 class="font-notoSerif text-6xl italic text-on-surface mb-2 tracking-tight">
              {{ userName }}
            </h2>

            <p class="font-manrope text-sm text-primary font-medium mb-2">
              {{ formattedUsername }}
            </p>

            <p class="font-manrope text-sm text-stone-500 uppercase tracking-widest">
              {{ profile.specialty || ui.artist }}
            </p>

            <div class="flex gap-8 mt-6">
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

      <!-- Messages -->
      <section v-if="successMessage || errorMessage" class="px-24 mb-8">
        <p
            v-if="successMessage"
            class="text-green-700 bg-green-50 border border-green-200 rounded-xl px-4 py-3 font-manrope text-sm"
        >
          {{ successMessage }}
        </p>

        <p
            v-if="errorMessage"
            class="text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 font-manrope text-sm"
        >
          {{ errorMessage }}
        </p>
      </section>

      <!-- Hidden edit form -->
      <section v-if="isEditing" class="px-24 mb-32">
        <div class="bg-surface-container-low p-10 border border-outline-variant/30 rounded-2xl shadow-xl">
          <div class="flex justify-between items-start mb-10">
            <div>
              <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-3">
                {{ ui.profileSettings }}
              </p>

              <h3 class="font-notoSerif text-4xl italic text-on-surface">
                {{ ui.editArtisticProfile }}
              </h3>
            </div>

            <button
                class="px-4 py-2 rounded-full font-manrope text-[10px] uppercase tracking-widest text-stone-500 hover:text-primary hover:bg-surface transition-colors"
                type="button"
                @click="toggleEditForm"
            >
              {{ ui.close }}
            </button>
          </div>

          <form @submit.prevent="saveProfile" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-8">
              <div>
                <label for="profile-artistic-name" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.artisticName }}
                </label>

                <input
                    id="profile-artistic-name"
                    name="artistic_name"
                    v-model="profile.artistic_name"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    required
                />
              </div>

              <div>
                <label for="profile-specialty" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.specialty }}
                </label>

                <input
                    id="profile-specialty"
                    name="specialty"
                    v-model="profile.specialty"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    :placeholder="ui.specialtyPlaceholder"
                />
              </div>

              <div>
                <label for="profile-biography" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.biography }}
                </label>

                <textarea
                    id="profile-biography"
                    name="biography"
                    v-model="profile.biography"
                    class="w-full bg-transparent border border-outline-variant p-4 focus:ring-0 focus:border-primary font-manrope"
                    rows="7"
                    :placeholder="ui.biographyPlaceholder"
                ></textarea>
              </div>

              <div>
                <label for="profile-image" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.profileImageLabel }}
                </label>

                <div class="flex items-center gap-4 mb-3">
                  <img
                      class="w-16 h-16 rounded-full object-cover border border-outline-variant/30"
                      :src="displayProfileImage"
                      :alt="ui.profileImage"
                  />

                  <div class="font-manrope text-xs text-stone-500">
                    <p>{{ ui.profileImageHelp }}</p>
                    <p v-if="selectedProfileImageFile" class="mt-1 text-primary font-bold">
                      {{ ui.previewReady }}
                    </p>
                  </div>
                </div>

                <input
                    id="profile-image"
                    ref="profileImageInput"
                    name="profile_image"
                    type="file"
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    @change="handleProfileImageSelection"
                    class="font-manrope text-sm"
                />
              </div>

              <div>
                <label for="cover-image" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.coverImageLabel }}
                </label>

                <div class="mb-3 overflow-hidden rounded-xl border border-outline-variant/30 bg-surface">
                  <img
                      class="w-full h-28 object-cover rounded-xl"
                      :src="displayCoverImage"
                      :alt="ui.headerImage"
                  />
                </div>

                <p class="font-manrope text-xs text-stone-500 mb-3">
                  {{ ui.coverImageHelp }}
                  <span v-if="selectedCoverImageFile" class="block mt-1 text-primary font-bold">
                    {{ ui.previewReady }}
                  </span>
                </p>

                <input
                    id="cover-image"
                    ref="coverImageInput"
                    name="cover_image"
                    type="file"
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    @change="handleCoverImageSelection"
                    class="font-manrope text-sm"
                />
              </div>
            </div>

            <div class="space-y-8">
              <div>
                <label for="profile-instagram" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Instagram
                </label>

                <input
                    id="profile-instagram"
                    name="instagram"
                    v-model="profile.social_links.instagram"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://instagram.com/usuario"
                />
              </div>

              <div>
                <label for="profile-behance" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  Behance
                </label>

                <input
                    id="profile-behance"
                    name="behance"
                    v-model="profile.social_links.behance"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://behance.net/usuario"
                />
              </div>

              <div>
                <label for="profile-website" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  {{ ui.website }}
                </label>

                <input
                    id="profile-website"
                    name="website"
                    v-model="profile.social_links.website"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://miweb.com"
                />
              </div>

              <div>
                <label for="profile-tiktok" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  TikTok
                </label>

                <input
                    id="profile-tiktok"
                    name="tiktok"
                    v-model="profile.social_links.tiktok"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://tiktok.com/@usuario"
                />
              </div>

              <div>
                <label for="profile-youtube" class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400 block mb-2">
                  YouTube
                </label>

                <input
                    id="profile-youtube"
                    name="youtube"
                    v-model="profile.social_links.youtube"
                    class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary font-manrope"
                    type="text"
                    placeholder="https://youtube.com/@usuario"
                />
              </div>
            </div>

            <div class="lg:col-span-2 flex justify-end gap-4 pt-6 border-t border-outline-variant/30">
              <button
                  class="px-10 py-4 rounded-full border border-outline-variant text-on-surface font-manrope font-bold uppercase tracking-widest text-xs hover:bg-surface transition-all"
                  type="button"
                  @click="toggleEditForm"
              >
                {{ ui.cancel }}
              </button>

              <button
                  class="px-12 py-4 rounded-full bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform disabled:opacity-60"
                  type="submit"
                  :disabled="loading"
              >
                {{ loading ? ui.saving : ui.saveChanges }}
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- Bio section -->
      <section class="px-24 mb-32 grid grid-cols-12 gap-12">
        <div class="col-span-4 sticky top-48 self-start">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-6">
            {{ ui.description }}
          </p>

          <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
            "{{ profile.biography || ui.emptyBiography }}"
          </div>

          <div class="mt-12 space-y-8">
            <router-link
                to="/messages"
                class="block w-full text-center py-4 px-12 rounded-full bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10"
            >
              {{ ui.messages }}
            </router-link>

            <button
                class="w-full py-4 px-12 rounded-full border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all"
                type="button"
                @click="toggleEditForm"
            >
              {{ isEditing ? ui.hideEditing : ui.editProfile }}
            </button>

            <div class="flex gap-4">
              <a
                  v-if="profile.social_links.website"
                  :href="profile.social_links.website"
                  target="_blank"
                  rel="noopener noreferrer"
                  :aria-label="ui.website"
                  class="material-symbols-outlined p-2 rounded-full border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
              >
                language
              </a>

              <a
                  v-if="profile.social_links.instagram"
                  :href="profile.social_links.instagram"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Instagram"
                  class="material-symbols-outlined p-2 rounded-full border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors"
              >
                share
              </a>
            </div>
          </div>
        </div>

        <!-- Gallery -->
        <div class="col-span-8">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">
            {{ ui.selectedWorks }}
          </p>

          <div v-if="loading && artworks.length === 0" class="font-manrope text-sm text-stone-500">
            {{ ui.loadingProfile }}
          </div>

          <div v-else-if="artworks.length === 0" class="bg-surface-container-low p-12 border border-outline-variant/20 rounded-2xl">
            <p class="font-notoSerif text-2xl italic mb-4">
              {{ ui.emptyWorksTitle }}
            </p>

            <p class="font-manrope text-sm text-stone-500">
              {{ ui.emptyWorksText }}
            </p>
          </div>

          <div v-else class="grid grid-cols-2 gap-16">
            <article
                v-for="artwork in artworks"
                :key="artwork.id"
                class="space-y-6"
            >
              <button
                  type="button"
                  class="block w-full bg-surface-container aspect-[3/4] relative overflow-hidden rounded-2xl group"
                  :aria-label="`${ui.viewArtwork}: ${artwork.title}`"
                  @click="goToPost(artwork.id)"
              >
                <img
                    class="w-full h-full object-cover rounded-2xl transition-transform duration-700 cursor-pointer group-hover:scale-105"
                    :src="artwork.image"
                    :alt="artwork.title"
                />

                <div class="absolute inset-0 rounded-2xl bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="font-manrope text-white uppercase tracking-widest text-[10px] border border-white rounded-full px-4 py-2">
                    {{ ui.viewArtwork }}
                  </span>
                </div>
              </button>

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
                class="inline-block px-16 py-4 rounded-full border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all"
            >
              {{ ui.viewFullPortfolio }}
            </router-link>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="profile-footer" :aria-label="ui.footerLabel">
        <nav class="profile-footer__links" :aria-label="ui.footerNavigation">
          <a
            href="tel:+34976123456"
            class="profile-footer__link"
            :aria-label="ui.contactPhoneLabel"
          >
            {{ ui.contactPhone }}
          </a>

          <router-link
            to="/terms"
            class="profile-footer__link"
          >
            {{ ui.terms }}
          </router-link>

          <router-link
            to="/help"
            class="profile-footer__link"
          >
            {{ ui.help }}
          </router-link>
        </nav>

        <p class="profile-footer__copyright">
          {{ ui.copyright }}
        </p>
      </footer>
    </main>

    <CoverImageCropModal
        v-model="showCoverCropModal"
        :image-src="coverOriginalPreviewUrl"
        @cropped="handleCroppedCover"
    />
  </div>
</template>

<script>
import {
  getProfile,
  updateProfile,
  uploadProfileImage,
  uploadCoverImage,
} from '@/services/profileService'

import { getMyPosts } from '@/services/postService'
import { logoutUser } from '@/services/authService'
import CoverImageCropModal from '@/components/CoverImageCropModal.vue'

const profileTexts = {
  es: {
    headerImage: 'Imagen de cabecera del portfolio',
    profileImage: 'Imagen de perfil',
    artist: 'Artista AMW',
    works: 'Obras',
    followers: 'Seguidores',
    following: 'Siguiendo',
    profileSettings: 'Configuración del perfil',
    editArtisticProfile: 'Editar perfil artístico',
    close: 'Cerrar',
    artisticName: 'Nombre artístico',
    specialty: 'Especialidad',
    specialtyPlaceholder: 'Arte digital, ilustración, pintura...',
    biography: 'Biografía',
    biographyPlaceholder: 'Describe tu trayectoria artística, intereses y estilo...',
    profileImageLabel: 'Imagen de perfil',
    profileImageHelp: 'JPG, PNG o WEBP. Máximo 2 MB.',
    coverImageLabel: 'Imagen de cabecera',
    coverImageHelp: 'Selecciona una imagen y ajusta el encuadre antes de guardarla. La portada final será panorámica.',
    previewReady: 'Previsualización pendiente de guardar.',
    website: 'Web personal',
    cancel: 'Cancelar',
    saving: 'Guardando...',
    saveChanges: 'Guardar cambios',
    description: 'Descripción',
    emptyBiography: 'Añade una biografía para presentar tu trabajo artístico dentro de AMW.',
    messages: 'Mensajes',
    hideEditing: 'Ocultar edición',
    editProfile: 'Editar perfil',
    selectedWorks: 'Obras seleccionadas',
    loadingProfile: 'Cargando perfil...',
    emptyWorksTitle: 'Todavía no tienes obras publicadas.',
    emptyWorksText: 'Cuando crees publicaciones en AMW, aparecerán aquí como parte de tu portfolio.',
    viewArtwork: 'Ver obra',
    viewFullPortfolio: 'Ver más publicaciones',
    artwork: 'Obra',
    footerLabel: 'Pie de página de AMW',
    footerNavigation: 'Enlaces de información y soporte',
    contactPhone: 'Contacto: +34 976 123 456',
    contactPhoneLabel: 'Llamar al teléfono de contacto de AMW',
    terms: 'Términos',
    help: 'Ayuda',
    copyright: '© 2026 AMW · Art Makes A Way',
    profileLoadedError: 'No se pudo cargar el perfil.',
    profileUpdated: 'Perfil actualizado correctamente.',
    profileUpdateError: 'No se pudo actualizar el perfil.',
    imageUpdated: 'Imagen de perfil actualizada correctamente.',
    imageError: 'No se pudo subir la imagen.',
    invalidImage: 'El archivo seleccionado debe ser una imagen JPG, PNG o WEBP.',
    profileImageTooLarge: 'La imagen de perfil no puede superar los 2 MB.',
    coverImageTooLarge: 'La imagen original de cabecera no puede superar los 12 MB.',
  },
  en: {
    headerImage: 'Portfolio header image',
    profileImage: 'Profile image',
    artist: 'AMW Artist',
    works: 'Artworks',
    followers: 'Followers',
    following: 'Following',
    profileSettings: 'Profile settings',
    editArtisticProfile: 'Edit artistic profile',
    close: 'Close',
    artisticName: 'Artistic name',
    specialty: 'Specialty',
    specialtyPlaceholder: 'Digital art, illustration, painting...',
    biography: 'Biography',
    biographyPlaceholder: 'Describe your artistic career, interests and style...',
    profileImageLabel: 'Profile image',
    profileImageHelp: 'JPG, PNG or WEBP. Maximum 2 MB.',
    coverImageLabel: 'Cover image',
    coverImageHelp: 'Select an image and adjust its framing before saving. The final cover will be panoramic.',
    previewReady: 'Preview pending save.',
    website: 'Personal website',
    cancel: 'Cancel',
    saving: 'Saving...',
    saveChanges: 'Save changes',
    description: 'Description',
    emptyBiography: 'Add a biography to introduce your artistic work on AMW.',
    messages: 'Messages',
    hideEditing: 'Hide editing',
    editProfile: 'Edit profile',
    selectedWorks: 'Selected artworks',
    loadingProfile: 'Loading profile...',
    emptyWorksTitle: 'You do not have published artworks yet.',
    emptyWorksText: 'When you create publications on AMW, they will appear here as part of your portfolio.',
    viewArtwork: 'View artwork',
    viewFullPortfolio: 'View more publications',
    artwork: 'Artwork',
    footerLabel: 'AMW footer',
    footerNavigation: 'Information and support links',
    contactPhone: 'Contact: +34 976 123 456',
    contactPhoneLabel: 'Call the AMW contact number',
    terms: 'Terms',
    help: 'Help',
    copyright: '© 2026 AMW · Art Makes A Way',
    profileLoadedError: 'The profile could not be loaded.',
    profileUpdated: 'Profile updated successfully.',
    profileUpdateError: 'The profile could not be updated.',
    imageUpdated: 'Profile image updated successfully.',
    imageError: 'The image could not be uploaded.',
    invalidImage: 'The selected file must be a JPG, PNG or WEBP image.',
    profileImageTooLarge: 'The profile image cannot exceed 2 MB.',
    coverImageTooLarge: 'The original cover image cannot exceed 12 MB.',
  },
  fr: {
    headerImage: 'Image d’en-tête du portfolio',
    profileImage: 'Image de profil',
    artist: 'Artiste AMW',
    works: 'Œuvres',
    followers: 'Abonnés',
    following: 'Abonnements',
    profileSettings: 'Configuration du profil',
    editArtisticProfile: 'Modifier le profil artistique',
    close: 'Fermer',
    artisticName: 'Nom artistique',
    specialty: 'Spécialité',
    specialtyPlaceholder: 'Art numérique, illustration, peinture...',
    biography: 'Biographie',
    biographyPlaceholder: 'Décrivez votre parcours artistique, vos intérêts et votre style...',
    profileImageLabel: 'Image de profil',
    profileImageHelp: 'JPG, PNG ou WEBP. Maximum 2 Mo.',
    coverImageLabel: 'Image de couverture',
    coverImageHelp: 'Sélectionnez une image et ajustez le cadrage avant de sauvegarder. La couverture finale sera panoramique.',
    previewReady: 'Aperçu en attente d’enregistrement.',
    website: 'Site personnel',
    cancel: 'Annuler',
    saving: 'Enregistrement...',
    saveChanges: 'Enregistrer',
    description: 'Description',
    emptyBiography: 'Ajoutez une biographie pour présenter votre travail artistique sur AMW.',
    messages: 'Messages',
    hideEditing: 'Masquer la modification',
    editProfile: 'Modifier le profil',
    selectedWorks: 'Œuvres sélectionnées',
    loadingProfile: 'Chargement du profil...',
    emptyWorksTitle: 'Vous n’avez pas encore publié d’œuvres.',
    emptyWorksText: 'Lorsque vous créerez des publications sur AMW, elles apparaîtront ici dans votre portfolio.',
    viewArtwork: 'Voir l’œuvre',
    viewFullPortfolio: 'Voir plus de publications',
    artwork: 'Œuvre',
    footerLabel: 'Pied de page AMW',
    footerNavigation: "Liens d'information et d'assistance",
    contactPhone: 'Contact : +34 976 123 456',
    contactPhoneLabel: 'Appeler le numéro de contact AMW',
    terms: 'Conditions',
    help: 'Aide',
    copyright: '© 2026 AMW · Art Makes A Way',
    profileLoadedError: 'Le profil n’a pas pu être chargé.',
    profileUpdated: 'Profil mis à jour correctement.',
    profileUpdateError: 'Le profil n’a pas pu être mis à jour.',
    imageUpdated: 'Image de profil mise à jour correctement.',
    imageError: 'L’image n’a pas pu être téléchargée.',
    invalidImage: 'Le fichier sélectionné doit être une image JPG, PNG ou WEBP.',
    profileImageTooLarge: 'L’image de profil ne peut pas dépasser 2 Mo.',
    coverImageTooLarge: 'L’image originale de couverture ne peut pas dépasser 12 Mo.',
  },
}

export default {
  name: 'ProfilePage',

  components: {
    CoverImageCropModal,
  },

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
        cover_image_url: '',
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
      userUsername: '',
      userWorksCount: 0,
      userFollowers: '0',
      userFollowing: 0,

      fallbackCoverImage:
          'https://www.guiarepsol.com/content/dam/repsol-guia/contenidos-imagenes/viajar/nos-gusta/nuevas-exposiciones-madrid/gr-cms-media-featured_images-none-867ebd3f-4b8b-4725-afe5-ea38e18aedec-saura-3.jpg',

      selectedProfileImageFile: null,
      selectedCoverImageFile: null,
      profileImagePreview: '',
      coverImagePreview: '',
      coverOriginalPreviewUrl: '',
      showCoverCropModal: false,
      profileSnapshot: null,

      artworks: [],
    }
  },

  computed: {
    ui() {
      return profileTexts[this.$i18n.locale] || profileTexts.es
    },

    displayProfileImage() {
      return this.profileImagePreview || this.profile.profile_image_url || this.userProfileImage
    },

    displayCoverImage() {
      return this.coverImagePreview || this.profile.cover_image_url || this.fallbackCoverImage
    },

    formattedUsername() {
      if (!this.userUsername) {
        return '@amw'
      }

      return `@${String(this.userUsername).replace(/^@/, '')}`
    },
  },

  async mounted() {
    window.addEventListener('amw-post-created', this.handlePostCreated)

    await this.loadProfile()
    await this.loadMyPosts()
  },

  beforeUnmount() {
    window.removeEventListener('amw-post-created', this.handlePostCreated)
    this.clearPendingImages()
  },

  methods: {
    handlePostCreated() {
      this.loadMyPosts()
    },

    goToPost(postId) {
      this.$router.push(`/posts/${postId}`)
    },

    toggleEditForm() {
      if (this.isEditing) {
        if (this.profileSnapshot) {
          this.profile = JSON.parse(JSON.stringify(this.profileSnapshot))
        }

        this.clearPendingImages()
        this.showCoverCropModal = false
        this.profileSnapshot = null
        this.isEditing = false
      } else {
        this.profileSnapshot = JSON.parse(JSON.stringify(this.profile))
        this.clearPendingImages()
        this.isEditing = true
      }

      this.successMessage = ''
      this.errorMessage = ''
    },

    loadStoredUsername() {
      const storedUser = localStorage.getItem('amw_user')

      if (!storedUser) {
        return
      }

      try {
        const user = JSON.parse(storedUser)
        this.userUsername = user?.username || this.userUsername
      } catch (error) {
        localStorage.removeItem('amw_user')
      }
    },

    async loadProfile() {
      this.loadStoredUsername()
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

        this.userName = data.artistic_name || this.ui.artist
        this.userUsername = data.username || this.userUsername
        this.userProfileImage = data.profile_image_url || this.userProfileImage
      } catch (error) {
        if (error.response?.status === 401) {
          localStorage.removeItem('amw_token')
          localStorage.removeItem('amw_user')
          this.$router.push('/login')
          return
        }

        this.errorMessage = this.ui.profileLoadedError
      } finally {
        this.loading = false
      }
    },

    async loadMyPosts() {
      try {
        const result = await getMyPosts()
        const posts = result.posts

        this.artworks = posts.map((post) => ({
          id: post.id,
          image: post.image_url || 'https://placehold.co/600x800?text=AMW',
          title: post.title,
          year: post.created_at
              ? new Date(post.created_at).getFullYear()
              : new Date().getFullYear(),
          medium: post.category?.name || post.type || this.ui.artwork,
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

        if (this.selectedProfileImageFile) {
          const imageResponse = await uploadProfileImage(this.selectedProfileImageFile)
          this.profile.profile_image_url = imageResponse.data.profile_image_url
          this.userProfileImage = imageResponse.data.profile_image_url
        }

        if (this.selectedCoverImageFile) {
          const coverResponse = await uploadCoverImage(this.selectedCoverImageFile)
          this.profile.cover_image_url = coverResponse.data.cover_image_url
        }

        this.userName = this.profile.artistic_name || this.userName
        this.successMessage = this.ui.profileUpdated
        this.profileSnapshot = null
        this.clearPendingImages()
        this.isEditing = false
      } catch (error) {
        if (error.response?.data?.errors?.image?.[0]) {
          this.errorMessage = error.response.data.errors.image[0]
        } else if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else {
          this.errorMessage = this.ui.profileUpdateError
        }
      } finally {
        this.loading = false
      }
    },

    handleProfileImageSelection(event) {
      const file = event.target.files[0]

      if (!file) {
        return
      }

      if (!this.validateImage(file, 2 * 1024 * 1024, this.ui.profileImageTooLarge)) {
        event.target.value = ''
        return
      }

      this.revokePreview(this.profileImagePreview)
      this.selectedProfileImageFile = file
      this.profileImagePreview = URL.createObjectURL(file)
      this.errorMessage = ''
    },

    handleCoverImageSelection(event) {
      const file = event.target.files[0]

      if (!file) {
        return
      }

      /*
       * La imagen original solo se utiliza en el navegador para recortarla.
       * El backend recibirá la versión panorámica generada por el editor.
       */
      if (!this.validateImage(file, 12 * 1024 * 1024, this.ui.coverImageTooLarge)) {
        event.target.value = ''
        return
      }

      this.revokePreview(this.coverOriginalPreviewUrl)
      this.coverOriginalPreviewUrl = URL.createObjectURL(file)
      this.showCoverCropModal = true
      this.errorMessage = ''
      event.target.value = ''
    },

    handleCroppedCover({ file, previewUrl }) {
      this.revokePreview(this.coverImagePreview)

      this.selectedCoverImageFile = file
      this.coverImagePreview = previewUrl
      this.showCoverCropModal = false
      this.errorMessage = ''
    },

    validateImage(file, maxSize, sizeError) {
      const validTypes = ['image/jpeg', 'image/png', 'image/webp']

      if (!validTypes.includes(file.type)) {
        this.errorMessage = this.ui.invalidImage
        return false
      }

      if (file.size > maxSize) {
        this.errorMessage = sizeError
        return false
      }

      return true
    },

    revokePreview(previewUrl) {
      if (previewUrl) {
        URL.revokeObjectURL(previewUrl)
      }
    },

    clearPendingImages() {
      this.revokePreview(this.profileImagePreview)
      this.revokePreview(this.coverImagePreview)
      this.revokePreview(this.coverOriginalPreviewUrl)

      this.selectedProfileImageFile = null
      this.selectedCoverImageFile = null
      this.profileImagePreview = ''
      this.coverImagePreview = ''
      this.coverOriginalPreviewUrl = ''
      this.showCoverCropModal = false

      if (this.$refs.profileImageInput) {
        this.$refs.profileImageInput.value = ''
      }

      if (this.$refs.coverImageInput) {
        this.$refs.coverImageInput.value = ''
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
.profile-page {
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", "Roboto", "Helvetica Neue", Arial, sans-serif;
}

.profile-page .font-notoSerif,
.profile-page .font-manrope {
  font-family: inherit;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.profile-footer {
  width: 100%;
  margin-top: 5rem;
  padding: 2.25rem 1.5rem 2.75rem;
  border-top: 1px solid rgba(120, 113, 108, 0.14);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1.15rem;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Roboto", "Helvetica Neue", Arial, sans-serif;
}

.profile-footer__links {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  column-gap: 1.6rem;
  row-gap: 0.75rem;
}

.profile-footer__link {
  color: #78716c;
  font-size: 0.75rem;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s ease;
}

.profile-footer__link:hover,
.profile-footer__link:focus-visible {
  color: #a900a9;
}

.profile-footer__copyright {
  color: #a8a29e;
  font-size: 0.72rem;
  font-weight: 500;
  text-align: center;
  margin: 0;
}

@media (max-width: 640px) {
  .profile-footer {
    margin-top: 3.5rem;
    padding: 1.75rem 1rem 2.25rem;
  }

  .profile-footer__links {
    column-gap: 1rem;
  }

  .profile-footer__link,
  .profile-footer__copyright {
    font-size: 0.7rem;
  }
}

</style>