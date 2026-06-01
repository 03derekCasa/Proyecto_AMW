<template>
  <div class="min-h-screen bg-background text-on-surface">
    <main class="grid grid-cols-1 md:grid-cols-2 min-h-screen" aria-labelledby="login-title">
      <section
        class="hidden md:flex flex-col justify-between p-12 bg-surface-container-low relative overflow-hidden"
        :aria-label="copy.presentationLabel"
      >
        <div class="z-10">
          <span class="font-serif text-3xl font-bold tracking-tighter text-on-surface">AMW</span>
        </div>

        <div class="relative z-10 space-y-8">
          <h2 class="font-serif text-6xl leading-[1.1] text-on-surface">
            {{ copy.heroLineOne }} <br />
            <span class="italic text-primary">{{ copy.heroLineTwo }}</span>
          </h2>

          <p class="font-body text-lg text-on-surface-variant max-w-md">
            {{ copy.communityText }}
          </p>
        </div>

        <div
          class="absolute -right-20 top-1/4 w-[400px] h-[600px] shadow-2xl rotate-3 bg-surface-container-highest"
          aria-hidden="true"
        >
          <img
            class="w-full h-full object-cover"
            :src="loginImage"
            :alt="copy.imageAlt"
          />
        </div>

        <div class="z-10">
          <p class="font-label text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">
            © 2026 Art Makes A Way
          </p>
        </div>
      </section>

      <section class="relative flex flex-col justify-center items-center px-6 py-12 md:px-24 bg-surface">
        <!-- Selector disponible antes de iniciar sesión -->
        <div class="absolute top-6 right-6">
          <label for="login-language-selector" class="sr-only">
            {{ copy.language }}
          </label>

          <select
            id="login-language-selector"
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

        <div class="md:hidden mb-16">
          <span class="font-serif text-3xl font-bold tracking-tighter text-primary">AMW</span>
        </div>

        <div class="w-full max-w-sm">
          <header class="mb-12">
            <h1 id="login-title" class="font-serif text-4xl mb-4 text-on-surface">
              {{ copy.welcome }}
            </h1>

            <p class="font-body text-on-surface-variant">
              {{ copy.credentialsText }}
            </p>
          </header>

          <div id="auth-message" aria-live="polite" role="status" class="mb-6">
            <p
              v-if="registrationSuccess"
              class="text-green-700 bg-green-50 border border-green-200 px-4 py-3 text-sm"
            >
              {{ copy.registerSuccess }}
            </p>

            <p
              v-if="displayedError"
              class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm"
            >
              {{ displayedError }}
            </p>
          </div>

          <form class="space-y-10" novalidate @submit.prevent="handleLogin">
            <div class="relative group">
              <label class="font-serif text-sm font-semibold text-on-surface-variant block mb-1" for="email">
                {{ copy.email }}
              </label>

              <input
                id="email"
                v-model="email"
                name="email"
                type="email"
                autocomplete="email"
                placeholder="artist@gallery.com"
                required
                :aria-invalid="displayedError ? 'true' : 'false'"
                aria-describedby="auth-message"
                class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline focus:border-b-2 focus:border-primary focus:ring-0 transition-all py-3 px-3 font-body text-on-surface placeholder:text-zinc-400"
              />
            </div>

            <div class="relative group">
              <div class="flex justify-between items-center mb-1">
                <label class="font-serif text-sm font-semibold text-on-surface-variant block" for="password">
                  {{ copy.password }}
                </label>

                <button
                  class="font-label text-[10px] uppercase tracking-widest text-primary font-bold hover:opacity-70 transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
                  type="button"
                >
                </button>
              </div>

              <input
                id="password"
                v-model="password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="••••••••"
                required
                :aria-invalid="displayedError ? 'true' : 'false'"
                aria-describedby="auth-message"
                class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline focus:border-b-2 focus:border-primary focus:ring-0 transition-all py-3 px-3 font-body text-on-surface placeholder:text-zinc-400"
              />
            </div>

            <div class="pt-4">
              <button
                class="w-full py-5 px-12 bg-gradient-to-r from-primary to-primary-container text-white font-label text-xs uppercase tracking-[0.2em] font-extrabold shadow-lg hover:shadow-primary/20 active:scale-[0.98] transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                type="submit"
                :disabled="loading"
                :aria-busy="loading ? 'true' : 'false'"
              >
                {{ loading ? copy.loggingIn : copy.login }}
              </button>
            </div>
          </form>

          <div class="mt-12 text-center">
            <p class="font-body text-sm text-on-surface-variant">
              {{ copy.noAccount }}

              <router-link
                class="font-bold text-on-surface hover:text-primary transition-colors underline decoration-primary/30 underline-offset-4 ml-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
                to="/register"
              >
                {{ copy.register }}
              </router-link>
            </p>
          </div>
        </div>
      </section>
    </main>

    <AppFooter />
  </div>
</template>

<script>
import { loginUser } from '@/services/authService'
import loginImage from '@/assets/Cardwell.jpeg'
import AppFooter from '@/components/AppFooter.vue'

const copies = {
  es: {
    language: 'Idioma',
    selectLanguage: 'Seleccionar idioma',
    presentationLabel: 'Presentación de AMW',
    heroLineOne: 'Donde el arte',
    heroLineTwo: 'encuentra su camino.',
    communityText: 'Únete a nuestra comunidad de artistas y crea tu propia galería virtual.',
    imageAlt: 'Imagen artística de presentación de AMW',
    welcome: 'Bienvenido',
    credentialsText: 'Introduce tus credenciales para acceder a tu estudio virtual.',
    registerSuccess: 'Cuenta creada correctamente. Ahora inicia sesión.',
    email: 'Email',
    password: 'Contraseña',
    forgotPassword: '¿Olvidaste la contraseña?',
    recoverPassword: 'Recuperar contraseña',
    loggingIn: 'Entrando...',
    login: 'Entrar',
    noAccount: '¿No tienes cuenta?',
    register: 'Regístrate',
    loginError: 'No se pudo iniciar sesión. Revisa tus credenciales.'
  },
  en: {
    language: 'Language',
    selectLanguage: 'Select language',
    presentationLabel: 'AMW presentation',
    heroLineOne: 'Where Art',
    heroLineTwo: 'Finds a Way.',
    communityText: 'Join our community of artists and create your own virtual gallery.',
    imageAlt: 'Artistic presentation image for AMW',
    welcome: 'Welcome',
    credentialsText: 'Enter your credentials to access your virtual studio.',
    registerSuccess: 'Account created successfully. You can now log in.',
    email: 'Email',
    password: 'Password',
    forgotPassword: 'Forgot your password?',
    recoverPassword: 'Recover password',
    loggingIn: 'Logging in...',
    login: 'Log in',
    noAccount: 'Do not have an account?',
    register: 'Sign up',
    loginError: 'Unable to log in. Please check your credentials.'
  },
  fr: {
    language: 'Langue',
    selectLanguage: 'Sélectionner la langue',
    presentationLabel: 'Présentation de AMW',
    heroLineOne: 'Là où l’art',
    heroLineTwo: 'trouve son chemin.',
    communityText: 'Rejoignez notre communauté d’artistes et créez votre propre galerie virtuelle.',
    imageAlt: 'Image artistique de présentation de AMW',
    welcome: 'Bienvenue',
    credentialsText: 'Saisissez vos identifiants pour accéder à votre atelier virtuel.',
    registerSuccess: 'Compte créé avec succès. Vous pouvez maintenant vous connecter.',
    email: 'E-mail',
    password: 'Mot de passe',
    forgotPassword: 'Mot de passe oublié ?',
    recoverPassword: 'Récupérer le mot de passe',
    loggingIn: 'Connexion...',
    login: 'Se connecter',
    noAccount: 'Vous n’avez pas de compte ?',
    register: 'Créer un compte',
    loginError: 'Impossible de se connecter. Vérifiez vos identifiants.'
  }
}

export default {
  name: 'LoginPage',

  components: {
    AppFooter,
  },

  data() {
    return {
      email: '',
      password: '',
      loading: false,
      errorMessage: '',
      errorKey: '',
      registrationSuccess: false,
      loginImage,
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
  },

  mounted() {
    document.documentElement.lang = this.$i18n.locale
    this.registrationSuccess = this.$route.query.registered === '1'
  },

  methods: {
    saveLocale() {
      localStorage.setItem('amw_locale', this.$i18n.locale)
      document.documentElement.lang = this.$i18n.locale
    },

    async handleLogin() {
      this.loading = true
      this.errorMessage = ''
      this.errorKey = ''
      this.registrationSuccess = false

      try {
        await loginUser({
          email: this.email,
          password: this.password,
        })

        this.$router.push('/profile')
      } catch (error) {
        if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message
        } else {
          this.errorKey = 'loginError'
        }
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
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
