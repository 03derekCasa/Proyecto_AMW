<template>
  <main class="grid grid-cols-1 md:grid-cols-2 min-h-screen">
    <div class="hidden md:flex flex-col justify-between p-12 bg-surface-container-low relative overflow-hidden">
      <div class="z-10">
        <span class="font-serif text-3xl font-bold tracking-tighter text-on-surface">AMW</span>
      </div>
      <div class="relative z-10 space-y-8">
        <h1 class="font-serif text-6xl leading-[1.1] text-on-surface">
          Where Art <br/>
          <span class="italic text-primary">Finds a Way.</span>
        </h1>
        <p class="font-body text-lg text-on-surface-variant max-w-md">
          Únete a nuestra comunidad de artistas y crea tu propia galeria virtual.
        </p>
      </div>
      <!--imagen -->
      <div class="absolute -right-20 top-1/4 w-[400px] h-[600px] shadow-2xl rotate-3 bg-surface-container-highest">
        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQR_iGWmK46Y40rWdwIV9GNHwVS70wOndE8aS4Fe63Dz170UjdTeeDg8uIO0IPcKLtl8p-15ifp63vVz4X9am5yowim6DjC_KErY4REuwRJuQc4GSUjnwFLJofzVSyqQHZ3IIyNHtBoffIWKovp62lJ9p-8QzWE1k5OFaTN74iLIofj6rIqYKbCKFxwQTGZHpg6I6buhxLag_2Sts2y6CTohowB1zq1NxZ7Zv6MUBvhFuNFF9iZH5JhPnTBp3N1NZZInaQW25j1hY"/>
      </div>
      <div class="z-10">
        <p class="font-label text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">© 2024 Art Makes a Way</p>
      </div>
    </div>
    <div class="flex flex-col justify-center items-center px-6 py-12 md:px-24 bg-surface">
      <div class="md:hidden mb-16">
        <span class="font-serif text-3xl font-bold tracking-tighter text-primary">AMW</span>
      </div>
      <div class="w-full max-w-sm">
        <header class="mb-12">
          <h2 class="font-serif text-4xl mb-4 text-on-surface">Bienvenido</h2>
          <p class="font-body text-on-surface-variant">Introduce tus credenciales para acceder a tu studio virtual.</p>
        </header>
        <form action="#" class="space-y-10" method="POST">
          <div class="relative group">
            <label class="font-serif text-sm font-semibold text-on-surface-variant block mb-1" for="email">Email</label>
            <input class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline focus:border-b-2 focus:border-primary focus:ring-0 transition-all py-3 px-0 font-body text-on-surface placeholder:text-zinc-300" id="email" name="email" placeholder="artist@gallery.com" required="" type="email"/>
          </div>
          <div class="relative group">
            <div class="flex justify-between items-center mb-1">
              <label class="font-serif text-sm font-semibold text-on-surface-variant block" for="password">Contraseña</label>
              <a class="font-label text-[10px] uppercase tracking-widest text-primary font-bold hover:opacity-70 transition-opacity" href="#">¿Olvidaste la contraseña?</a>
            </div>
            <input class="w-full bg-transparent border-t-0 border-x-0 border-b border-outline focus:border-b-2 focus:border-primary focus:ring-0 transition-all py-3 px-0 font-body text-on-surface placeholder:text-zinc-300" id="password" name="password" placeholder="••••••••" required="" type="password"/>
          </div>
          <div class="pt-4">
            <button class="w-full py-5 px-12 bg-gradient-to-r from-primary to-primary-container text-white font-label text-xs uppercase tracking-[0.2em] font-extrabold shadow-lg hover:shadow-primary/20 active:scale-[0.98] transition-all" type="submit">
              Enter Gallery
            </button>
          </div>
        </form>
        <div class="mt-12 text-center">
          <p class="font-body text-sm text-on-surface-variant">
            ¿No tienes cuenta?
            <a class="font-bold text-on-surface hover:text-primary transition-colors underline decoration-primary/30 underline-offset-4 ml-1" href="http://localhost:8080/register">Registrate</a>
          </p>
        </div>
      </div>

      <div class="w-full max-w-sm mt-16 pt-8 border-t border-outline-variant/20">
        <p class="text-center font-label text-[10px] uppercase tracking-widest text-on-surface-variant mb-6">O continua con</p>
        <div class="grid grid-cols-2 gap-4">
          <button class="flex items-center justify-center py-4 border border-outline hover:bg-surface-container-low transition-colors">
            <span class="font-label text-[10px] uppercase tracking-widest font-bold">Google</span>
          </button>
          <button class="flex items-center justify-center py-4 border border-outline hover:bg-surface-container-low transition-colors">
            <span class="font-label text-[10px] uppercase tracking-widest font-bold">Apple</span>
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {
    handleLogin() {
      // Aquí puedes manejar el proceso de login, como conectarse a una API
      console.log("Email:", this.email);
      console.log("Password:", this.password);
    },
  },
};
</script>

<style scoped>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  body { font-family: 'Manrope', sans-serif; background-color: #FAF9F6; }
  .font-serif { font-family: 'Noto Serif', serif; }
</style>