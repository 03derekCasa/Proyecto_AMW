<template>
  <div class="bg-surface text-on-surface">
    <!-- TopAppBar -->
    <nav class="fixed top-0 right-0 w-[calc(100%-18rem)] z-50 flex justify-between items-center px-12 py-8 bg-[#FAF9F6]/70 dark:bg-stone-950/70 backdrop-blur-xl border-b-0">
      <div class="flex items-center gap-8">
        <h1 class="font-notoSerif italic text-xl text-stone-900 dark:text-stone-50">Art Make a Way</h1>
        <div class="hidden md:flex gap-6">
          <a class="text-[#A900A9] dark:text-[#FF00FF] font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Explore</a>
          <a class="text-stone-600 dark:text-stone-400 font-manrope font-medium hover:opacity-70 transition-opacity" href="#">Exhibitions</a>
        </div>
      </div>
      <div class="flex items-center gap-6">
        <span class="material-symbols-outlined text-on-surface cursor-pointer hover:opacity-70 transition-opacity" data-icon="notifications">notifications</span>
        <span class="material-symbols-outlined text-on-surface cursor-pointer hover:opacity-70 transition-opacity" data-icon="account_circle">account_circle</span>
      </div>
    </nav>

    <!-- SideNavBar -->
    <aside class="fixed left-0 top-0 h-full flex flex-col py-12 px-6 bg-[#FAF9F6] dark:bg-stone-950 w-72 border-r-0 z-50">
      <div class="mb-12 px-4">
        <div class="flex items-center gap-3 mb-2">
          <img alt="User profile curator photo" class="w-10 h-10 object-cover grayscale" :src="userProfileImage" />
          <div>
            <h2 class="font-notoSerif text-lg leading-none text-stone-900 dark:text-stone-50">{{ userName }}</h2>
            <p class="font-manrope text-[10px] uppercase tracking-widest text-stone-500">Art Make a Way</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 flex flex-col gap-2">
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="palette">palette</span>
          <span>Curated Feed</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-[#A900A9] dark:text-[#FF00FF] font-bold border-l-4 border-[#A900A9] pl-4 font-manrope uppercase tracking-widest text-[10px] translate-x-1 transition-transform" href="#">
          <span class="material-symbols-outlined" data-icon="brush">brush</span>
          <span>Portfolios</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="gallery_thumbnail">gallery_thumbnail</span>
          <span>Collections</span>
        </a>
        <a class="flex items-center gap-4 py-3 text-stone-500 dark:text-stone-400 pl-5 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
          <span class="material-symbols-outlined" data-icon="forum">forum</span>
          <span>Collaborations</span>
        </a>
      </nav>
      <div class="mt-auto px-4 space-y-6">
        <button class="w-full bg-primary text-on-primary py-3 font-manrope uppercase tracking-widest text-[10px] font-bold hover:opacity-90 transition-opacity">
          Upload Artwork
        </button>
        <div class="flex flex-col gap-2 pt-6 border-t border-stone-200 dark:border-stone-800">
          <a class="flex items-center gap-4 text-stone-500 dark:text-stone-400 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
            <span class="material-symbols-outlined text-sm" data-icon="settings">settings</span>
            <span>Settings</span>
          </a>
          <a class="flex items-center gap-4 text-stone-500 dark:text-stone-400 font-manrope uppercase tracking-widest text-[10px] hover:text-[#FF00FF] transition-colors duration-300" href="#">
            <span class="material-symbols-outlined text-sm" data-icon="info">info</span>
            <span>Exhibition Info</span>
          </a>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-72 pt-32 min-h-screen">
      <!-- Hero Header -->
      <header class="relative px-12 mb-32">
        <div class="w-full h-[512px] bg-surface-container-low relative overflow-hidden">
          <img class="w-full h-full object-cover grayscale opacity-40 mix-blend-multiply" :src="heroImage" />
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
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Works</span>
                <span class="font-notoSerif text-2xl">{{ userWorksCount }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Followers</span>
                <span class="font-notoSerif text-2xl">{{ userFollowers }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-manrope text-[10px] uppercase tracking-[0.2em] text-stone-400">Following</span>
                <span class="font-notoSerif text-2xl">{{ userFollowing }}</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Bio Section -->
      <section class="px-24 mb-32 grid grid-cols-12 gap-12">
        <div class="col-span-4 sticky top-48 self-start">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-primary mb-6">Artist Statement</p>
          <div class="font-notoSerif text-lg leading-relaxed text-on-surface-variant italic border-l-2 border-primary/20 pl-8">
            "My work explores the intersections of digital fragility and classical permanence. Each piece is a meditation on the fleeting nature of the contemporary image, rendered through the lens of timeless Italian brutalism."
          </div>
          <div class="mt-12 space-y-8">
            <button class="w-full py-4 px-12 bg-gradient-to-r from-primary to-primary-container text-on-primary font-manrope font-bold uppercase tracking-widest text-xs hover:scale-[1.02] transition-transform shadow-lg shadow-primary/10">
              Contact for Collaboration
            </button>
            <div class="flex gap-4">
              <span class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors" data-icon="share">share</span>
              <span class="material-symbols-outlined p-2 border border-outline-variant/30 text-on-surface cursor-pointer hover:bg-surface-container transition-colors" data-icon="language">language</span>
            </div>
          </div>
        </div>

        <!-- Art Gallery Grid -->
        <div class="col-span-8">
          <p class="font-manrope text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-12">Selected Works</p>
          <div class="grid grid-cols-2 gap-16">
            <div v-for="(artwork, index) in artworks" :key="index" class="space-y-6">
              <div class="bg-surface-container aspect-[3/4] relative overflow-hidden group">
                <img class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700 cursor-pointer" :src="artwork.image" />
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="font-manrope text-white uppercase tracking-widest text-[10px] border border-white px-4 py-2">View Piece</span>
                </div>
              </div>
              <div class="flex justify-between items-baseline">
                <h3 class="font-notoSerif text-xl italic">{{ artwork.title }}</h3>
                <span class="font-manrope text-[10px] text-stone-400 uppercase tracking-widest">{{ artwork.year }} • {{ artwork.medium }}</span>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div class="mt-32 flex justify-center">
            <button class="px-16 py-4 border-2 border-primary text-primary font-manrope font-bold uppercase tracking-widest text-xs hover:bg-primary hover:text-white transition-all">
              View Entire Portfolio
            </button>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="px-24 py-32 border-t border-stone-200 bg-surface-container-low">
        <div class="flex justify-between items-start">
          <div class="max-w-md">
            <h4 class="font-notoSerif italic text-3xl mb-6">Art Make a Way</h4>
            <p class="font-manrope text-sm text-stone-500 leading-relaxed">
              A curated digital space dedicated to the intersection of fine art and modern digital culture. We celebrate the artist as a primary architect of society.
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
      userName: 'The Curator',
      userWorksCount: 142,
      userFollowers: '12.4k',
      userFollowing: 840,
      heroImage: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAaAeYYot8QN6I-KeOCat80oxh_ILfzJGZ07_mk5NEOrAewbJq1fz5H1jFeH3TBiglVm9qJ355WPhFg9uVUqkuL8FXgei9OrtO616Qtsy4hBJPDO3ArRPWxgre2YFJh2Coz3BeAIh-ea4w6Ofci32k11s0lG3teL3BqOYwcEVm0l4LRwcVoTaGIOwaiGuU4lqO05YYb4ZtxTBHmayUmsYpJ3EEqqaAEdD5WYArYVqLqoFmjRlCmYFKvQR6iPZw4ij3xY8_0qJ9SHQ4',
      artworks: [
        { image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCDmlp-bMbV34fHAjlFUknicSeOWK7-X8N0YEQ7wlersjGAAIZv6yktBZBK2eP2SY7imSwUKWq-PwssfvPDA_Dxd00MZ1CpUDJQmwqca-M4EFNwVzRXCWYAIKzocfYwNuAaeZA70MPD0PmjTW0JUxiJhm_RBLLq1gbjePsEv13yT7ZqY5UilqORE8qMetlXvDP8VpAIiLTc293itOpowG8RHD2rJUzzgsARqPtTx9FHWcfxCknQGpiIT2dtHMDIu3J9bwTPHkE6KVE', title: 'Silent Echoes I', year: 2023, medium: 'Acrylic' },
        { image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDsD9HfRINFZKAre9xVycNoT_g19AQ5nJGj-RdBV_UtxbGt1FYZVvr_lb09vno_nB5sSPbND-poeZfdr9vbsFiY3JoSyZvmZxUQeMjEcFnnXkXDlI2IuNiLU-tsZn1CoUifdcATlXfDHmkDWwegMsbHOEeXMnKKvLSpDLd5NSaPRYPsT8d1Ra52x9s7827bhXFSzTxWd0JlI9nPHwc5FAQgK0eEoLj1frQVgDpj1l0YF0LHVAR7tk5jGlvTuW0IByeCY3910QbAEjU', title: 'Structure 04', year: 2024, medium: 'Mixed Media' }
      ]
    };
  }
};
</script>

<style scoped>
</style>