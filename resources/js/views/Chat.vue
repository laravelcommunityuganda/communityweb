<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="flex h-[calc(100vh-12rem)]">
          <!-- Conversations Sidebar -->
          <div class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Messages</h2>
                <button class="p-2 text-gray-500 hover:text-laravel-600 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </button>
              </div>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search conversations..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
                />
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
              <div v-if="loadingConversations" class="p-4 text-center">
                <div class="animate-spin w-6 h-6 border-2 border-laravel-500 border-t-transparent rounded-full mx-auto"></div>
              </div>
              <div v-else-if="conversations.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                No conversations yet
              </div>
              <div v-else>
                <div
                  v-for="conversation in filteredConversations"
                  :key="conversation.id"
                  @click="selectConversation(conversation)"
                  class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                  :class="{ 'bg-laravel-50 dark:bg-laravel-900/20': selectedConversation?.id === conversation.id }"
                >
                  <div class="flex items-center gap-3">
                    <div class="relative">
                      <img
                        :src="getConversationAvatar(conversation)"
                        :alt="getConversationName(conversation)"
                        class="w-12 h-12 rounded-full"
                      />
                      <span
                        v-if="conversation.unread_count > 0"
                        class="absolute -top-1 -right-1 w-5 h-5 bg-laravel-500 text-white text-xs rounded-full flex items-center justify-center"
                      >
                        {{ conversation.unread_count }}
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900 dark:text-white truncate">
                          {{ getConversationName(conversation) }}
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                          {{ formatTime(conversation.updated_at) }}
                        </span>
                      </div>
                      <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ conversation.last_message?.content || 'No messages yet' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chat Area -->
          <div class="flex-1 flex flex-col">
            <!-- Chat Header -->
            <div v-if="selectedConversation" class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img
                  :src="getConversationAvatar(selectedConversation)"
                  :alt="getConversationName(selectedConversation)"
                  class="w-10 h-10 rounded-full"
                />
                <div>
                  <h3 class="font-medium text-gray-900 dark:text-white">
                    {{ getConversationName(selectedConversation) }}
                  </h3>
                  <p class="text-sm text-green-500" v-if="isOnline">
                    Online
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button class="p-2 text-gray-500 hover:text-laravel-600 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </button>
                <button class="p-2 text-gray-500 hover:text-laravel-600 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                </button>
                <button class="p-2 text-gray-500 hover:text-laravel-600 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Messages Area -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
              <div v-if="!selectedConversation" class="h-full flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  <p class="text-gray-500 dark:text-gray-400">Select a conversation to start messaging</p>
                </div>
              </div>
              <template v-else>
                <div
                  v-for="message in messages"
                  :key="message.id"
                  class="flex"
                  :class="message.user_id === currentUserId ? 'justify-end' : 'justify-start'"
                >
                  <div
                    class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl"
                    :class="message.user_id === currentUserId
                      ? 'bg-laravel-500 text-white rounded-br-none'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-none'"
                  >
                    <p>{{ message.content }}</p>
                    <p
                      class="text-xs mt-1"
                      :class="message.user_id === currentUserId ? 'text-laravel-100' : 'text-gray-500 dark:text-gray-400'"
                    >
                      {{ formatTime(message.created_at) }}
                    </p>
                  </div>
                </div>
              </template>
            </div>

            <!-- Message Input -->
            <div v-if="selectedConversation" class="p-4 border-t border-gray-200 dark:border-gray-700">
              <div class="flex items-center gap-3">
                <button class="p-2 text-gray-500 hover:text-laravel-600 transition">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                  </svg>
                </button>
                <input
                  v-model="newMessage"
                  @keyup.enter="sendMessage"
                  type="text"
                  placeholder="Type a message..."
                  class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
                />
                <button
                  @click="sendMessage"
                  :disabled="!newMessage.trim()"
                  class="p-3 bg-laravel-500 text-white rounded-full hover:bg-laravel-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id)

const loadingConversations = ref(true)
const conversations = ref([])
const selectedConversation = ref(null)
const messages = ref([])
const newMessage = ref('')
const searchQuery = ref('')
const messagesContainer = ref(null)

const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  const query = searchQuery.value.toLowerCase()
  return conversations.value.filter(c => {
    const name = getConversationName(c).toLowerCase()
    return name.includes(query)
  })
})

const fetchConversations = async () => {
  try {
    const response = await axios.get('/api/v1/conversations')
    conversations.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch conversations:', error)
  } finally {
    loadingConversations.value = false
  }
}

const selectConversation = async (conversation) => {
  selectedConversation.value = conversation
  await fetchMessages(conversation.id)
}

const fetchMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/api/v1/conversations/${conversationId}/messages`)
    messages.value = response.data.data
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to fetch messages:', error)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  try {
    const response = await axios.post('/api/v1/messages', {
      conversation_id: selectedConversation.value.id,
      content: newMessage.value,
    })
    messages.value.push(response.data.data)
    newMessage.value = ''
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const getConversationName = (conversation) => {
  if (conversation.type === 'group') {
    return conversation.name
  }
  // For private conversations, get the other user's name
  const otherUser = conversation.participants?.find(p => p.id !== currentUserId.value)
  return otherUser?.name || 'Unknown User'
}

const getConversationAvatar = (conversation) => {
  if (conversation.type === 'group') {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(conversation.name)}&background=FF2D20&color=fff`
  }
  const otherUser = conversation.participants?.find(p => p.id !== currentUserId.value)
  return otherUser?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(getConversationName(conversation))}&background=FF2D20&color=fff`
}

const formatTime = (date) => {
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const hours = Math.floor(diff / (1000 * 60 * 60))
  
  if (hours < 24) {
    return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
  } else if (hours < 48) {
    return 'Yesterday'
  } else {
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  }
}

const isOnline = computed(() => {
  // This would be determined by presence system
  return false
})

onMounted(() => {
  fetchConversations()
})
</script>