<template>
  <div class="min-h-screen bg-background text-on-surface">
    <main class="flex min-h-screen" aria-labelledby="register-title">
      <section class="relative w-full lg:w-[45%] flex flex-col justify-center px-8 sm:px-12 md:px-24 py-16 bg-background z-10">
        <!-- Selector disponible antes de iniciar sesión -->
        <div class="absolute top-6 right-6">
          <label for="register-language-selector" class="sr-only">
            {{ copy.language }}
          </label>

          <select
            id="register-language-selector"
            v-model="$i18n.locale"
            class="bg-transparent border-0 focus:ring-0 font-label text-[10px] uppercase tracking-widest text-primary font-bold cursor-pointer px-1 py-2"
            :aria-label="copy.selectLanguage"
            @change="saveLocale"
          >
            <option value="es">ES</option>
            <option value="en">EN</option>
            <option value="fr">FR</option>
          </select>
        </div>

        <div class="max-w-md w-full mx-auto space-y-12">
          <header class="space-y-4">
            <span class="font-label text-primary uppercase tracking-[0.3em] text-[10px] font-bold">
              Art Makes A Way
            </span>

            <h1 id="register-title" class="text-4xl md:text-5xl font-headline font-bold text-on-surface leading-tight">
              AMW
            </h1>

            <p class="text-on-surface-variant font-body text-lg leading-relaxed max-w-sm">
              {{ copy.introduction }}
            </p>
          </header>

          <div id="register-message" aria-live="polite" role="status">
            <p
              v-if="displayedError"
              class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
            >
              {{ displayedError }}
            </p>
          </div>

          <form class="space-y-10" novalidate @submit.prevent="handleRegister">
            <div class="space-y-8">
              <div class="group">
                <label class="block font-headline text-on-surface-variant text-sm mb-1" for="full-name">
                  {{ copy.name }}
                </label>

                <input
                  id="full-name"
                  v-model="fullName"
                  name="full-name"
                  type="text"
                  autocomplete="name"
                  placeholder="Derek Casa"
                  required
                  :aria-invalid="displayedError ? 'true' : 'false'"
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400 placeholder:font-body"
                />
              </div>

              <div class="group">
                <label class="block font-headline text-on-surface-variant text-sm mb-1" for="discipline">
                  {{ copy.discipline }}
                </label>

                <select
                  id="discipline"
                  v-model="discipline"
                  name="discipline"
                  required
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 appearance-none font-body text-on-surface"
                >
                  <option disabled value="">{{ copy.chooseDiscipline }}</option>
                  <option value="artes visuales">{{ copy.visualArts }}</option>
                  <option value="artes digitales">{{ copy.digitalArts }}</option>
                  <option value="artes escénicas">{{ copy.performingArts }}</option>
                </select>
              </div>

              <div class="group">
                <label class="block font-headline text-on-surface-variant text-sm mb-1" for="email">
                  {{ copy.email }}
                </label>

                <input
                  id="email"
                  v-model="email"
                  name="email"
                  type="email"
                  autocomplete="email"
                  placeholder="derekcasa@gmail.com"
                  required
                  :aria-invalid="displayedError ? 'true' : 'false'"
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400"
                />
              </div>

              <div class="group">
                <label class="block font-headline text-on-surface-variant text-sm mb-1" for="password">
                  {{ copy.password }}
                </label>

                <input
                  id="password"
                  v-model="password"
                  name="password"
                  type="password"
                  autocomplete="new-password"
                  placeholder="••••••••"
                  required
                  minlength="8"
                  :aria-invalid="displayedError ? 'true' : 'false'"
                  aria-describedby="register-message password-help"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400"
                />

                <p id="password-help" class="text-xs text-stone-500 mt-2">
                  {{ copy.passwordHelp }}
                </p>
              </div>

              <div class="group">
                <label class="block font-headline text-on-surface-variant text-sm mb-1" for="password_confirmation">
                  {{ copy.confirmPassword }}
                </label>

                <input
                  id="password_confirmation"
                  v-model="password_confirmation"
                  name="password_confirmation"
                  type="password"
                  autocomplete="new-password"
                  placeholder="••••••••"
                  required
                  minlength="8"
                  :aria-invalid="password_confirmation && password !== password_confirmation ? 'true' : 'false'"
                  aria-describedby="register-message password-confirmation-help"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400"
                />

                <p id="password-confirmation-help" class="text-xs text-stone-500 mt-2">
                  {{ copy.confirmHelp }}
                </p>
              </div>
            </div>

            <div class="pt-4">
              <button
                class="w-full bg-gradient-to-r from-primary to-primary-container text-on-primary font-label text-sm font-extrabold py-5 uppercase tracking-[0.2em] hover:opacity-90 active:scale-[0.98] transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                type="submit"
                :disabled="loading || !canSubmit"
                :aria-busy="loading ? 'true' : 'false'"
              >
                {{ loading ? copy.creatingAccount : copy.createAccount }}
              </button>
            </div>
          </form>

          <footer class="pt-8 border-t border-surface-container-low flex flex-col sm:flex-row items-center gap-4">
            <p class="text-on-surface-variant font-body text-sm">
              {{ copy.alreadyAccount }}
            </p>

            <router-link
              class="font-label text-primary font-bold text-xs uppercase tracking-widest border-b-2 border-primary-container pb-1 hover:border-primary transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
              to="/login"
            >
              {{ copy.login }}
            </router-link>
          </footer>
        </div>
      </section>

      <section
        class="hidden lg:block lg:w-[55%] relative overflow-hidden bg-surface-container-low"
        :aria-label="copy.presentationLabel"
      >
        <img
          :alt="copy.imageAlt"
          class="absolute inset-0 w-full h-full object-cover"
          src="../assets/karalangshark.webp"
        />

        <div class="absolute inset-0 bg-gradient-to-t from-primary/20 via-transparent to-transparent" aria-hidden="true"></div>

        <div class="absolute bottom-24 left-24 right-24 space-y-6">
          <h2 class="text-6xl font-headline font-bold text-white leading-tight drop-shadow-2xl">
            {{ copy.heroLineOne }} <br />
            {{ copy.heroLineTwo }}
          </h2>

          <div class="flex items-center gap-6 text-white/80 font-label text-xs uppercase tracking-[0.4em]">
            <span>{{ copy.collection }}</span>
            <span class="h-1 w-1 bg-white/40 rounded-full" aria-hidden="true"></span>
            <span>{{ copy.curatedSelection }}</span>
          </div>
        </div>

        <div class="absolute top-24 right-24 p-6 bg-surface/10 backdrop-blur-xl border border-white/10 w-64">
          <span class="material-symbols-outlined text-primary mb-4" aria-hidden="true">auto_awesome</span>

          <p class="text-white font-headline italic text-lg">
            “Every artist was first an amateur.”
          </p>

          <p class="text-white/60 font-label text-[10px] mt-4 uppercase tracking-widest">
            Ralph Waldo Emerson
          </p>
        </div>
      </section>
    </main>

    <AppFooter />
  </div>
</template>

<script>
import { registerUser } from '@/services/authService'
import AppFooter from '@/components/AppFooter.vue'

const copies = {
  es: {
    language: 'Idioma',
    selectLanguage: 'Seleccionar idioma',
    introduction: 'Únete a nuestra comunidad de artistas listos para expandirse.',
    name: 'Nombre',
    discipline: 'Disciplina artística',
    chooseDiscipline: 'Selecciona disciplina',
    visualArts: 'Artes visuales',
    digitalArts: 'Artes digitales',
    performingArts: 'Artes escénicas',
    email: 'Email',
    password: 'Contraseña',
    passwordHelp: 'La contraseña debe tener al menos 8 caracteres.',
    confirmPassword: 'Confirmar contraseña',
    confirmHelp: 'Debe coincidir con la contraseña anterior.',
    creatingAccount: 'Creando cuenta...',
    createAccount: 'Crear una cuenta',
    alreadyAccount: '¿Ya tienes una cuenta?',
    login: 'Iniciar sesión',
    presentationLabel: 'Imagen artística de presentación',
    imageAlt: 'Ilustración digital de estilo artístico usada como imagen de presentación de AMW',
    heroLineOne: 'El arte es',
    heroLineTwo: 'el camino.',
    collection: 'Colección 04',
    curatedSelection: 'Selección curada',
    passwordMismatch: 'Las contraseñas no coinciden.',
    registerError: 'No se pudo completar el registro.'
  },
  en: {
    language: 'Language',
    selectLanguage: 'Select language',
    introduction: 'Join our community of artists ready to expand their work.',
    name: 'Name',
    discipline: 'Artistic discipline',
    chooseDiscipline: 'Select a discipline',
    visualArts: 'Visual arts',
    digitalArts: 'Digital arts',
    performingArts: 'Performing arts',
    email: 'Email',
    password: 'Password',
    passwordHelp: 'Your password must be at least 8 characters long.',
    confirmPassword: 'Confirm password',
    confirmHelp: 'It must match the password above.',
    creatingAccount: 'Creating account...',
    createAccount: 'Create an account',
    alreadyAccount: 'Already have an account?',
    login: 'Log in',
    presentationLabel: 'Artistic presentation image',
    imageAlt: 'Digital artistic illustration used as the AMW presentation image',
    heroLineOne: 'Art Is',
    heroLineTwo: 'The Way Out.',
    collection: 'Collection 04',
    curatedSelection: 'Curated Selection',
    passwordMismatch: 'Passwords do not match.',
    registerError: 'Registration could not be completed.'
  },
  fr: {
    language: 'Langue',
    selectLanguage: 'Sélectionner la langue',
    introduction: 'Rejoignez notre communauté d’artistes prêts à développer leur univers.',
    name: 'Nom',
    discipline: 'Discipline artistique',
    chooseDiscipline: 'Sélectionnez une discipline',
    visualArts: 'Arts visuels',
    digitalArts: 'Arts numériques',
    performingArts: 'Arts de la scène',
    email: 'E-mail',
    password: 'Mot de passe',
    passwordHelp: 'Le mot de passe doit contenir au moins 8 caractères.',
    confirmPassword: 'Confirmer le mot de passe',
    confirmHelp: 'Il doit correspondre au mot de passe précédent.',
    creatingAccount: 'Création du compte...',
    createAccount: 'Créer un compte',
    alreadyAccount: 'Vous avez déjà un compte ?',
    login: 'Se connecter',
    presentationLabel: 'Image artistique de présentation',
    imageAlt: 'Illustration numérique artistique utilisée comme image de présentation de AMW',
    heroLineOne: 'L’art est',
    heroLineTwo: 'la voie.',
    collection: 'Collection 04',
    curatedSelection: 'Sélection organisée',
    passwordMismatch: 'Les mots de passe ne correspondent pas.',
    registerError: 'L’inscription n’a pas pu être finalisée.'
  }
}

export default {
  name: 'RegisterPage',

  components: {
    AppFooter,
  },

  data() {
    return {
      fullName: '',
      discipline: '',
      email: '',
      password: '',
      password_confirmation: '',
      loading: false,
      errorMessage: '',
      errorKey: '',
    }
  },

  computed: {
    copy() {
      return copies[this.$i18n.locale] || copies.es
    },

    displayedError() {
      if (this.errorMessage) {
        return this.errorMessage
      }

      return this.errorKey ? this.copy[this.errorKey] : ''
    },

    canSubmit() {
      return (
        this.fullName &&
        this.email &&
        this.password.length >= 8 &&
        this.password_confirmation.length >= 8 &&
        this.password === this.password_confirmation
      )
    },
  },

  mounted() {
    document.documentElement.lang = this.$i18n.locale
  },

  methods: {
    saveLocale() {
      localStorage.setItem('amw_locale', this.$i18n.locale)
      document.documentElement.lang = this.$i18n.locale
    },

    async handleRegister() {
      this.loading = true
      this.errorMessage = ''
      this.errorKey = ''

      if (this.password !== this.password_confirmation) {
        this.errorKey = 'passwordMismatch'
        this.loading = false
        return
      }

      try {
        await registerUser({
          name: this.fullName,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        })

        this.$router.push({
          path: '/login',
          query: {
            registered: '1',
          },
        })
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else {
          this.errorKey = 'registerError'
        }
      } finally {
        this.loading = false
      }
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
