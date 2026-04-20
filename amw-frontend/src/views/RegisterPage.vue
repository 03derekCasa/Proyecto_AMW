<template>
  <main class="flex min-h-screen">
    <!-- Form Section -->
    <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 sm:px-12 md:px-24 py-12 bg-background z-10">
      <div class="max-w-md w-full mx-auto space-y-12">
        <!-- Brand Anchor -->
        <header class="space-y-4">
          <span class="font-label text-primary uppercase tracking-[0.3em] text-[10px] font-bold">Art Makes A Way</span>
          <h1 class="text-4xl md:text-5xl font-headline font-bold text-on-surface leading-tight">AMW</h1>
          <p class="text-on-surface-variant font-body text-lg leading-relaxed max-w-sm">
            Únete a nuestra comunidad de artistas listos para expandirse.
          </p>
        </header>
        <form @submit.prevent="handleRegister" class="space-y-10">
          <div class="space-y-8">
            <!-- Nombre -->
            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="full-name">Nombre</label>
              <input
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-surface-container-highest placeholder:font-body"
                  v-model="fullName"
                  id="full-name"
                  name="full-name"
                  placeholder="Derek Casa"
                  type="text"
                  required
              />
            </div>
            <!-- Disciplina -->
            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="discipline">Disciplina artistica</label>
              <select
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary transition-all duration-300 appearance-none font-body text-on-surface"
                  v-model="discipline"
                  id="discipline"
                  name="discipline"
                  required
              >
                <option disabled selected value="">Selecciona disciplina</option>
                <option value="artes visuales">artes visuales</option>
                <option value="artes digitales">artes digitales</option>
                <option value="artes escénicas">artes escénicas</option>
              </select>
            </div>
            <!-- Email -->
            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="email">Email</label>
              <input
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-surface-container-highest"
                  v-model="email"
                  id="email"
                  name="email"
                  placeholder="derekcasa@gmail.com"
                  type="email"
                  required
              />
            </div>
            <!-- Password -->
            <div class="group">
              <label class="block font-headline text-on-surface-variant text-sm mb-1" for="password">Contraseña</label>
              <input
                  class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 focus:ring-0 focus:border-primary transition-all duration-300 placeholder:text-surface-container-highest"
                  v-model="password"
                  id="password"
                  name="password"
                  placeholder="•••••••••••"
                  type="password"
                  required
              />
            </div>
          </div>
          <!-- CTA Button -->
          <div class="pt-4">
            <button
                class="w-full bg-gradient-to-r from-primary to-primary-container text-on-primary font-label text-sm font-extrabold py-5 uppercase tracking-[0.2em] hover:opacity-90 active:scale-[0.98] transition-all duration-200"
                type="submit"
            >
              Crear una cuenta
            </button>
          </div>
        </form>
        <!-- Footer Link -->
        <footer class="pt-8 border-t border-surface-container-low flex flex-col sm:flex-row items-center gap-4">
          <p class="text-on-surface-variant font-body text-sm">¿Ya tienes una cuenta?</p>
          <a class="font-label text-primary font-bold text-xs uppercase tracking-widest border-b-2 border-primary-container pb-1 hover:border-primary transition-colors" href="http://localhost:8080/">
            Iniciar sesión
          </a>
        </footer>
      </div>
    </section>

    <!-- Visual Canvas Section -->
    <section class="hidden lg:block lg:w-[55%] relative overflow-hidden bg-surface-container-low">
      <!-- Background Image -->
      <img
          alt="dibujo en procreate by @disneyprince"
          class="absolute inset-0 w-full h-full object-cover"
          src= "../assets/karalangshark.webp"
      />
      <!-- Artistic Overlays -->
      <div class="absolute inset-0 bg-gradient-to-t from-primary/20 via-transparent to-transparent"></div>
      <!-- Editorial Content Overlay -->
      <div class="absolute bottom-24 left-24 right-24 space-y-6">
        <div class="h-px w-24 bg-primary-container"></div>
        <h2 class="text-6xl font-headline font-bold text-white leading-tight drop-shadow-2xl">
          Art Is <br />
          The Way Out.
        </h2>
        <div class="flex items-center gap-6 text-white/80 font-label text-xs uppercase tracking-[0.4em]">
          <span>Collection 04</span>
          <span class="h-1 w-1 bg-white/40 rounded-full"></span>
          <span>Curated Selection</span>
        </div>
      </div>
      <!-- Floating Glass Element -->
      <div class="absolute top-24 right-24 p-6 bg-surface/1 backdrop-blur-xl border border-white/10 w-64">
        <span class="material-symbols-outlined text-primary mb-4" data-icon="auto_awesome">auto_awesome</span>
        <p class="text-white font-headline italic text-lg">
          "Every artist was first an amateur."
        </p>
        <p class="text-white/60 font-label text-[10px] mt-4 uppercase tracking-widest">Ralph Waldo Emerson</p>
      </div>
    </section>
  </main>
</template>

<script>
export default {
  data() {
    return {
      fullName: "",
      discipline: "",
      email: "",
      password: ""
    };
  },
  methods: {
    handleRegister() {
      // Lógica para el registro de usuario
      console.log("Full Name:", this.fullName);
      console.log("Discipline:", this.discipline);
      console.log("Email:", this.email);
      console.log("Password:", this.password);
    }
  }
};
</script>

<style scoped>
</style>