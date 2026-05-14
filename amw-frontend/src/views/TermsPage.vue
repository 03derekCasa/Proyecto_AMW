<template>
  <div class="bg-background text-on-surface min-h-screen">
    <!-- TopAppBar -->

    <AppTopBar @logout="handleLogout" />

    <!-- SideBar -->

    <AppSidebar />

    <!-- Main Content -->
    <main class="ml-20 pt-32 pb-12 px-8 min-h-screen" aria-labelledby="terms-title">
      <div class="max-w-3xl mx-auto">
        <!-- Sections -->
        <div class="space-y-20">
          <!-- Section 01 -->
          <section class="relative" aria-labelledby="section-acceptance">
            <span class="section-number" aria-hidden="true">01</span>

            <h3 id="section-acceptance" class="section-title">
              Aceptación de las condiciones
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                Al acceder, registrarte o utilizar Art Makes A Way (AMW), aceptas estas condiciones de uso,
                así como las normas de funcionamiento de la plataforma. Si no estás de acuerdo con alguna de
                estas condiciones, no debes utilizar la aplicación.
              </p>

              <p>
                AMW funciona como una red social y galería digital orientada a artistas, creadores y usuarios
                interesados en descubrir, publicar, comentar, guardar y compartir contenido artístico.
              </p>

              <p>
                La plataforma podrá actualizar estas condiciones cuando sea necesario para adaptarse a cambios
                técnicos, legales o funcionales. En caso de modificaciones relevantes, se informará a los usuarios
                a través de la aplicación o por otros medios razonables.
              </p>
            </div>
          </section>

          <!-- Section 02 -->
          <section class="relative" aria-labelledby="section-account">
            <span class="section-number" aria-hidden="true">02</span>

            <h3 id="section-account" class="section-title">
              Cuenta de usuario y registro
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                Para utilizar determinadas funcionalidades de AMW, como publicar obras, editar el perfil,
                comentar, dar “me gusta” o guardar contenido, será necesario crear una cuenta de usuario.
              </p>

              <p>
                El usuario se compromete a proporcionar información veraz, actualizada y suficiente para la
                correcta gestión de su cuenta. Asimismo, será responsable de mantener la confidencialidad de sus
                credenciales de acceso.
              </p>

              <p>
                AMW podrá suspender o limitar el acceso a aquellas cuentas que incumplan estas condiciones,
                utilicen identidades falsas, realicen actividades fraudulentas o perjudiquen a otros usuarios.
              </p>
            </div>
          </section>

          <!-- Section 03 -->
          <section class="relative" aria-labelledby="section-content">
            <span class="section-number" aria-hidden="true">03</span>

            <h3 id="section-content" class="section-title">
              Contenido publicado y propiedad intelectual
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                El usuario conserva la titularidad de los derechos de autor y propiedad intelectual sobre las
                obras, imágenes, textos, publicaciones y demás contenidos que suba a AMW.
              </p>

              <p>
                Al publicar contenido en la plataforma, el usuario concede a AMW una licencia no exclusiva,
                gratuita y limitada para mostrar, almacenar, organizar, promocionar y difundir dicho contenido
                dentro de la propia aplicación, siempre con la finalidad de permitir el funcionamiento normal
                del servicio.
              </p>

              <ul class="space-y-4 pt-4">
                <li class="flex gap-4">
                  <span class="text-[#FF00FF] font-bold" aria-hidden="true">/</span>
                  <span>
                    No está permitido subir obras ajenas sin autorización del titular de los derechos.
                  </span>
                </li>

                <li class="flex gap-4">
                  <span class="text-[#FF00FF] font-bold" aria-hidden="true">/</span>
                  <span>
                    No se permite presentar como propio contenido copiado, plagiado o generado por terceros.
                  </span>
                </li>

                <li class="flex gap-4">
                  <span class="text-[#FF00FF] font-bold" aria-hidden="true">/</span>
                  <span>
                    El usuario podrá solicitar la retirada de su contenido cuando lo considere necesario.
                  </span>
                </li>
              </ul>
            </div>
          </section>

          <!-- Section 04 -->
          <section class="relative" aria-labelledby="section-community">
            <span class="section-number" aria-hidden="true">04</span>

            <h3 id="section-community" class="section-title">
              Normas de comunidad
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                AMW busca mantener un entorno seguro, creativo y respetuoso. Las interacciones entre usuarios
                deben basarse en el respeto, la crítica constructiva y el reconocimiento del trabajo artístico.
              </p>

              <div class="p-8 bg-surface-container-low border-l-4 border-[#A900A9]">
                <p class="italic font-headline text-xl text-stone-900">
                  “No se permite el acoso, la suplantación de identidad, el discurso de odio, la publicación de
                  contenido ofensivo o la utilización de la plataforma para perjudicar a otros usuarios.”
                </p>
              </div>

              <p>
                AMW podrá moderar, ocultar o eliminar publicaciones, comentarios o perfiles cuando detecte
                incumplimientos de estas normas o reciba reclamaciones justificadas.
              </p>
            </div>
          </section>

          <!-- Section 05 -->
          <section class="relative" aria-labelledby="section-visibility">
            <span class="section-number" aria-hidden="true">05</span>

            <h3 id="section-visibility" class="section-title">
              Visibilidad, recomendaciones y funcionamiento social
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                AMW puede organizar el contenido en perfiles, galerías, categorías, publicaciones destacadas,
                colecciones o secciones recomendadas. La visibilidad de una obra podrá depender de factores como
                la fecha de publicación, la categoría, la interacción de otros usuarios o la relevancia artística
                dentro de la plataforma.
              </p>

              <p>
                Las funciones sociales, como comentarios, likes o favoritos, tienen como finalidad facilitar la
                interacción entre usuarios y la difusión del trabajo artístico. El uso abusivo o manipulado de
                estas funciones podrá dar lugar a limitaciones de cuenta.
              </p>
            </div>
          </section>

          <!-- Section 06 -->
          <section class="relative" aria-labelledby="section-payments">
            <span class="section-number" aria-hidden="true">06</span>

            <h3 id="section-payments" class="section-title">
              Pagos, donaciones y crowdfunding
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                En caso de que AMW incorpore funciones de pagos, compraventa de obras, aportaciones,
                donaciones o campañas de crowdfunding, dichas operaciones se realizarán conforme a la normativa
                de comercio electrónico y a las condiciones establecidas por las plataformas de pago integradas,
                como PayPal u otros proveedores equivalentes.
              </p>

              <p>
                Los usuarios deberán revisar las condiciones específicas de cada operación antes de confirmar
                cualquier pago. AMW podrá establecer políticas de cancelación, soporte y reclamaciones para
                garantizar una experiencia clara y segura.
              </p>
            </div>
          </section>

          <!-- Section 07 -->
          <section class="relative" aria-labelledby="section-privacy">
            <span class="section-number" aria-hidden="true">07</span>

            <h3 id="section-privacy" class="section-title">
              Protección de datos y privacidad
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                La aplicación cumple con la normativa vigente en materia de protección de datos personales,
                incluyendo el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica 3/2018 de
                Protección de Datos Personales y garantía de los derechos digitales.
              </p>

              <p>
                La plataforma podrá tratar datos necesarios para gestionar la cuenta de usuario, permitir el
                inicio de sesión, mostrar perfiles, almacenar publicaciones, gestionar comentarios, favoritos,
                imágenes y demás funcionalidades propias de AMW.
              </p>

              <p>
                El usuario podrá ejercer sus derechos de acceso, rectificación, supresión, limitación,
                oposición y portabilidad cuando proceda, mediante los canales de contacto o soporte habilitados
                por la plataforma.
              </p>

              <p>
                AMW podrá utilizar políticas de privacidad y cookies para informar de forma clara sobre el uso
                de datos, almacenamiento técnico, preferencias de usuario y mecanismos necesarios para el
                funcionamiento de la aplicación.
              </p>
            </div>
          </section>

          <!-- Section 08 -->
          <section class="relative" aria-labelledby="section-legal">
            <span class="section-number" aria-hidden="true">08</span>

            <h3 id="section-legal" class="section-title">
              Marco legal aplicable
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                AMW garantiza el cumplimiento de las leyes de propiedad intelectual en cuanto a la publicación,
                visualización y posible comercialización de obras artísticas, así como las políticas de uso justo
                y derechos de autor.
              </p>

              <p>
                En el ámbito de los servicios de la sociedad de la información, la plataforma se ajusta a la
                Ley 34/2002, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE),
                especialmente en lo relativo a obligaciones de información, comunicaciones electrónicas y
                prestación de servicios digitales.
              </p>

              <p>
                En materia de propiedad intelectual, se definen condiciones de uso que aclaran la autoría de
                las obras, los permisos de publicación, las limitaciones de reutilización y el procedimiento
                para gestionar reclamaciones o retiradas de contenido cuando proceda.
              </p>

              <p>
                Respecto a transacciones, aportaciones o funcionalidades económicas futuras, AMW establecerá
                términos claros sobre pagos, cancelaciones, reclamaciones y soporte, alineados con la normativa
                aplicable al comercio electrónico.
              </p>
            </div>
          </section>

          <!-- Section 09 -->
          <section class="relative" aria-labelledby="section-liability">
            <span class="section-number" aria-hidden="true">09</span>

            <h3 id="section-liability" class="section-title">
              Responsabilidad y limitaciones del servicio
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                AMW proporciona un entorno técnico para la publicación y difusión de contenido artístico, pero
                no se responsabiliza de la veracidad, originalidad o legalidad de los contenidos subidos por los
                usuarios.
              </p>

              <p>
                Cada usuario será responsable de las obras, textos, imágenes, enlaces, comentarios o datos que
                publique en la plataforma. En caso de reclamación por vulneración de derechos, AMW podrá revisar
                el contenido y adoptar las medidas correspondientes.
              </p>

              <p>
                La plataforma se ofrece procurando mantener la estabilidad, disponibilidad y seguridad del
                servicio, aunque no se garantiza que el funcionamiento sea ininterrumpido o libre de errores en
                todo momento.
              </p>
            </div>
          </section>

          <!-- Section 10 -->
          <section class="relative" aria-labelledby="section-contact">
            <span class="section-number" aria-hidden="true">10</span>

            <h3 id="section-contact" class="section-title">
              Contacto, soporte y reclamaciones
              <span class="section-dot" aria-hidden="true"></span>
            </h3>

            <div class="section-content">
              <p>
                Para comunicar incidencias, solicitar asistencia, ejercer derechos sobre datos personales o
                presentar reclamaciones relacionadas con contenidos publicados en AMW, el usuario podrá acudir
                a la sección de ayuda al cliente.
              </p>

              <p>
                Las reclamaciones se gestionarán de forma razonable, atendiendo a la información aportada por
                el usuario y a la normativa aplicable en materia de propiedad intelectual, protección de datos y
                servicios digitales.
              </p>

              <router-link
                  to="/help"
                  class="inline-flex mt-4 px-8 py-3 border-2 border-[#A900A9] text-[#A900A9] font-manrope uppercase tracking-widest text-[10px] hover:bg-[#A900A9] hover:text-white transition-all"
              >
                Ir a ayuda al cliente
              </router-link>
            </div>
          </section>
        </div>
      </div>
    </main>

    <!-- Decoration -->
    <div class="fixed bottom-12 right-12 w-32 h-32 opacity-10 pointer-events-none" aria-hidden="true">
      <div class="w-full h-full border-r-2 border-b-2 border-[#FF00FF]"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TermsPage',

  data() {
    return {
      accepted: false,
    }
  },

  methods: {
    acceptTerms() {
      this.accepted = true
      localStorage.setItem('amw_terms_accepted', 'true')
    },

    printPage() {
      window.print()
    },

    handleLogout() {
      localStorage.removeItem('amw_token')
      localStorage.removeItem('amw_user')
      this.$router.push('/login')
    },
  },
}
</script>

<style scoped>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }

  .section-number {
    position: absolute;
    left: -4rem;
    top: 0;
    font-family: 'Noto Serif', serif;
    font-style: italic;
    font-size: 2.25rem;
    color: #f5f5f4;
    user-select: none;
  }

  .section-title {
    font-family: 'Noto Serif', serif;
    font-size: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .section-dot {
    height: 0.5rem;
    width: 0.5rem;
    background-color: #ff00ff;
    display: inline-block;
  }

  .section-content {
    font-family: 'Manrope', sans-serif;
    font-size: 1.125rem;
    line-height: 1.75;
    color: #44403c;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  ::-webkit-scrollbar {
    width: 4px;
  }

  ::-webkit-scrollbar-track {
    background: #FAF9F6;
  }

  ::-webkit-scrollbar-thumb {
    background: #A900A9;
  }
</style>