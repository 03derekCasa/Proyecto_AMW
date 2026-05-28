<template>
  <div class="bg-background text-on-surface min-h-screen">

    <main class="ml-10 pt-16 pb-16 px-6 sm:px-10 min-h-screen" aria-labelledby="terms-title">
      <div class="max-w-3xl mx-auto">
        <header class="mb-20">
          <div class="flex items-center gap-4 mb-5">
            <span class="w-12 h-px bg-[#FF00FF]" aria-hidden="true"></span>
            <p class="font-manrope uppercase tracking-[0.3em] text-[10px] text-[#A900A9] font-bold">
              {{ copy.eyebrow }}
            </p>
          </div>

          <h1 id="terms-title" class="font-headline italic text-5xl sm:text-6xl text-on-background leading-tight mb-6">
            {{ copy.title }}
          </h1>

          <p class="font-manrope text-base sm:text-lg leading-relaxed text-stone-600 max-w-2xl">
            {{ copy.introduction }}
          </p>
        </header>

        <div class="space-y-20">
          <section
              v-for="section in copy.sections"
              :key="section.number"
              class="relative"
              :aria-labelledby="`terms-section-${section.number}`"
          >
            <span class="section-number" aria-hidden="true">{{ section.number }}</span>

            <h2 :id="`terms-section-${section.number}`" class="section-title">
              {{ section.title }}
              <span class="section-dot" aria-hidden="true"></span>
            </h2>

            <div class="section-content">
              <p v-for="(paragraph, index) in section.paragraphs" :key="index">
                {{ paragraph }}
              </p>

              <ul v-if="section.bullets" class="space-y-4 pt-3">
                <li v-for="bullet in section.bullets" :key="bullet" class="flex gap-4">
                  <span class="text-[#FF00FF] font-bold" aria-hidden="true">/</span>
                  <span>{{ bullet }}</span>
                </li>
              </ul>

              <blockquote
                  v-if="section.quote"
                  class="p-7 bg-surface-container-low border-l-4 border-[#A900A9]"
              >
                <p class="italic font-headline text-xl text-stone-900">
                  “{{ section.quote }}”
                </p>
              </blockquote>

              <router-link
                  v-if="section.link"
                  :to="section.link.to"
                  class="inline-flex self-start mt-4 px-8 py-3 border-2 border-[#A900A9] text-[#A900A9] font-manrope uppercase tracking-widest text-[10px] hover:bg-[#A900A9] hover:text-white transition-all"
              >
                {{ section.link.text }}
              </router-link>
            </div>
          </section>
        </div>

        <footer class="mt-24 pt-10 border-t border-stone-200">
          <p class="font-manrope text-sm leading-relaxed text-stone-500">
            {{ copy.footer }}
          </p>
        </footer>
      </div>
    </main>

    <div class="fixed bottom-12 right-12 w-32 h-32 opacity-10 pointer-events-none" aria-hidden="true">
      <div class="w-full h-full border-r-2 border-b-2 border-[#FF00FF]"></div>
    </div>
  </div>
</template>

<script>
import { logoutUser } from '@/services/authService'

const termsCopies = {
  es: {
    eyebrow: 'Marco legal y condiciones',
    title: 'Términos y condiciones',
    introduction: 'Estas condiciones regulan el acceso y uso de Art Makes A Way (AMW), una plataforma digital concebida para compartir, descubrir y difundir contenido artístico.',
    footer: 'Al continuar utilizando AMW, confirmas que has leído y comprendido estas condiciones de uso y las políticas aplicables a la plataforma.',
    sections: [
      {
        number: '01',
        title: 'Aceptación de las condiciones',
        paragraphs: [
          'Al acceder, registrarte o utilizar Art Makes A Way (AMW), aceptas estas condiciones de uso y las normas de funcionamiento de la plataforma. Si no estás de acuerdo con alguna de ellas, no debes utilizar la aplicación.',
          'AMW funciona como una red social y galería digital orientada a artistas, creadores y personas interesadas en descubrir, publicar, comentar, guardar y compartir contenido artístico.',
          'La plataforma podrá actualizar estas condiciones para adaptarse a cambios técnicos, legales o funcionales. Cuando las modificaciones sean relevantes, se informará a los usuarios por medios razonables.'
        ]
      },
      {
        number: '02',
        title: 'Cuenta de usuario y registro',
        paragraphs: [
          'Para utilizar funcionalidades como publicar obras, editar el perfil, comentar, dar me gusta o guardar contenido, será necesario disponer de una cuenta de usuario.',
          'El usuario se compromete a facilitar información veraz y actualizada, y será responsable de mantener la confidencialidad de sus credenciales de acceso.',
          'AMW podrá suspender o limitar las cuentas que incumplan estas condiciones, realicen actividades fraudulentas o perjudiquen a otros usuarios.'
        ]
      },
      {
        number: '03',
        title: 'Contenido publicado y propiedad intelectual',
        paragraphs: [
          'El usuario conserva la titularidad de los derechos de autor y de propiedad intelectual sobre las obras, imágenes, textos y demás contenidos que publique en AMW.',
          'Al publicar contenido, el usuario concede a AMW una licencia no exclusiva, gratuita y limitada para mostrar, almacenar, organizar y difundir dicho contenido dentro de la plataforma, únicamente para permitir el funcionamiento normal del servicio.'
        ],
        bullets: [
          'No está permitido subir obras ajenas sin autorización del titular de los derechos.',
          'No se permite presentar como propio contenido copiado, plagiado o atribuido falsamente.',
          'El usuario podrá solicitar la retirada de su contenido cuando corresponda.'
        ]
      },
      {
        number: '04',
        title: 'Normas de comunidad',
        paragraphs: [
          'AMW busca mantener un entorno seguro, creativo y respetuoso. Las interacciones deben basarse en el respeto, la crítica constructiva y el reconocimiento del trabajo artístico.',
          'AMW podrá moderar, ocultar o eliminar publicaciones, comentarios o perfiles cuando detecte incumplimientos de estas normas o reciba reclamaciones justificadas.'
        ],
        quote: 'No se permite el acoso, la suplantación de identidad, el discurso de odio, el contenido ofensivo ni el uso de la plataforma para perjudicar a otros usuarios.'
      },
      {
        number: '05',
        title: 'Visibilidad y funcionamiento social',
        paragraphs: [
          'AMW puede organizar el contenido en perfiles, galerías, categorías, colecciones o secciones recomendadas. La visibilidad de una obra podrá depender de la fecha de publicación, su categoría o la interacción de la comunidad.',
          'Las funciones sociales, como comentarios, likes o favoritos, facilitan la interacción y la difusión artística. Su utilización abusiva o manipulada podrá dar lugar a limitaciones de cuenta.'
        ]
      },
      {
        number: '06',
        title: 'Pagos, donaciones y crowdfunding',
        paragraphs: [
          'Si AMW incorpora pagos, compraventa de obras, donaciones o campañas de crowdfunding, estas operaciones se realizarán conforme a la normativa de comercio electrónico y a las condiciones de las plataformas de pago integradas, como PayPal u otros proveedores equivalentes.',
          'Los usuarios deberán revisar las condiciones específicas antes de confirmar cualquier operación económica. AMW podrá establecer políticas de cancelación, soporte y reclamaciones.'
        ]
      },
      {
        number: '07',
        title: 'Protección de datos y privacidad',
        paragraphs: [
          'La aplicación cumple con la normativa vigente en materia de protección de datos personales, incluyendo el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica 3/2018 de Protección de Datos Personales y garantía de los derechos digitales.',
          'La plataforma podrá tratar los datos necesarios para gestionar cuentas, inicios de sesión, perfiles, publicaciones, comentarios, favoritos e imágenes.',
          'El usuario podrá ejercer sus derechos de acceso, rectificación, supresión, limitación, oposición y portabilidad cuando corresponda, mediante los canales de soporte habilitados.'
        ]
      },
      {
        number: '08',
        title: 'Marco legal aplicable',
        paragraphs: [
          'AMW garantiza el cumplimiento de las leyes de propiedad intelectual en cuanto a la publicación, visualización y posible comercialización de obras artísticas, así como las políticas de uso justo y derechos de autor.',
          'En el ámbito de los servicios de la sociedad de la información, la plataforma se ajusta a la Ley 34/2002, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), especialmente respecto a obligaciones de información y prestación de servicios digitales.',
          'Respecto a transacciones o funcionalidades económicas futuras, AMW establecerá términos claros sobre pagos, cancelaciones, reclamaciones y soporte, alineados con la normativa aplicable.'
        ]
      },
      {
        number: '09',
        title: 'Responsabilidad y limitaciones del servicio',
        paragraphs: [
          'AMW proporciona un entorno técnico para la publicación y difusión de contenido artístico, pero no se responsabiliza de la veracidad, originalidad o legalidad de los contenidos subidos por los usuarios.',
          'Cada usuario será responsable de las obras, textos, imágenes, enlaces, comentarios o datos que publique. En caso de reclamación por vulneración de derechos, AMW podrá revisar el contenido y adoptar las medidas correspondientes.',
          'La plataforma procurará mantener la estabilidad, disponibilidad y seguridad del servicio, aunque no garantiza un funcionamiento ininterrumpido o libre de errores en todo momento.'
        ]
      },
      {
        number: '10',
        title: 'Contacto, soporte y reclamaciones',
        paragraphs: [
          'Para comunicar incidencias, solicitar asistencia, ejercer derechos sobre datos personales o presentar reclamaciones relacionadas con contenidos publicados en AMW, el usuario podrá acudir a la sección de ayuda al cliente.',
          'Las reclamaciones se gestionarán de forma razonable, atendiendo a la información aportada por el usuario y a la normativa aplicable.'
        ],
        link: { to: '/help', text: 'Ir a ayuda al cliente' }
      }
    ]
  },
  en: {
    eyebrow: 'Legal framework and terms',
    title: 'Terms and conditions',
    introduction: 'These terms govern access to and use of Art Makes A Way (AMW), a digital platform designed to share, discover and promote artistic content.',
    footer: 'By continuing to use AMW, you confirm that you have read and understood these terms of use and the policies applicable to the platform.',
    sections: [
      { number: '01', title: 'Acceptance of the terms', paragraphs: ['By accessing, registering for or using Art Makes A Way (AMW), you agree to these terms of use and the platform rules. If you disagree with any of them, you must not use the application.', 'AMW operates as a social network and digital gallery for artists, creators and people interested in discovering, publishing, commenting on, saving and sharing artistic content.', 'The platform may update these terms to adapt to technical, legal or functional changes. Users will be informed by reasonable means when changes are significant.'] },
      { number: '02', title: 'User account and registration', paragraphs: ['An account is required to use features such as publishing artworks, editing a profile, commenting, liking or saving content.', 'Users undertake to provide truthful and up-to-date information and remain responsible for keeping their access credentials confidential.', 'AMW may suspend or restrict accounts that breach these terms, perform fraudulent activities or harm other users.'] },
      { number: '03', title: 'Published content and intellectual property', paragraphs: ['Users retain ownership of the copyright and intellectual property rights over the artworks, images, texts and other content they publish on AMW.', 'By publishing content, users grant AMW a non-exclusive, royalty-free and limited licence to display, store, organise and disseminate that content within the platform solely to operate the service.'], bullets: ['Uploading third-party artworks without the rights holder’s permission is not allowed.', 'Presenting copied, plagiarised or falsely attributed content as one’s own is not allowed.', 'Users may request the removal of their content when applicable.'] },
      { number: '04', title: 'Community standards', paragraphs: ['AMW aims to maintain a safe, creative and respectful environment. Interactions must be based on respect, constructive criticism and recognition of artistic work.', 'AMW may moderate, hide or remove posts, comments or profiles when these standards are breached or justified reports are received.'], quote: 'Harassment, impersonation, hate speech, offensive content and use of the platform to harm other users are not allowed.' },
      { number: '05', title: 'Visibility and social features', paragraphs: ['AMW may organise content into profiles, galleries, categories, collections or recommended sections. The visibility of a work may depend on its publication date, category or community engagement.', 'Social features such as comments, likes and favourites facilitate interaction and artistic dissemination. Abusive or manipulated use may lead to account restrictions.'] },
      { number: '06', title: 'Payments, donations and crowdfunding', paragraphs: ['If AMW introduces payments, artwork sales, donations or crowdfunding campaigns, these operations will comply with e-commerce regulations and the terms of integrated payment platforms such as PayPal or equivalent providers.', 'Users must review the applicable conditions before confirming an economic operation. AMW may establish cancellation, support and complaints policies.'] },
      { number: '07', title: 'Data protection and privacy', paragraphs: ['The application complies with the regulations in force concerning personal data protection, including the General Data Protection Regulation (GDPR) and Spanish Organic Law 3/2018 on Personal Data Protection and guarantee of digital rights.', 'The platform may process data necessary to manage accounts, logins, profiles, publications, comments, favourites and images.', 'Users may exercise their rights of access, rectification, erasure, restriction, objection and portability, where applicable, through the available support channels.'] },
      { number: '08', title: 'Applicable legal framework', paragraphs: ['AMW ensures compliance with intellectual property laws regarding the publication, display and possible commercialisation of artistic works, as well as fair-use and copyright policies.', 'With regard to information society services, the platform complies with Spanish Law 34/2002 on Information Society Services and Electronic Commerce (LSSI-CE), particularly information duties and the provision of digital services.', 'For future transactions or economic features, AMW will establish clear terms concerning payments, cancellations, complaints and support, aligned with applicable legislation.'] },
      { number: '09', title: 'Liability and service limitations', paragraphs: ['AMW provides a technical environment for publishing and disseminating artistic content, but is not responsible for the truthfulness, originality or legality of content uploaded by users.', 'Each user is responsible for the artworks, texts, images, links, comments or data they publish. In case of a rights infringement complaint, AMW may review the content and take appropriate action.', 'The platform will strive to maintain service stability, availability and security, but does not guarantee uninterrupted or error-free operation at all times.'] },
      { number: '10', title: 'Contact, support and complaints', paragraphs: ['To report incidents, request assistance, exercise personal data rights or submit complaints relating to content published on AMW, users may access the customer support section.', 'Complaints will be handled reasonably, taking into account the information supplied by the user and the applicable regulations.'], link: { to: '/help', text: 'Go to customer support' } }
    ]
  },
  fr: {
    eyebrow: 'Cadre juridique et conditions',
    title: 'Conditions générales',
    introduction: 'Ces conditions régissent l’accès et l’utilisation de Art Makes A Way (AMW), une plateforme numérique conçue pour partager, découvrir et diffuser du contenu artistique.',
    footer: 'En continuant à utiliser AMW, vous confirmez avoir lu et compris les présentes conditions d’utilisation et les politiques applicables à la plateforme.',
    sections: [
      { number: '01', title: 'Acceptation des conditions', paragraphs: ['En accédant à Art Makes A Way (AMW), en vous inscrivant ou en utilisant la plateforme, vous acceptez les présentes conditions et les règles de fonctionnement. Si vous n’êtes pas d’accord avec celles-ci, vous ne devez pas utiliser l’application.', 'AMW fonctionne comme un réseau social et une galerie numérique destinés aux artistes, aux créateurs et aux personnes souhaitant découvrir, publier, commenter, enregistrer et partager des contenus artistiques.', 'La plateforme peut mettre à jour ces conditions afin de s’adapter à des évolutions techniques, juridiques ou fonctionnelles. Les utilisateurs seront informés raisonnablement des modifications importantes.'] },
      { number: '02', title: 'Compte utilisateur et inscription', paragraphs: ['Un compte est nécessaire pour publier des œuvres, modifier un profil, commenter, aimer ou enregistrer du contenu.', 'L’utilisateur s’engage à fournir des informations exactes et actualisées et reste responsable de la confidentialité de ses identifiants.', 'AMW peut suspendre ou limiter les comptes qui enfreignent ces conditions, commettent des actes frauduleux ou portent préjudice à d’autres utilisateurs.'] },
      { number: '03', title: 'Contenu publié et propriété intellectuelle', paragraphs: ['L’utilisateur conserve la propriété des droits d’auteur et des droits de propriété intellectuelle relatifs aux œuvres, images, textes et autres contenus publiés sur AMW.', 'En publiant un contenu, l’utilisateur accorde à AMW une licence non exclusive, gratuite et limitée permettant d’afficher, stocker, organiser et diffuser ce contenu au sein de la plateforme uniquement pour assurer le fonctionnement du service.'], bullets: ['Il est interdit de publier les œuvres d’un tiers sans l’autorisation du titulaire des droits.', 'Il est interdit de présenter comme sien un contenu copié, plagié ou faussement attribué.', 'L’utilisateur peut demander le retrait de son contenu lorsque cela est applicable.'] },
      { number: '04', title: 'Règles de la communauté', paragraphs: ['AMW souhaite maintenir un environnement sûr, créatif et respectueux. Les échanges doivent reposer sur le respect, la critique constructive et la reconnaissance du travail artistique.', 'AMW peut modérer, masquer ou supprimer des publications, commentaires ou profils en cas de non-respect de ces règles ou de signalements justifiés.'], quote: 'Le harcèlement, l’usurpation d’identité, les discours haineux, les contenus offensants et l’utilisation de la plateforme pour nuire à d’autres utilisateurs sont interdits.' },
      { number: '05', title: 'Visibilité et fonctions sociales', paragraphs: ['AMW peut organiser les contenus dans des profils, galeries, catégories, collections ou sections recommandées. La visibilité d’une œuvre peut dépendre de sa date de publication, de sa catégorie ou de l’interaction de la communauté.', 'Les fonctions sociales telles que les commentaires, les mentions J’aime ou les favoris facilitent les échanges et la diffusion artistique. Toute utilisation abusive ou manipulée peut entraîner des restrictions de compte.'] },
      { number: '06', title: 'Paiements, dons et financement participatif', paragraphs: ['Si AMW intègre des paiements, la vente d’œuvres, des dons ou des campagnes de financement participatif, ces opérations respecteront la réglementation du commerce électronique et les conditions des plateformes de paiement intégrées, telles que PayPal ou des fournisseurs équivalents.', 'Les utilisateurs doivent consulter les conditions spécifiques avant de confirmer toute opération économique. AMW peut définir des politiques d’annulation, d’assistance et de réclamation.'] },
      { number: '07', title: 'Protection des données et vie privée', paragraphs: ['L’application respecte la réglementation en vigueur en matière de protection des données personnelles, notamment le Règlement général sur la protection des données (RGPD) et la loi organique espagnole 3/2018 relative à la protection des données personnelles et à la garantie des droits numériques.', 'La plateforme peut traiter les données nécessaires à la gestion des comptes, connexions, profils, publications, commentaires, favoris et images.', 'L’utilisateur peut exercer ses droits d’accès, de rectification, d’effacement, de limitation, d’opposition et de portabilité, lorsque cela s’applique, via les canaux d’assistance disponibles.'] },
      { number: '08', title: 'Cadre juridique applicable', paragraphs: ['AMW garantit le respect des lois relatives à la propriété intellectuelle concernant la publication, l’affichage et l’éventuelle commercialisation d’œuvres artistiques, ainsi que les politiques relatives au droit d’auteur.', 'Dans le domaine des services de la société de l’information, la plateforme respecte la loi espagnole 34/2002 sur les services de la société de l’information et le commerce électronique (LSSI-CE), en particulier les obligations d’information et la prestation de services numériques.', 'Pour de futures transactions ou fonctionnalités économiques, AMW établira des conditions claires sur les paiements, annulations, réclamations et l’assistance, conformément à la réglementation applicable.'] },
      { number: '09', title: 'Responsabilité et limitations du service', paragraphs: ['AMW fournit un environnement technique permettant de publier et de diffuser du contenu artistique, mais n’est pas responsable de la véracité, de l’originalité ou de la légalité des contenus téléchargés par les utilisateurs.', 'Chaque utilisateur est responsable des œuvres, textes, images, liens, commentaires ou données publiés. En cas de réclamation pour violation de droits, AMW peut examiner le contenu et prendre les mesures appropriées.', 'La plateforme s’efforcera de maintenir la stabilité, la disponibilité et la sécurité du service, sans garantir un fonctionnement ininterrompu ou exempt d’erreurs à tout moment.'] },
      { number: '10', title: 'Contact, assistance et réclamations', paragraphs: ['Pour signaler un incident, demander de l’aide, exercer ses droits en matière de données personnelles ou présenter une réclamation relative à un contenu publié sur AMW, l’utilisateur peut accéder à la rubrique d’assistance client.', 'Les réclamations seront traitées de manière raisonnable, en tenant compte des informations fournies par l’utilisateur et de la réglementation applicable.'], link: { to: '/help', text: 'Accéder à l’assistance client' } }
    ]
  }
}

export default {
  name: 'TermsPage',

  computed: {
    copy() {
      return termsCopies[this.$i18n.locale] || termsCopies.es
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
  },
}
</script>

<style scoped>
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

@media (max-width: 768px) {
  .section-number {
    position: static;
    display: block;
    margin-bottom: 0.75rem;
    color: #e7e5e4;
  }

  .section-content {
    font-size: 1rem;
  }
}
</style>
