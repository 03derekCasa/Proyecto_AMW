<template>
  <div class="bg-surface text-on-surface selection:bg-primary-container selection:text-on-primary-container min-h-screen">
    <AppSidebar />

    <!-- Main Content -->
    <main class="ml-20 pt-0 h-screen flex overflow-hidden">
      <!-- Conversation List -->
      <section class="w-[26rem] xl:w-[28rem] flex flex-col border-r border-outline-variant/15 bg-surface-container-low">        <div class="p-8 flex flex-col h-full">
          <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl social-title">
              {{ ui.title }}
            </h1>

            <button
                type="button"
                class="bg-primary text-white w-10 h-10 rounded-full flex items-center justify-center hover:opacity-90 transition-opacity active:scale-95"
                :aria-label="ui.newMessage"
                @click="toggleNewMessagePanel"
            >
              <span class="material-symbols-outlined" aria-hidden="true">
                add
              </span>
            </button>
          </div>

          <!-- Nuevo mensaje -->
          <div
              v-if="showNewMessagePanel"
              class="mb-6 bg-white border border-outline-variant/30 p-4 shadow-sm"
          >
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-manrope text-xs uppercase tracking-widest font-bold text-stone-600">
                {{ ui.newMessage }}
              </h2>

              <button
                  type="button"
                  class="text-stone-400 hover:text-primary"
                  :aria-label="ui.closeNewMessage"
                  @click="toggleNewMessagePanel"
              >
                <span class="material-symbols-outlined text-sm" aria-hidden="true">close</span>
              </button>
            </div>

            <div class="mb-4">
              <label for="user-search" class="sr-only">
                {{ ui.searchUser }}
              </label>

              <input
                  id="user-search"
                  v-model="userSearch"
                  type="text"
                  class="w-full border border-stone-200 px-3 py-2 text-sm font-manrope focus:ring-0 focus:border-primary"
                  :placeholder="ui.searchArtistPlaceholder"
                  @input="searchUsers"
              />
            </div>

            <div v-if="loadingUsers" class="text-xs text-stone-500 font-manrope">
              {{ ui.searchingUsers }}
            </div>

            <div v-else-if="usersError" class="text-xs text-red-700 bg-red-50 border border-red-200 p-2">
              {{ usersError }}
            </div>

            <div v-else-if="users.length === 0" class="text-xs text-stone-500 font-manrope">
              {{ ui.noUsers }}
            </div>

            <div v-else class="space-y-2 max-h-72 overflow-y-auto">
              <button
                  v-for="user in users"
                  :key="user.id"
                  type="button"
                  class="w-full flex items-center gap-3 text-left p-3 hover:bg-surface-container-low transition-colors"
                  @click="startConversation(user)"
              >
                <div class="w-9 h-9 bg-stone-200 overflow-hidden shrink-0">
                  <img
                      v-if="getUserAvatar(user)"
                      :src="getUserAvatar(user)"
                      :alt="ui.avatarOf(getUserName(user))"
                      class="w-full h-full object-cover"
                  />

                  <div v-else class="w-full h-full flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">person</span>
                  </div>
                </div>

                <div class="flex-1 overflow-hidden">
                  <p class="font-manrope text-xs font-bold uppercase tracking-wider truncate">
                    {{ getUserName(user) }}
                  </p>

                  <p class="font-manrope text-[10px] text-stone-400 truncate">
                    {{ user.profile?.specialty || user.email }}
                  </p>
                </div>
              </button>
            </div>
          </div>

          <!-- Lista de conversaciones -->
          <div class="flex-1 overflow-y-auto">
            <div v-if="loadingConversations" class="text-sm text-stone-500 font-manrope">
              {{ ui.loadingConversations }}
            </div>

            <div v-else-if="conversationError" class="text-sm text-red-700 bg-red-50 border border-red-200 p-3">
              {{ conversationError }}
            </div>

            <div v-else-if="filteredConversations.length === 0" class="text-sm text-stone-500 font-manrope">
              {{ ui.noConversations }}
            </div>

            <div v-else class="space-y-1">
              <button
                  v-for="conversation in filteredConversations"
                  :key="conversation.id"
                  type="button"
                  class="w-full text-left p-4 transition-all cursor-pointer group flex gap-3 rounded-2xl border-l-4"
                  :class="activeConversation && activeConversation.id === conversation.id
                  ? 'bg-white border-primary'
                  : 'hover:bg-white border-transparent'"
                  @click="selectConversation(conversation)"
              >
                <div class="w-10 h-10 bg-stone-200 shrink-0 overflow-hidden">
                  <img
                      v-if="getParticipantAvatar(conversation)"
                      :src="getParticipantAvatar(conversation)"
                      :alt="ui.avatarOf(getParticipantName(conversation))"
                      class="w-full h-full object-cover"
                  />

                  <div v-else class="w-full h-full flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined" aria-hidden="true">person</span>
                  </div>
                </div>

                <div class="flex-1 overflow-hidden">
                  <div class="flex justify-between items-start mb-1">
                    <span class="font-manrope font-bold text-xs uppercase tracking-wider truncate">
                      {{ getParticipantName(conversation) }}
                    </span>

                    <span class="text-[10px] text-stone-400 shrink-0 ml-2">
                      {{ formatConversationTime(conversation.last_message?.created_at || conversation.updated_at) }}
                    </span>
                  </div>

                  <p class="text-sm text-stone-600 truncate font-manrope">
                    {{ conversation.last_message?.body || ui.noMessagesYet }}
                  </p>

                  <div v-if="conversation.unread_count > 0" class="mt-2 flex gap-1">
                    <span class="w-2 h-2 bg-primary"></span>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Chat Area -->
      <section class="flex-1 flex flex-col bg-surface min-w-0">
        <div v-if="!activeConversation" class="flex-1 flex items-center justify-center text-center px-8">
          <div>
            <h2 class="font-notoSerif text-4xl italic mb-4">
              {{ ui.selectConversation }}
            </h2>

            <p class="font-manrope text-stone-500">
              {{ ui.selectConversationHelp }}
            </p>
          </div>
        </div>

        <template v-else>
          <!-- Chat Header -->
          <div class="px-8 py-5 bg-white/50 backdrop-blur-sm border-b border-outline-variant/15">
            <div class="max-w-3xl mx-auto flex justify-between items-center">
              <router-link
                  :to="getParticipantProfileLink(activeConversation)"
                  class="flex items-center gap-4 group"
                  :aria-label="ui.viewUserProfile"
              >
                <div class="w-11 h-11 rounded-full bg-stone-100 overflow-hidden">
                  <img
                      v-if="getParticipantAvatar(activeConversation)"
                      :src="getParticipantAvatar(activeConversation)"
                      :alt="ui.avatarOf(getParticipantName(activeConversation))"
                      class="w-full h-full object-cover"
                  />

                  <div v-else class="w-full h-full flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined" aria-hidden="true">person</span>
                  </div>
                </div>

                <div>
                  <h2 class="text-base font-bold font-manrope group-hover:text-primary transition-colors">
                    {{ getParticipantName(activeConversation) }}
                  </h2>
                </div>
              </router-link>

              <div class="flex gap-4">
                <button
                    class="p-2 text-stone-400 hover:text-stone-900 transition-colors"
                    type="button"
                    :aria-label="ui.moreOptions"
                >
                  <span class="material-symbols-outlined" aria-hidden="true">more_vert</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Show Messages -->
          <div ref="messagesContainer" class="flex-1 overflow-y-auto px-8 py-8">
            <div class="max-w-3xl mx-auto space-y-8">
              <div v-if="loadingMessages" class="text-sm text-stone-500 font-manrope">
                {{ ui.loadingMessages }}
              </div>

              <div v-else-if="messageError" class="text-sm text-red-700 bg-red-50 border border-red-200 p-3">
                {{ messageError }}
              </div>

              <div v-else-if="messages.length === 0" class="text-sm text-stone-500 font-manrope">
                {{ ui.emptyConversation }}
              </div>

              <div v-else>
                <div class="flex items-center gap-4 mb-8">
                  <div class="h-px flex-1 bg-stone-200"></div>

                  <span class="font-manrope text-[10px] uppercase tracking-widest text-stone-400">
                    {{ ui.conversation }}
                  </span>

                  <div class="h-px flex-1 bg-stone-200"></div>
                </div>

                <div
                    v-for="message in messages"
                    :key="message.id"
                    class="flex mb-8"
                    :class="isMine(message) ? 'justify-end' : 'justify-start'"
                >

                  <!-- Avatar del otro usuario -->

                  <router-link
                      v-if="!isMine(message)"
                      :to="getParticipantProfileLink(activeConversation)"
                      class="w-9 h-9 rounded-full overflow-hidden bg-stone-200 shrink-0 mr-3 mt-1 hover:ring-2 hover:ring-primary transition-all"
                      :aria-label="ui.viewUserProfile"
                  >
                    <img
                        v-if="getMessageSenderAvatar(message)"
                        :src="getMessageSenderAvatar(message)"
                        :alt="ui.profilePhotoOf(getMessageSenderName(message))"
                        class="w-full h-full object-cover"
                    />

                    <div
                        v-else
                        class="w-full h-full flex items-center justify-center text-stone-400"
                    >
                      <span class="material-symbols-outlined text-sm" aria-hidden="true">
                        person
                      </span>
                    </div>
                  </router-link>

                  <!-- Burbuja del mensaje -->

                  <div
                      class="max-w-[70%]"
                      :class="isMine(message) ? 'items-end text-right' : 'items-start text-left'"
                  >
                    <div
                        class="inline-block px-4 py-2 rounded-2xl text-left"
                        :class="isMine(message)
                        ? 'bg-[#007AFF] text-white'
                        : 'bg-surface-container-low text-stone-800'"
                    >
                      <p class="social-message whitespace-pre-line break-words">
                        {{ message.body }}
                      </p>

                      <div v-if="message.image_url" class="w-full max-w-md h-64 bg-stone-200 mt-4 overflow-hidden rounded-xl">
                        <img
                            :src="message.image_url"
                            :alt="ui.attachedImage"
                            class="w-full h-full object-cover grayscale"
                        />
                      </div>
                    </div>

                    <div
                        class="flex items-center gap-2 mt-2"
                        :class="isMine(message) ? 'justify-end' : 'justify-start'"
                    >
                      <span class="font-manrope text-[10px] text-stone-400 block">
                        {{ formatMessageTime(message.created_at) }}
                      </span>

                      <span
                          v-if="isMine(message)"
                          class="material-symbols-outlined text-[12px] text-primary"
                          aria-hidden="true"
                      >
                        done_all
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="px-8 py-5 bg-white/50 backdrop-blur-md">
            <form
                class="max-w-5xl mx-auto flex items-center gap-3 bg-white border border-stone-200 rounded-full px-4 py-2 shadow-sm focus-within:border-primary transition-colors"
                @submit.prevent="sendMessage"
            >
              <!-- Botón añadir archivo -->
              <button
                  class="w-9 h-9 rounded-full flex items-center justify-center text-stone-400 hover:text-primary hover:bg-stone-100 transition-colors"
                  type="button"
                  :aria-label="ui.addFile"
              >
                <span class="material-symbols-outlined text-xl" aria-hidden="true">
                  add_circle
                </span>
              </button>

              <!-- Caja de texto -->
              <textarea
                  id="new-message"
                  name="message"
                  v-model="newMessage"
                  class="social-input flex-1 bg-transparent border-none outline-none focus:outline-none focus:ring-0 focus:border-transparent resize-none placeholder-stone-400 min-h-[38px] max-h-24 overflow-y-auto px-3 py-2"
                  :placeholder="ui.writeMessage"
                  :aria-label="ui.writeMessage"
                  rows="1"
                  @keydown.enter.exact.prevent="sendMessage"
              ></textarea>

              <!-- Botón emoji -->
              <button
                  class="w-9 h-9 rounded-full flex items-center justify-center text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition-colors"
                  type="button"
                  :aria-label="ui.addEmoji"
              >
                <span class="material-symbols-outlined text-xl" aria-hidden="true">
                  mood
                </span>
              </button>
              <!-- Botón enviar -->
              <button
                  class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center hover:scale-95 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
                  type="submit"
                  :disabled="sendingMessage || !newMessage.trim()"
                  :aria-label="ui.sendMessage"
              >
                <span class="material-symbols-outlined text-lg" aria-hidden="true">
                  send
                </span>
              </button>
            </form>
          </div>
        </template>
      </section>

    </main>
  </div>
</template>

<script>
import api from '@/services/api'
import AppSidebar from '@/components/AppSidebar.vue'

const messagesTexts = {
  es: {
    title: 'Mensajes',
    newMessage: 'Crear nuevo mensaje',
    closeNewMessage: 'Cerrar nuevo mensaje',
    searchUser: 'Buscar usuario',
    searchArtistPlaceholder: 'Buscar artista...',
    searchingUsers: 'Buscando usuarios...',
    noUsers: 'No hay usuarios para mostrar.',
    loadingConversations: 'Cargando conversaciones...',
    noConversations: 'Todavía no tienes conversaciones.',
    noMessagesYet: 'Sin mensajes todavía.',
    selectConversation: 'Selecciona una conversación',
    selectConversationHelp: 'Pulsa el botón + para iniciar una conversación con otro artista.',
    viewUserProfile: 'Ver perfil del usuario',
    moreOptions: 'Más opciones',
    loadingMessages: 'Cargando mensajes...',
    emptyConversation: 'Todavía no hay mensajes en esta conversación.',
    conversation: 'Conversación',
    attachedImage: 'Imagen adjunta al mensaje',
    addFile: 'Añadir archivo',
    writeMessage: 'Escribe un mensaje...',
    addEmoji: 'Añadir emoji',
    sendMessage: 'Enviar mensaje',
    user: 'Usuario',
    errors: {
      users: 'No se pudieron cargar los usuarios.',
      startConversation: 'No se pudo iniciar la conversación.',
      conversations: 'No se pudieron cargar las conversaciones.',
      messages: 'No se pudieron cargar los mensajes de esta conversación.',
      send: 'No se pudo enviar el mensaje.',
    },
    avatarOf: (name) => `Avatar de ${name}`,
    profilePhotoOf: (name) => `Foto de perfil de ${name}`,
  },
  en: {
    title: 'Messages',
    newMessage: 'Create new message',
    closeNewMessage: 'Close new message',
    searchUser: 'Search user',
    searchArtistPlaceholder: 'Search artist...',
    searchingUsers: 'Searching users...',
    noUsers: 'There are no users to display.',
    loadingConversations: 'Loading conversations...',
    noConversations: 'You do not have any conversations yet.',
    noMessagesYet: 'No messages yet.',
    selectConversation: 'Select a conversation',
    selectConversationHelp: 'Press the + button to start a conversation with another artist.',
    viewUserProfile: 'View user profile',
    moreOptions: 'More options',
    loadingMessages: 'Loading messages...',
    emptyConversation: 'There are no messages in this conversation yet.',
    conversation: 'Conversation',
    attachedImage: 'Image attached to the message',
    addFile: 'Add file',
    writeMessage: 'Write a message...',
    addEmoji: 'Add emoji',
    sendMessage: 'Send message',
    user: 'AMW User',
    errors: {
      users: 'Users could not be loaded.',
      startConversation: 'The conversation could not be started.',
      conversations: 'Conversations could not be loaded.',
      messages: 'Messages from this conversation could not be loaded.',
      send: 'The message could not be sent.',
    },
    avatarOf: (name) => `Avatar of ${name}`,
    profilePhotoOf: (name) => `Profile photo of ${name}`,
  },
  fr: {
    title: 'Messages',
    newMessage: 'Créer un nouveau message',
    closeNewMessage: 'Fermer le nouveau message',
    searchUser: 'Rechercher un utilisateur',
    searchArtistPlaceholder: 'Rechercher un artiste...',
    searchingUsers: 'Recherche des utilisateurs...',
    noUsers: 'Aucun utilisateur à afficher.',
    loadingConversations: 'Chargement des conversations...',
    noConversations: "Vous n'avez pas encore de conversations.",
    noMessagesYet: 'Aucun message pour le moment.',
    selectConversation: 'Sélectionnez une conversation',
    selectConversationHelp: 'Appuyez sur le bouton + pour démarrer une conversation avec un autre artiste.',
    viewUserProfile: "Voir le profil de l'utilisateur",
    moreOptions: "Plus d'options",
    loadingMessages: 'Chargement des messages...',
    emptyConversation: "Il n'y a pas encore de messages dans cette conversation.",
    conversation: 'Conversation',
    attachedImage: 'Image jointe au message',
    addFile: 'Ajouter un fichier',
    writeMessage: 'Écrivez un message...',
    addEmoji: 'Ajouter un emoji',
    sendMessage: 'Envoyer le message',
    user: 'Utilisateur AMW',
    errors: {
      users: "Les utilisateurs n'ont pas pu être chargés.",
      startConversation: "La conversation n'a pas pu être démarrée.",
      conversations: "Les conversations n'ont pas pu être chargées.",
      messages: "Les messages de cette conversation n'ont pas pu être chargés.",
      send: "Le message n'a pas pu être envoyé.",
    },
    avatarOf: (name) => `Avatar de ${name}`,
    profilePhotoOf: (name) => `Photo de profil de ${name}`,
  },
}

export default {
  name: 'MessagesPage',

  components: {
    AppSidebar,
  },

  data() {
    return {
      conversations: [],
      activeConversation: null,
      messages: [],

      search: '',
      newMessage: '',

      showNewMessagePanel: false,
      userSearch: '',
      users: [],
      loadingUsers: false,
      usersError: '',
      userSearchTimeout: null,

      loadingConversations: false,
      loadingMessages: false,
      sendingMessage: false,
      conversationError: '',
      messageError: '',

      currentUser: null,
      currentUserAvatar: 'https://placehold.co/100x100?text=AMW',
    }
  },

  computed: {
    ui() {
      return messagesTexts[this.$i18n.locale] || messagesTexts.es
    },

    filteredConversations() {
      return this.conversations.filter((conversation) => {
        return this.getParticipantName(conversation)
            .toLowerCase()
            .includes(this.search.toLowerCase())
      })
    },
  },

  async mounted() {
    this.loadCurrentUser()
    await this.loadConversations()
  },

  beforeUnmount() {
    clearTimeout(this.userSearchTimeout)
  },

  methods: {
    loadCurrentUser() {
      const storedUser = localStorage.getItem('amw_user')

      if (!storedUser) {
        return
      }

      try {
        this.currentUser = JSON.parse(storedUser)

        this.currentUserAvatar =
            this.currentUser?.profile?.profile_image_url ||
            this.currentUser?.avatar ||
            this.currentUserAvatar
      } catch (error) {
        localStorage.removeItem('amw_user')
      }
    },

    toggleNewMessagePanel() {
      this.showNewMessagePanel = !this.showNewMessagePanel
      this.usersError = ''

      if (this.showNewMessagePanel && this.users.length === 0) {
        this.loadUsers()
      }
    },

    searchUsers() {
      clearTimeout(this.userSearchTimeout)

      this.userSearchTimeout = setTimeout(() => {
        this.loadUsers()
      }, 300)
    },

    async loadUsers() {
      this.loadingUsers = true
      this.usersError = ''

      try {
        const response = await api.get('/users', {
          params: {
            search: this.userSearch,
          },
        })

        const payload = response.data
        this.users = payload.data || payload
      } catch (error) {
        this.usersError = this.ui.errors.users
      } finally {
        this.loadingUsers = false
      }
    },

    async startConversation(user) {
      this.usersError = ''

      try {
        const response = await api.post('/conversations', {
          participant_id: user.id,
        })

        const conversation = response.data.data || response.data

        this.showNewMessagePanel = false
        this.userSearch = ''

        await this.loadConversations(false)

        const selectedConversation =
            this.conversations.find((item) => item.id === conversation.id) ||
            conversation

        await this.selectConversation(selectedConversation)
      } catch (error) {
        if (error.response?.data?.message) {
          this.usersError = error.response.data.message
        } else {
          this.usersError = this.ui.errors.startConversation
        }
      }
    },

    async loadConversations(selectFirst = true) {
      this.loadingConversations = true
      this.conversationError = ''

      try {
        const response = await api.get('/conversations')
        const payload = response.data

        this.conversations = payload.data || payload

        const requestedConversationId = Number(this.$route.query.conversation)

        if (selectFirst && this.conversations.length > 0 && !this.activeConversation) {
          const requestedConversation = requestedConversationId
            ? this.conversations.find((conversation) => conversation.id === requestedConversationId)
            : null

          await this.selectConversation(requestedConversation || this.conversations[0])
        }

        if (this.activeConversation) {
          const updatedActiveConversation = this.conversations.find(
              (conversation) => conversation.id === this.activeConversation.id
          )

          if (updatedActiveConversation) {
            this.activeConversation = updatedActiveConversation
          }
        }
      } catch (error) {
        this.conversationError = this.ui.errors.conversations
      } finally {
        this.loadingConversations = false
      }
    },

    async selectConversation(conversation) {
      this.activeConversation = conversation
      await this.loadMessages(conversation.id)
    },

    async loadMessages(conversationId) {
      this.loadingMessages = true
      this.messageError = ''

      try {
        const response = await api.get(`/conversations/${conversationId}/messages`)
        const payload = response.data

        this.messages = payload.data || payload

        await this.$nextTick()
        this.scrollToBottom()
      } catch (error) {
        this.messageError = this.ui.errors.messages
      } finally {
        this.loadingMessages = false
      }
    },

    async sendMessage() {
      if (!this.newMessage.trim() || !this.activeConversation) {
        return
      }

      this.sendingMessage = true
      this.messageError = ''

      try {
        const response = await api.post(
            `/conversations/${this.activeConversation.id}/messages`,
            {
              body: this.newMessage,
            }
        )

        const createdMessage = response.data.data || response.data

        this.messages.push(createdMessage)
        this.newMessage = ''

        await this.$nextTick()
        this.scrollToBottom()

        await this.loadConversations(false)
      } catch (error) {
        this.messageError = this.ui.errors.send
      } finally {
        this.sendingMessage = false
      }
    },

    scrollToBottom() {
      if (!this.$refs.messagesContainer) {
        return
      }

      this.$refs.messagesContainer.scrollTop =
          this.$refs.messagesContainer.scrollHeight
    },

    getParticipant(conversation) {
      if (!conversation) {
        return null
      }

      if (conversation.participant) {
        return conversation.participant
      }

      if (Array.isArray(conversation.users)) {
        return conversation.users.find((user) => user.id !== this.currentUser?.id)
      }

      return null
    },

    getParticipantName(conversation) {
      const participant = this.getParticipant(conversation)

      return (
          participant?.profile?.artistic_name ||
          participant?.name ||
          this.ui.user
      )
    },

    getParticipantAvatar(conversation) {
      const participant = this.getParticipant(conversation)

      return (
          participant?.profile?.profile_image_url ||
          participant?.avatar ||
          null
      )
    },

    getParticipantProfileLink(conversation) {
      const participant = this.getParticipant(conversation)

      if (!participant?.id) {
        return '/profile'
      }

      return `/profiles/${participant.id}`
    },

    getMessageSenderAvatar(message) {
      return (
          message.sender?.profile?.profile_image_url ||
          message.sender?.avatar ||
          this.getParticipantAvatar(this.activeConversation) ||
          null
      )
    },

    getMessageSenderName(message) {
      return (
          message.sender?.profile?.artistic_name ||
          message.sender?.name ||
          this.getParticipantName(this.activeConversation) ||
          this.ui.user
      )
    },

    getUserName(user) {
      return user.profile?.artistic_name || user.name || this.ui.user
    },

    getUserAvatar(user) {
      return user.profile?.profile_image_url || user.avatar || null
    },

    isMine(message) {
      if (typeof message.is_mine === 'boolean') {
        return message.is_mine
      }

      return message.sender_id === this.currentUser?.id
    },

    formatConversationTime(date) {
      if (!date) {
        return ''
      }

      const locales = {
        es: 'es-ES',
        en: 'en-US',
        fr: 'fr-FR',
      }

      return new Date(date).toLocaleDateString(locales[this.$i18n.locale] || 'es-ES', {
        day: '2-digit',
        month: 'short',
      })
    },

    formatMessageTime(date) {
      if (!date) {
        return ''
      }

      const locales = {
        es: 'es-ES',
        en: 'en-US',
        fr: 'fr-FR',
      }

      return new Date(date).toLocaleTimeString(locales[this.$i18n.locale] || 'es-ES', {
        hour: '2-digit',
        minute: '2-digit',
      })
    },
  },
}
</script>

<style scoped>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
}

.font-notoSerif {
  font-family: 'Noto Serif', serif;
}

.font-manrope {
  font-family: 'Manrope', sans-serif;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  white-space: nowrap;
  border: 0;
  clip: rect(0, 0, 0, 0);
}
</style>