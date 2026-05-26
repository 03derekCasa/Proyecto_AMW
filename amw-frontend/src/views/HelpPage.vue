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
            {{ ui.back }}
          </router-link>

          <h1 class="font-notoSerif text-5xl italic mt-10 mb-8">
            {{ ui.title }}
          </h1>

          <p class="font-manrope text-stone-600 leading-relaxed mb-10">
            {{ ui.introduction }}
          </p>

          <div class="space-y-6 font-manrope">
            <div>
              <p class="text-xs uppercase tracking-widest text-stone-400 mb-1">
                {{ ui.phone }}
              </p>

              <p class="text-lg font-bold text-on-surface">
                +34 976 123 456
              </p>
            </div>

            <div>
              <p class="text-xs uppercase tracking-widest text-stone-400 mb-1">
                {{ ui.email }}
              </p>

              <p class="text-lg font-bold text-on-surface">
                soporte@amw-art.com
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white border border-stone-200 p-8 shadow-xl">
          <h2 class="font-notoSerif text-3xl italic mb-6">
            {{ ui.formTitle }}
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
                {{ ui.name }}
              </label>

              <input
                  id="name"
                  name="name"
                  v-model="form.name"
                  type="text"
                  required
                  autocomplete="name"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  :placeholder="ui.namePlaceholder"
              />
            </div>

            <div>
              <label
                  for="email"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ ui.email }}
              </label>

              <input
                  id="email"
                  name="email"
                  v-model="form.email"
                  type="email"
                  required
                  autocomplete="email"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  :placeholder="ui.emailPlaceholder"
              />
            </div>

            <div>
              <label
                  for="subject"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ ui.subject }}
              </label>

              <input
                  id="subject"
                  name="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  :placeholder="ui.subjectPlaceholder"
              />
            </div>

            <div>
              <label
                  for="message"
                  class="block font-manrope text-xs uppercase tracking-widest text-stone-500 mb-2"
              >
                {{ ui.message }}
              </label>

              <textarea
                  id="message"
                  name="message"
                  v-model="form.message"
                  required
                  rows="6"
                  class="w-full border border-stone-300 px-4 py-3 focus:ring-0 focus:border-primary"
                  :placeholder="ui.messagePlaceholder"
              ></textarea>
            </div>

            <button
                type="submit"
                class="w-full py-4 bg-gradient-to-r from-primary to-primary-container text-white font-manrope font-bold uppercase tracking-widest text-xs hover:opacity-90 transition-opacity"
            >
              {{ ui.submit }}
            </button>
          </form>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { logoutUser } from '@/services/authService'

const helpTexts = {
  es: {
    back: 'Volver',
    title: 'Ayuda al cliente',
    introduction: 'Si tienes cualquier problema con tu cuenta, el acceso a la plataforma, la subida de obras o el uso de AMW, puedes contactar con nuestro equipo de soporte.',
    phone: 'Teléfono',
    email: 'Correo electrónico',
    formTitle: 'Cuéntanos qué sucede',
    name: 'Nombre',
    namePlaceholder: 'Tu nombre',
    emailPlaceholder: 'tuemail@email.com',
    subject: 'Asunto',
    subjectPlaceholder: 'Motivo de la queja',
    message: 'Mensaje',
    messagePlaceholder: 'Describe tu problema o queja',
    submit: 'Enviar queja',
    success: 'Tu queja ha sido registrada correctamente. El equipo de AMW contactará contigo pronto.',
  },
  en: {
    back: 'Back',
    title: 'Customer support',
    introduction: 'If you have any problem with your account, access to the platform, artwork uploads or the use of AMW, you can contact our support team.',
    phone: 'Telephone',
    email: 'Email address',
    formTitle: 'Tell us what happened',
    name: 'Name',
    namePlaceholder: 'Your name',
    emailPlaceholder: 'youremail@email.com',
    subject: 'Subject',
    subjectPlaceholder: 'Reason for your complaint',
    message: 'Message',
    messagePlaceholder: 'Describe your problem or complaint',
    submit: 'Send complaint',
    success: 'Your complaint has been registered successfully. The AMW team will contact you soon.',
  },
  fr: {
    back: 'Retour',
    title: 'Service client',
    introduction: "Si vous rencontrez un problème avec votre compte, l'accès à la plateforme, la publication d'œuvres ou l'utilisation d'AMW, vous pouvez contacter notre équipe d'assistance.",
    phone: 'Téléphone',
    email: 'Adresse électronique',
    formTitle: 'Expliquez-nous ce qui se passe',
    name: 'Nom',
    namePlaceholder: 'Votre nom',
    emailPlaceholder: 'votreadresse@email.com',
    subject: 'Objet',
    subjectPlaceholder: 'Motif de votre réclamation',
    message: 'Message',
    messagePlaceholder: 'Décrivez votre problème ou votre réclamation',
    submit: 'Envoyer la réclamation',
    success: "Votre réclamation a été enregistrée correctement. L'équipe AMW vous contactera prochainement.",
  },
}

export default {
  name: 'HelpPage',

  data() {
    return {
      successMessage: '',

      form: {
        name: '',
        email: '',
        subject: '',
        message: '',
      },
    }
  },

  computed: {
    ui() {
      return helpTexts[this.$i18n.locale] || helpTexts.es
    },
  },

  methods: {
    async handleLogout() {
      try {
        await logoutUser()
      } catch (error) {
        localStorage.removeItem('amw_token')
        localStorage.removeItem('amw_user')
      }

      this.$router.push('/login')
    },

    sendComplaint() {
      this.successMessage = this.ui.success

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