<template>
  <main class="flex min-h-screen bg-background text-on-surface" aria-labelledby="register-title">
    <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 sm:px-12 md:px-24 py-12 bg-background z-10">
      <div class="max-w-md w-full mx-auto space-y-12">
        <header class="space-y-4">
          <span class="font-label text-primary uppercase tracking-[0.3em] text-[10px] font-bold">
            Art Makes A Way
          </span>

          <h1 id="register-title" class="text-4xl md:text-5xl font-headline font-bold text-on-surface leading-tight">
            AMW
          </h1>

          <p class="text-on-surface-variant font-body text-lg leading-relaxed max-w-sm">
            Únete a nuestra comunidad de artistas listos para expandirse.
          </p>
        </header>

        <div id="register-message" aria-live="polite" role="status">
          <p v-if="errorMessage" class="text-red-700 bg-red-50 border border-red-200 px-4 py-3 text-sm">
            {{ errorMessage }}
          </p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-10" novalidate>
          <div class="space-y-8">
            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="full-name">
                Nombre
              </label>

              <input
                  v-model="fullName"
                  id="full-name"
                  name="full-name"
                  type="text"
                  autocomplete="name"
                  placeholder="Derek Casa"
                  required
                  :aria-invalid="errorMessage ? 'true' : 'false'"
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400 placeholder:font-body"
              />
            </div>

            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="discipline">
                Disciplina artística
              </label>

              <select
                  v-model="discipline"
                  id="discipline"
                  name="discipline"
                  required
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 appearance-none font-body text-on-surface"
              >
                <option disabled value="">Selecciona disciplina</option>
                <option value="artes visuales">Artes visuales</option>
                <option value="artes digitales">Artes digitales</option>
                <option value="artes escénicas">Artes escénicas</option>
              </select>
            </div>

            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="email">
                Email
              </label>

              <input
                  v-model="email"
                  id="email"
                  name="email"
                  type="email"
                  autocomplete="email"
                  placeholder="derekcasa@gmail.com"
                  required
                  :aria-invalid="errorMessage ? 'true' : 'false'"
                  aria-describedby="register-message"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400"
              />
            </div>

            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="password">
                Contraseña
              </label>

              <input
                  v-model="password"
                  id="password"
                  name="password"
                  type="password"
                  autocomplete="new-password"
                  placeholder="••••••••"
                  required
                  minlength="8"
                  :aria-invalid="errorMessage ? 'true' : 'false'"
                  aria-describedby="register-message password-help"
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-3 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-zinc-400"
              />

              <p id="password-help" class="text-xs text-stone-500 mt-2">
                La contraseña debe tener al menos 8 caracteres.
              </p>
            </div>

            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="password_confirmation">
                Confirmar contraseña
              </label>

              <input
                  v-model="password_confirmation"
                  id="password_confirmation"
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
                Debe coincidir con la contraseña anterior.
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
              {{ loading ? 'Creando cuenta...' : 'Crear una cuenta' }}
            </button>
          </div>
        </form>

        <footer class="pt-8 border-t border-surface-container-low flex flex-col sm:flex-row items-center gap-4">
          <p class="text-on-surface-variant font-body text-sm">
            ¿Ya tienes una cuenta?
          </p>

          <router-link
              class="font-label text-primary font-bold text-xs uppercase tracking-widest border-b-2 border-primary-container pb-1 hover:border-primary transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
              to="/login"
          >
            Iniciar sesión
          </router-link>
        </footer>
      </div>
    </section>

    <section
        class="hidden lg:block lg:w-[55%] relative overflow-hidden bg-surface-container-low"
        aria-label="Imagen artística de presentación"
    >
      <img
          alt="Ilustración digital de estilo artístico usada como imagen de presentación de AMW"
          class="absolute inset-0 w-full h-full object-cover"
          src="../assets/karalangshark.webp"
      />

      <div class="absolute inset-0 bg-gradient-to-t from-primary/20 via-transparent to-transparent" aria-hidden="true"></div>

      <div class="absolute bottom-24 left-24 right-24 space-y-6">

        <h2 class="text-6xl font-headline font-bold text-white leading-tight drop-shadow-2xl">
          Art Is <br />
          The Way Out.
        </h2>

        <div class="flex items-center gap-6 text-white/80 font-label text-xs uppercase tracking-[0.4em]">
          <span>Collection 04</span>
          <span class="h-1 w-1 bg-white/40 rounded-full" aria-hidden="true"></span>
          <span>Curated Selection</span>
        </div>
      </div>

      <div class="absolute top-24 right-24 p-6 bg-surface/1 backdrop-blur-xl border border-white/10 w-64">
        <span class="material-symbols-outlined text-primary mb-4" aria-hidden="true">auto_awesome</span>

        <p class="text-white font-headline italic text-lg">
          "Every artist was first an amateur."
        </p>

        <p class="text-white/60 font-label text-[10px] mt-4 uppercase tracking-widest">
          Ralph Waldo Emerson
        </p>
      </div>
    </section>
  </main>
  <AppFooter/>
</template>

<script>
import { registerUser } from '@/services/authService'
import AppFooter from '@/components/AppFooter.vue'
export default {
  name: 'RegisterPage',
  components: {AppFooter},

  data() {
    return {
      fullName: '',
      discipline: '',
      email: '',
      password: '',
      password_confirmation: '',
      loading: false,
      errorMessage: '',
    }
  },

  computed: {
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

  methods: {
    async handleRegister() {
      this.loading = true
      this.errorMessage = ''

      if (this.password !== this.password_confirmation) {
        this.errorMessage = 'Las contraseñas no coinciden.'
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
          this.errorMessage = 'No se pudo completar el registro.'
        }
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
</style>