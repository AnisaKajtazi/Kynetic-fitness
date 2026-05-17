<template>
  <div class="chat-layout">
    <Sidebar />

    <main class="chat-container">
      <div class="chat-inner">
        <aside class="chat-sidebar">
          <div class="sidebar-head">
            <h3>Conversations</h3>
          </div>
          <ul>
            <li v-for="c in conversations" :key="c.user.UserID" :class="{ active: selected && selected.UserID === c.user.UserID }" @click="select(c)">
              <img :src="photoUrl(c.user)" />
              <div class="meta">
                <div class="top">
                  <strong>{{ c.user.name }} {{ c.user.surname }}</strong>
                  <span class="time">{{ formatTime(c.last_message?.created_at) }}</span>
                </div>
                <div class="bottom">
                  <span class="preview">{{ c.last_message?.body || 'No messages yet' }}</span>
                  <span v-if="c.unread_count" class="badge">{{ c.unread_count }}</span>
                </div>
              </div>
            </li>
          </ul>
        </aside>

        <section class="chat-window" v-if="selected">
          <header class="chat-header">
            <img :src="photoUrl(selected)" />
            <div class="info">
              <h4>{{ selected.name }} {{ selected.surname }}</h4>
              <p class="sub">{{ selected.email }}</p>
            </div>
          </header>

          <div class="messages" ref="messagesEl">
            <div v-for="m in messages" :key="m.id" :class="['message', { me: m.sender_id === me.UserID }]">
              <div class="bubble">{{ m.body }}</div>
              <div class="ts">{{ formatTime(m.created_at) }}</div>
            </div>
          </div>

          <footer class="composer">
            <input v-model="newMessage" @keydown.enter.prevent="sendMessage" placeholder="Write a message..." />
            <button class="send-btn" @click="sendMessage">Send</button>
          </footer>
        </section>

        <section class="chat-empty" v-else>
          <div class="empty-card">
            <h3>Select a conversation</h3>
            <p>Pick a trainer or client from the list to start chatting.</p>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import api from '@/services/axios'
import { useRoute, useRouter } from 'vue-router'

const conversations = ref([])
const selected = ref(null)
const messages = ref([])
const newMessage = ref('')
const me = ref(null)
const route = useRoute()
const router = useRouter()
let pollInterval = null
const messagesEl = ref(null)

const loadConversations = async () => {
  try {
    const res = await api.get('chat/conversations')
    conversations.value = res.data || []
    // auto-select if peer query present
    const peer = route.query.peer
    if (peer && !selected.value) {
      const found = conversations.value.find(c => String(c.user.UserID) === String(peer))
      if (found) select(found)
      else {
        // load partner profile and select
        try {
          const u = await api.get(`users/${peer}`)
          select({ user: u.data, last_message: null, unread_count: 0 })
        } catch (e) { console.error(e) }
      }
    }
  } catch (e) {
    console.error('Error loading conversations', e)
  }
}

const select = async (conv) => {
  selected.value = conv.user
  await loadMessages(conv.user.UserID)
  // mark read
  await api.post(`chat/conversations/${conv.user.UserID}/read`)
  await loadConversations()
}

const loadMessages = async (userId) => {
  try {
    const res = await api.get(`chat/conversations/${userId}/messages`)
    messages.value = res.data || []
    await nextTick()
    scrollToBottom()
  } catch (e) {
    console.error('Error loading messages', e)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selected.value) return
  try {
    await api.post(`chat/conversations/${selected.value.UserID}/messages`, { body: newMessage.value })
    newMessage.value = ''
    await loadMessages(selected.value.UserID)
    await loadConversations()
  } catch (e) {
    console.error('Send error', e)
  }
}

const formatTime = (t) => {
  if (!t) return ''
  return new Date(t).toLocaleTimeString()
}

const photoUrl = (user) => {
  if (!user || !user.photo) return 'https://via.placeholder.com/100x100?text=Profile'
  if (user.photo.startsWith('http')) return user.photo
  return `http://127.0.0.1:8000/uploads/profilephotos/${user.photo}`
}

const scrollToBottom = () => {
  if (!messagesEl.value) return
  messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

onMounted(async () => {
  const stored = localStorage.getItem('user')
  if (!stored) return router.push('/login')
  me.value = JSON.parse(stored)

  await loadConversations()
  pollInterval = setInterval(loadConversations, 5000)
})

watch(() => route.query.peer, (p) => {
  if (p && conversations.value.length) {
    const found = conversations.value.find(c => String(c.user.UserID) === String(p))
    if (found) select(found)
  }
})
</script>

<style scoped>
.chat-layout { display: flex; width: 100%; min-height: 100vh; background: var(--bg-dark); }
.chat-container { margin-left: 240px; width: calc(100% - 240px); padding: 2rem; }
.chat-inner { display: flex; gap: 1rem; height: calc(100vh - 6rem); }
.chat-sidebar { width: 320px; background: var(--bg-card); border-radius: 12px; padding: 1rem; overflow: auto; }
.chat-sidebar ul { list-style: none; padding: 0; margin: 0; }
.chat-sidebar li { display: flex; gap: .75rem; padding: .5rem; border-radius: 8px; cursor: pointer; align-items: center; }
.chat-sidebar li.active { background: rgba(255,255,255,0.02); }
.chat-sidebar img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; }
.chat-sidebar .meta { flex: 1; }
.chat-sidebar .top { display:flex; justify-content: space-between; align-items: center; }
.chat-sidebar .bottom { display:flex; justify-content: space-between; align-items: center; gap: .5rem; }
.badge { background: #ef4444; color: #fff; padding: .15rem .5rem; border-radius: 999px; font-size: .8rem; }

.chat-window { flex: 1; display: flex; flex-direction: column; background: var(--bg-card); border-radius: 12px; overflow: hidden; }
.chat-header { display:flex; gap: .75rem; padding: 1rem; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.03); }
.chat-header img { width:48px; height:48px; border-radius:8px; }
.messages { flex:1; padding: 1rem; overflow:auto; display:flex; flex-direction: column; gap: .5rem; }
.message { display:flex; flex-direction: column; max-width: 70%; }
.message.me { align-self: flex-end; }
.bubble { padding: .6rem .9rem; background: rgba(255,255,255,0.04); border-radius: 12px; }
.message.me .bubble { background: linear-gradient(90deg,#2563eb,#06b6d4); color: white; }
.composer { display:flex; gap:.5rem; padding: .75rem; border-top: 1px solid rgba(255,255,255,0.03); }
.composer input { flex:1; padding:.6rem .8rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.02); color: #fff; }
.send-btn { background:#2563eb; color:#fff; padding:.5rem .8rem; border-radius:8px; border:none }

.chat-empty { flex:1; display:flex; align-items:center; justify-content:center; }
.empty-card { background: var(--bg-card); padding:2rem; border-radius:12px; }
</style>
