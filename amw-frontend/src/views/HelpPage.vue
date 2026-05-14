<template>
  <div class="min-h-screen bg-[#FAF9F6] text-on-surface">

    <!-- Header -->

    <AppTopBar @logout="handleLogout" />

    <!-- Sidebar -->

    <AppSidebar />

    <!-- Contenido principal -->

    <main class="ml-20 px-8 pt-28 pb-16">
      <section class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16">
        <div>
          <router-link
              to="/login"
              class="font-manrope text-xs uppercase tracking-widest text-primary hover:opacity-70 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
          >
            Volver
          </router-link>

          <h1 class="font-notoSerif text-5xl italic mt-10 mb-8">
            Ayuda al cliente
          </h1>

          <p class="font-manrope text-stone-600 leading-relaxed mb-10">
            Si tienes cualquier problema con tu cuenta, el acceso a la plataforma, la subida de obras
            o el uso de AMW, puedes contactar con nuestro equipo de soporte.
          </p>

          <div class="space-y-6 font-manrope">
            <div>
              <p class="text-xs uppercase tracking-widest text-stone-400 mb-1">
                Teléfono
              </p>

              <p class="text-lg font-bold text-on-surface">
                +34 976 123 456
              </p>
            </div>

            <div>
              <p class="text-xs uppercase tracking-widest text-stone-400 mb-1">
                Correo electrónico
              </p>

              <p class="text-lg font-bold text-on-surface">
                soporte@amw-art.com
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white border border-stone-200 p-8 shadow-xl">
          <h2 class="font-notoSerif text-3xl italic mb-6">
            Coméntanos que sucede
          </h2>

          <div aria-live="polite" role="status" class="mb-6">
            <p
                v-if="successMessage"
                class="text-green-700 bg-green-50 border border-green-200 px-4 py-3 text-sm"
            >
              {{ successMessage }}
            </p>
          </div>

          <form @submit.prevent="sendComplaint" class="space-y-6">
            <div>
              <label
                  for="name"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                Nombre
              </label>

              <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  autocomplete="name"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  placeholder="Tu nombre"
              />
            </div>

            <div>
              <label
                  for="email"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                Email
              </label>

              <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  autocomplete="email"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  placeholder="tuemail@email.com"
              />
            </div>

            <div>
              <label
                  for="subject"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                Asunto
              </label>

              <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  placeholder="Motivo de la queja"
              />
            </div>

            <div>
              <label
                  for="message"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                Mensaje
              </label>

              <textarea
                  id="message"
                  v-model="form.message"
                  required
                  rows="6"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  placeholder="Describe tu problema o queja"
              ></textarea>
            </div>

            <button
                type="submit"
                class="w-full py-4 bg-gradient-to-r from-primary to-primary-container text-white font-manrope font-bold uppercase tracking-widest text-xs hover:opacity-90 transition-opacity"
            >
              Enviar queja
            </button>
          </form>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
export default {
  name: 'HelpPage',

  data() {
    return {
      selectedLanguage: 'es',

      successMessage: '',

      form: {
        name: '',
        email: '',
        subject: '',
        message: '',
      },
    }
  },

  methods: {
    handleLogout() {
      localStorage.removeItem('amw_token')
      localStorage.removeItem('amw_user')
      this.$router.push('/login')
    },

    sendComplaint() {
      this.successMessage =
          'Tu queja ha sido registrada correctamente. El equipo de AMW contactará contigo pronto.'

      this.form = {
        name: '',
        email: '',
        subject: '',
        message: '',
      }
    },
  },
}
</script>