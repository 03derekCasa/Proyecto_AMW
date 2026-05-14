<template>
  <main class="grid grid-cols-1 md:grid-cols-2 min-h-screen bg-background text-on-surface" aria-labelledby="login-title">
    <section
        class="hidden md:flex flex-col justify-between p-12 bg-surface-container-low relative overflow-hidden"
        aria-label="Presentación de AMW"
    >
      <div class="z-10">
        <span class="font-serif text-3xl font-bold tracking-tighter text-on-surface">AMW</span>
      </div>

      <div class="relative z-10 space-y-8">
        <h2 class="font-serif text-6xl leading-[1.1] text-on-surface">
          Where Art <br />
          <span class="italic text-primary">Finds a Way.</span>
        </h2>

        <p class="font-body text-lg text-on-surface-variant max-w-md">
          Únete a nuestra comunidad de artistas y crea tu propia galería virtual.
        </p>
      </div>

      <div
          class="absolute -right-20 top-1/4 w-[400px] h-[600px] shadow-2xl rotate-3 bg-surface-container-highest"
          aria-hidden="true"
      >
        <img
            class="w-full h-full object-cover"
            :src="loginImage"
            alt="imagen del torso de una estatua"
        />
      </div>

      <div class="z-10">
        <p class="font-label text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">
          © 2026 Art Makes a Way
        </p>
      </div>
    </section>

    <section class="flex flex-col justify-center items-center px-6 py-12 md:px-24 bg-surface">
      <div class="md:hidden mb-16">
        <span class="font-serif text-3xl font-bold tracking-tighter text-primary">AMW</span>
      </div>

      <div class="w-full max-w-sm">
        <header class="mb-12">
          <h1 id="login-title" class="font-serif text-4xl mb-4 text-on-surface">
            Bienvenido
          </h1>

          <p class="font-body text-on-surface-variant">
            Introduce tus credenciales para acceder a tu estudio virtual.
          </p>
        </header>

        <div id="auth-message" aria-live="polite" role="status" class="mb-6">
          <p v-if="successMessage" class="text-green-700 bg-green-50 border border-green-200 px-4 py-3 text-sm">
            {{ successMessage }}
          </p>

          <p v-if="errorMessage" class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm">
            {{ errorMessage }}
          </p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-10" novalidate>
          <div class="relative group">
            <label class="font-serif text-sm font-semibold text-on-surface-variant block mb-1" for="email">
              Email
            </label>

            <input
                v-model="email"
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                placeholder="artist@gallery.com"
                required
                :aria-invalid="errorMessage ? 'true' : 'false'"
                aria-describedby="auth-message"
                class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline focus:border-b-2 focus:border-primary focus:ring-0 transition-all py-3 px-3 font-body text-on-surface placeholder:text-zinc-400"
            />
          </div>

          <div class="relative group">
            <div class="flex justify-between items-center mb-1">
              <label class="font-serif text-sm font-semibold text-on-surface-variant block" for="password">
                Contraseña
              </label>

              <a
                  class="font-label text-[10px] uppercase tracking-widest text-primary font-bold hover:opacity-70 transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
                  href="#"
                  aria-label="Recuperar contraseña"
              >
                ¿Olvidaste la contraseña?
              </a>
            </div>

            <input
                v-model="password"
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="••••••••"
                required
                :aria-invalid="errorMessage ? 'true' : 'false'"
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
              {{ loading ? 'Entrando...' : 'Entrar' }}
            </button>
          </div>
        </form>

        <div class="mt-12 text-center">
          <p class="font-body text-sm text-on-surface-variant">
            ¿No tienes cuenta?

            <router-link
                class="font-bold text-on-surface hover:text-primary transition-colors underline decoration-primary/30 underline-offset-4 ml-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
                to="/register"
            >
              Regístrate
            </router-link>
          </p>
        </div>
      </div>
    </section>
  </main>
  <AppFooter />
</template>

<script>
import { loginUser } from '@/services/authService'
import loginImage from '@/assets/Cardwell.jpeg'
import AppFooter from '@/components/AppFooter.vue'
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
      successMessage: '',
      loginImage: loginImage,
    }
  },

  mounted() {
    if (this.$route.query.registered === '1') {
      this.successMessage = 'Cuenta creada correctamente. Ahora inicia sesión.'
    }
  },

  methods: {
    async handleLogin() {
      this.loading = true
      this.errorMessage = ''
      this.successMessage = ''

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
          this.errorMessage = 'No se pudo iniciar sesión. Revisa tus credenciales.'
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
</style>