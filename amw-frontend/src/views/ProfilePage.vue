<template>
  <div class="bg-surface text-on-surface">
    <!-- Barra superior -->
    <nav class="fixed top-0 right-0 w-[calc(100%-18rem)] z-50 flex justify-between items-center px-12 py-8 bg-[#FAF9F6]/70 dark:bg-stone-950/70 backdrop-blur-xl border-b-0">
      <div class="flex items-center gap-8">
        <div class="hidden md:flex gap-6">
          <a class="text-[#A900A9] dark:text-[#FF00FF] font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Perfil</a>
          <a class="text-stone-600 dark:text-stone-400 font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Explorar</a>
          <a class="text-stone-600 dark:text-stone-400 font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Mensajes</a>
          <a class="text-stone-600 dark:text-stone-400 font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Mensajes</a>

        </div>
      </div>
      <div class="flex items-center gap-6">
        <span class="material-symbols-outlined text-on-surface cursor-pointer hover:opacity-70 transition-opacity" data-icon="notifications">notifications</span>
        <span class="material-symbols-outlined text-on-surface cursor-pointer hover:opacity-70 transition-opacity" data-icon="account_circle">account_circle</span>
      </div>
    </nav>

    <!-- Barra lateral de navegación -->
    <aside class="fixed left-0 top-0 h-full flex flex-col py-12 px-6 bg-[#FAF9F6] dark:bg-stone-950 w-72 border-r-0 z-50">
      <div class="mb-12 px-4">
        <div class="flex items-center gap-3 mb-2">
          <img alt="Foto de perfil del comisario de usuario" class="w-10 h-10 object-cover grayscale" :src="userProfileImage" />
          <div>
            <h2 class="font-notoSerif text-lg leading-none text-stone-900 dark:text-stone-50">{{ userName }}</h2>
            <p class="font-manrope text-[10px] uppercase tracking-widest text-stone-500">AMW</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 flex flex-col gap-2">
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="palette">palette</span>
          <span>Feed</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-[#A900A9] dark:text-[#FF00FF] font-bold border-l-4 border-[#A900A9] pl-4 font-manrope uppercase tracking-widest text-[10px] translate-x-1 transition-transform" href="#">
          <span class="material-symbols-outlined" data-icon="brush">brush</span>
          <span>Portfolio</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="gallery_thumbnail">gallery_thumbnail</span>
          <span>Colecciones</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="forum">forum</span>
          <span>Colaboraciones</span>
        </a>
      </nav>
      <div class="mt-auto px-4 space-y-6">
        <button class="w-full bg-primary text-on-primary py-3 font-manrope uppercase tracking-widest text-[10px] font-bold hover:opacity-90 transition-opacity">
          Subir obra
        </button>
        <div class="flex flex-col gap-2 pt-6 border-t border-stone-200 dark:border-stone-800">
          <a class="flex items-center gap-4 text-stone-500 dark:text-stone-400 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
            <span class="material-symbols-outlined text-sm" data-icon="settings">settings</span>
            <span>Configuración</span>
          </a>
          <a class="flex items-center gap-4 text-stone-500 dark:text-stone-400 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
            <span class="material-symbols-outlined text-sm" data-icon="info">info</span>
            <span>Información de la exposición</span>
          </a>
        </div>
      </div>
    </aside>

    <!-- Contenido principal -->
    <main class="ml-72 pt-32 min-h-screen">
      <!-- Cabecera principal -->
      <header class="relative px-12 mb-32">
        <div class="w-full h-[512px] bg-surface-container-low relative overflow-hidden">
          <img class="w-full h-full object-cover grayscale opacity-70 mix-blend-multiply" :src="heroImage" />
          <div class="absolute inset-0 bg-gradient-to-b from-transparent to-surface/80"></div>
        </div>
        <div class="absolute bottom-[-80px] left-24 flex items-end gap-12">
          <div class="relative">
            <div class="w-48 h-64 bg-stone-200 overflow-hidden border-8 border-surface shadow-2xl">
              <img class="w-full h-full object-cover" :src="userProfileImage" />
            </div>
          </div>
          <div class="mb-8">
            <h2 class="font-notoSerif text-6xl italic text-on-surface mb-2 tracking-tight">{{ userName }}</h2>
            <div class="flex gap-8 mt-6">
              <div class="flex flex-col">
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Obras</span>
                <span class="font-notoSerif text-2xl">{{ userWorksCount }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Seguidores</span>
                <span class="font-notoSerif text-2xl">{{ userFollowers }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Siguiendo</span>
                <span class="font-notoSerif text-2xl">{{ userFollowing }}</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Sección de biografía -->
      <section class="px-24 mb-32 grid grid-cols-12 gap-12">
        <div class="col-span-4 sticky top-48 self-start">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-6">Descripción</p>
          <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
            "Mi obra explora las intersecciones entre la fragilidad digital y la permanencia clásica. Cada pieza es una meditación sobre la naturaleza efímera de la imagen contemporánea"
          </div>
          <div class="mt-12 space-y-8">
            <button class="w-full py-4 px-12 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10">
              Contactar
            </button>
            <div class="flex gap-4">
              <span class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors" data-icon="share">share</span>
              <span class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors" data-icon="language">language</span>
            </div>
          </div>
        </div>

        <!-- Cuadrícula de galería de arte -->
        <div class="col-span-8">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">Obras seleccionadas</p>
          <div class="grid grid-cols-2 gap-16">
            <div v-for="(artwork, index) in artworks" :key="index" class="space-y-6">
              <div class="bg-surface-container aspect-[3/4] relative overflow-hidden group">
                <img class="w-full h-full object-cover transition-all duration-700 cursor-pointer" :src="artwork.image" />
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="font-manrope text-white uppercase tracking-widest text-[10px] border border-white px-4 py-2">Ver obra</span>
                </div>
              </div>
              <div class="flex justify-between items-baseline">
                <h3 class="font-notoSerif text-xl italic">{{ artwork.title }}</h3>
                <span class="font-manrope text-[10px] text-stone-400 uppercase tracking-widest">{{ artwork.year }} • {{ artwork.medium }}</span>
              </div>
            </div>
          </div>

          <!-- Botón de cargar más -->
          <div class="mt-32 flex justify-center">
            <button class="px-16 py-4 border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all">
              Ver portfolio completo
            </button>
          </div>
        </div>
      </section>

      <!-- Pie de página -->
      <footer class="px-24 py-32 border-t border-stone-200 bg-surface-container-low">
        <div class="flex justify-between items-start">
          <div class="max-w-md">
            <h4 class="font-notoSerif italic text-3xl mb-6">AMW</h4>
            <p class="font-manrope text-sm text-stone-500 leading-relaxed">
              Un espacio digital dedicado a la intersección entre las bellas artes y la cultura digital moderna. Celebramos al artista como arquitecto principal de la sociedad.
            </p>
          </div>
        </div>
      </footer>
    </main>
  </div>
</template>

<script>
export default {
  data() {
    return {
      userProfileImage: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAnN8llVhABRCAELYCrGshxCVt0pi356pl92ksBpb6iL7ivKbRmCtRXFZNZ9AH1GkUMk8VdY2b_rd89rfx7TLsmhYrsrJFzgFyV-vcLCIqfAbUI7Nbm-URV8Bnt_HhpJsOdPBHapz_lRi3hx-9CS742wpwle4wWBd-G6D8XnBTKe2Tm5g1YIldeoGtoEAMyJZwd9ScJRDx4ri-YVGE0zIQjmurd12QS6--vYQkbsjeSvMGqPuxZWW0HH1WkJArX-aA6RSPUFkNCjlU',
      userName: 'Derek Casa',
      userWorksCount: 142,
      userFollowers: '12.4k',
      userFollowing: 840,
      heroImage: 'https://www.guiarepsol.com/content/dam/repsol-guia/contenidos-imagenes/viajar/nos-gusta/nuevas-exposiciones-madrid/gr-cms-media-featured_images-none-867ebd3f-4b8b-4725-afe5-ea38e18aedec-saura-3.jpg',
      artworks: [
        {
          image: '//upload.wikimedia.org/wikipedia/commons/thumb/4/40/The_Kiss_-_Gustav_Klimt_-_Google_Cultural_Institute.jpg/330px-The_Kiss_-_Gustav_Klimt_-_Google_Cultural_Institute.jpg',
          title: 'El beso',
          year: 2023,
          medium: 'Acrílico'
        },
        {
          image: 'https://upload.wikimedia.org/wikipedia/commons/archive/7/70/20190714213902%21Los_nen%C3%BAfares_%28Monet%29.jpg',
          title: 'Nenufares',
          year: 2024,
          medium: 'Técnica mixta'
        }
      ]
    };
  }
};
</script>

<style scoped>
</style>