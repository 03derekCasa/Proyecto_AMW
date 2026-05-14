<template>
  <footer class="bg-[#FAF9F6] border-t border-stone-200 px-8 py-5 text-on-surface">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="flex items-center gap-2 font-manrope text-sm text-stone-600">
        <span aria-hidden="true" class="text-primary text-lg">©</span>
        <span class="font-bold">AMW</span>
        <span>2026</span>
      </div>

      <nav aria-label="Enlaces legales y ayuda" class="flex flex-col sm:flex-row items-center gap-4">
        <router-link
            to="/terms"
            class="font-manrope text-xs uppercase tracking-widest text-stone-600 hover:text-primary transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
        >
          Términos y condiciones
        </router-link>

        <router-link
            to="/help"
            class="font-manrope text-xs uppercase tracking-widest text-stone-600 hover:text-primary transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary focus-visible:outline-offset-4"
        >
          Ayuda al cliente
        </router-link>
      </nav>
    </div>
  </footer>
</template>

<script>
  export default {
    name: 'AppFooter',
  }
</script>
