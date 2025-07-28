<template>
  <div class="admin-dashboard">
    <AdminHeader />
    
    <div class="dashboard-container">
      <!-- Left Sidebar -->
      <aside class="dashboard-sidebar">
        <div class="sidebar-header">
          <h2 class="dashboard-title">Dashboard</h2>
          <button class="refresh-btn" @click="manualRefresh" title="Refresh data">
            🔄
          </button>
        </div>
        
        <div class="inbox-section">
                      <h3 class="inbox-title">Pesan masuk</h3>
            <div class="inbox-filters">
              <button 
                class="filter-item" 
                :class="{ active: currentFilter === 'all' }"
                @click="setFilter('all')"
              >
                <span class="filter-text">All</span>
                <span class="filter-count">{{ totalConversations }}</span>
              </button>
              <button 
                class="filter-item" 
                :class="{ active: currentFilter === 'unread' }"
                @click="setFilter('unread')"
              >
                <span class="filter-text">Belum Dibaca</span>
                <span class="filter-count">{{ unreadConversations }}</span>
              </button>
            </div>
        </div>
        
        <div class="conversation-list" :class="{ loading: isLoading }">
          <h4 class="conversation-header">User Inbox</h4>
          
          <div v-if="isLoading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Memuat daftar user...</p>
          </div>
          
          <div v-else-if="conversations.length === 0" class="empty-conversations">
            <p>Belum ada user yang chat</p>
          </div>
          
          <div v-else class="conversation-item" 
               v-for="user in filteredConversations" 
               :key="user.id"
               :class="{ active: selectedUser?.id === user.id }"
               @click="selectUser(user)">
            <div class="user-avatar">
              <img v-if="user.profilePhoto && user.profilePhoto !== ''" :src="user.profilePhoto" :alt="user.name" />
              <div v-else class="avatar-placeholder" :style="{ backgroundColor: user.avatarColor }">
                {{ user.initials }}
              </div>
            </div>
            <div class="user-info">
              <div class="user-name">{{ user.name }}</div>
              <div class="user-message">{{ user.lastMessage || 'Belum ada pesan' }}</div>
            </div>
            <div v-if="user.hasUnreadMessages" class="unread-badge">
              {{ user.displayUnreadCount }}
            </div>
          </div>
        </div>
      </aside>
      
      <!-- Main Chat Area -->
      <main class="chat-main">
        <div v-if="selectedUser" class="chat-container">
          <!-- Chat Header -->
          <div class="chat-header">
            <div class="chat-user-info">
              <div class="user-avatar">
                <img v-if="selectedUser.profilePhoto && selectedUser.profilePhoto !== ''" :src="selectedUser.profilePhoto" :alt="selectedUser.name" />
                <div v-else class="avatar-placeholder" :style="{ backgroundColor: selectedUser.avatarColor }">
                  {{ selectedUser.initials }}
                </div>
              </div>
              <span class="user-name">{{ selectedUser.name }}</span>
            </div>
            <button class="close-chat" @click="closeChat">&times;</button>
          </div>
          
          <!-- Messages Area -->
          <div class="messages-container" ref="messagesContainer">
            <div v-for="message in selectedUser.messages" 
                 :key="message.id" 
                 class="message" 
                 :class="message.type">
              <div class="message-bubble">
                <p>{{ message.text }}</p>
                <span class="message-time">{{ message.time }}</span>
              </div>
            </div>
          </div>
          
          <!-- Message Input -->
          <div class="message-input-area">
            <div class="input-container">
              <div class="input-field">
                <textarea 
                  v-model="newMessage" 
                  @keydown.enter.prevent="handleEnterKey"
                  @input="autoResize"
                  placeholder="Ketik pesan..."
                  class="message-input"
                  rows="1"
                  ref="messageInput"
                ></textarea>
              </div>
              
              <div class="action-buttons">
                <button class="send-btn" @click="sendMessage" title="Send">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon">💬</div>
          <h3>Pilih percakapan</h3>
          <p>Pilih pengguna dari daftar untuk memulai percakapan</p>
        </div>
        
        <!-- Error Message -->
        <div v-if="errorMessage" class="error-message" @click="clearError">
          {{ errorMessage }}
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
import axios from 'axios';
import TimezoneHelper from '../utils/timezoneHelper.js';

export default {
  name: 'AdminChatDashboard',
  components: { AdminHeader },
  data() {
    return {
      selectedUser: null,
      newMessage: '',
      conversations: [], // Akan diisi dari backend
      adminId: null, // Akan diisi dari session/auth
      chatPollingInterval: null, // Untuk polling pesan baru
      isLoading: false,
      errorMessage: '',
      openedConversations: new Set(), // Track conversations that have been opened
      currentFilter: 'all' // Filter untuk menampilkan conversation
    }
  },
  async mounted() {
    // Ambil adminId dari session/auth
    const user = JSON.parse(localStorage.getItem('user'));
    this.adminId = user ? user.id : null;
    
    if (!this.adminId) {
      this.errorMessage = 'Admin tidak ditemukan. Silakan login ulang.';
      return;
    }
    
    await this.loadConversations();
    this.startChatPolling();
    this.$nextTick(() => {
      this.autoResize();
    });
  },
  
  beforeDestroy() {
    this.stopChatPolling();
  },
  computed: {
    // Computed property untuk menghitung unread count yang sebenarnya
    conversationsWithUnreadStatus() {
      return this.conversations.map(user => {
        const isOpened = this.openedConversations.has(user.conversationId);
        const actualUnreadCount = isOpened ? 0 : user.unreadCount;
        
        return {
          ...user,
          displayUnreadCount: actualUnreadCount,
          hasUnreadMessages: actualUnreadCount > 0
        };
      });
    },
    
    // Computed property untuk filtered conversations berdasarkan filter
    filteredConversations() {
      if (this.currentFilter === 'unread') {
        return this.conversationsWithUnreadStatus.filter(user => user.hasUnreadMessages);
      }
      return this.conversationsWithUnreadStatus;
    },
    
    // Computed property untuk total conversations
    totalConversations() {
      return this.conversations.length;
    },
    
    // Computed property untuk unread conversations
    unreadConversations() {
      return this.conversationsWithUnreadStatus.filter(user => user.hasUnreadMessages).length;
    }
  },
  methods: {
    async loadConversations() {
      try {
        this.isLoading = true;
        console.log('Loading conversations for admin ID:', this.adminId);
        
        // Force refresh dengan timestamp untuk menghindari cache
        const timestamp = Date.now();
        const res = await axios.get('https://mindcareindependent.com/api/get_chat_users.php', {
          params: { 
            admin_id: this.adminId,
            _t: timestamp 
          }
        });
        
        console.log('Load conversations API response:', res.data);
        
        if (res.data.success) {
          // Clear existing conversations dan replace dengan data baru
          this.conversations = [];
          
          // Process users dengan delay untuk memastikan UI ter-update
          await this.$nextTick();
          
          this.conversations = res.data.users.map(user => {
            console.log('Processing user:', user);
            return {
              id: user.id,
              name: user.username || user.fullName || 'User',
              initials: (user.username || user.fullName || 'U').slice(0,2).toUpperCase(),
              avatarColor: this.getFixedAvatarColor(user.id),
              profilePhoto: user.profile_photo && user.profile_photo !== '' ? user.profile_photo : null,
              lastMessage: user.last_message || '',
              unreadCount: user.unread_count || 0,
              messages: [],
              conversationId: user.conversation_id
            };
          });
          
          console.log('Final conversations:', this.conversations);
          
          // Force UI update
          this.$forceUpdate();
        } else {
          console.error('API response not successful:', res.data);
          this.errorMessage = 'Gagal memuat daftar user.';
        }
      } catch (error) {
        console.error('Error loading conversations:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.errorMessage = 'Gagal memuat daftar user. Coba lagi.';
      } finally {
        this.isLoading = false;
      }
    },
    async selectUser(user) {
      try {
        console.log('Selecting user:', user);
        
        // Clear selected user terlebih dahulu
        this.selectedUser = null;
        await this.$nextTick();
        
        // Set selected user
        this.selectedUser = user;
        
        // Mark conversation as opened
        if (user.conversationId) {
          this.openedConversations.add(user.conversationId);
          // Mark messages as read
          await this.markMessagesAsRead(user.conversationId);
        }
        
        // Jika belum ada conversation_id, buat atau dapatkan conversation
        if (!user.conversationId) {
          console.log('No conversation ID, creating conversation...');
          await this.getOrCreateConversation(user);
        }
        
        console.log('Using conversation ID:', user.conversationId);
        
        // Force refresh messages dengan timestamp
        const timestamp = Date.now();
        const res = await axios.get('https://mindcareindependent.com/api/get_messages.php', {
          params: { 
            conversation_id: user.conversationId,
            _t: timestamp 
          }
        });
        
        console.log('Select user API response:', res.data);
        
        if (res.data.success) {
          // Clear existing messages
          this.selectedUser.messages = [];
          await this.$nextTick();
          
          this.selectedUser.messages = res.data.messages.map(msg => {
            console.log('Processing message in selectUser:', msg);
            return {
              id: msg.id,
              text: msg.message,
              type: msg.sender_role === 'user' ? 'user' : 'agent',
              time: TimezoneHelper.formatTimeForDisplay(msg.created_at),
              isFile: !!msg.file_url,
              fileUrl: msg.file_url,
              fileName: msg.file_name
            };
          });
          
          console.log('Final selected user messages:', this.selectedUser.messages);
          
          // Update last message dan unread count
          this.updateUserLastMessage(user, res.data.messages);
          
          // Force UI update
          this.$forceUpdate();
          
          this.$nextTick(() => {
            this.scrollToBottom();
          });
        } else {
          console.error('API response not successful:', res.data);
          this.errorMessage = 'Gagal memuat pesan.';
        }
      } catch (error) {
        console.error('Error selecting user:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.errorMessage = 'Gagal memuat chat. Coba lagi.';
      }
    },
    closeChat() {
      this.selectedUser = null;
    },
    async sendMessage() {
      if (!this.newMessage.trim() || !this.selectedUser) return;
      
      const messageText = this.newMessage.trim();
      this.newMessage = '';
      
      try {
        console.log('Sending message:', messageText, 'to user:', this.selectedUser.name);
        
        const formData = new FormData();
        formData.append('sender_id', this.adminId);
        formData.append('receiver_id', this.selectedUser.id);
        formData.append('sender_role', 'admin');
        formData.append('message', messageText);
        
        // Tambahkan waktu client dengan zona waktu yang akurat
        const timeData = TimezoneHelper.addTimezoneToRequest();
        formData.append('current_time', timeData.current_time);
        formData.append('timezone', timeData.timezone);
        
        const response = await axios.post('https://mindcareindependent.com/api/send_message_new.php', formData);
        
        console.log('Send message response:', response.data);
        
        if (response.data.success) {
          // Force reload messages untuk mendapatkan data terbaru
          await this.loadUserMessages(this.selectedUser);
          
          // Force reload conversations untuk update last message
          await this.loadConversations();
          
          this.$nextTick(() => {
            this.scrollToBottom();
            this.resetTextareaHeight();
          });
        } else {
          console.error('Failed to send message:', response.data);
          this.errorMessage = 'Gagal mengirim pesan.';
        }
      } catch (error) {
        console.error('Error sending message:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.errorMessage = 'Gagal mengirim pesan. Coba lagi.';
        // Restore message jika gagal
        this.newMessage = messageText;
      }
    },
    autoResize() {
      const textarea = this.$refs.messageInput;
      if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
      }
    },
    handleEnterKey(event) {
      if (event.shiftKey) {
        // Shift + Enter untuk line break
        return;
      } else {
        // Enter saja untuk kirim pesan
        this.sendMessage();
      }
    },
    resetTextareaHeight() {
      const textarea = this.$refs.messageInput;
      if (textarea) {
        textarea.style.height = 'auto';
      }
    },
    getCurrentTime() {
      const now = new Date();
      return now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
      });
    },
    scrollToBottom() {
      const container = this.$refs.messagesContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    },
    
    // Method untuk mendapatkan atau membuat conversation
    async getOrCreateConversation(user) {
      try {
        console.log('Creating conversation for user:', user.id, 'admin:', this.adminId);
        
        const formData = new FormData();
        formData.append('user_id', user.id);
        formData.append('admin_id', this.adminId);
        
        const response = await axios.post('https://mindcareindependent.com/api/get_or_create_conversation.php', formData);
        
        console.log('Conversation response:', response.data);
        
        if (response.data.success) {
          user.conversationId = response.data.conversation_id;
          console.log('Conversation ID set to:', user.conversationId);
        } else {
          console.error('Failed to create conversation:', response.data);
        }
      } catch (error) {
        console.error('Error getting or creating conversation:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.errorMessage = 'Gagal membuat conversation.';
      }
    },
    
    // Method untuk load messages user tertentu
    async loadUserMessages(user) {
      try {
        console.log('Loading messages for conversation ID:', user.conversationId);
        
        // Force refresh dengan timestamp
        const timestamp = Date.now();
        const res = await axios.get('https://mindcareindependent.com/api/get_messages.php', {
          params: { 
            conversation_id: user.conversationId,
            _t: timestamp 
          }
        });
        
        console.log('Messages API response:', res.data);
        
        if (res.data.success) {
          // Clear existing messages dan replace dengan data baru
          user.messages = [];
          
          // Process messages dengan delay
          await this.$nextTick();
          
          user.messages = res.data.messages.map(msg => {
            console.log('Processing message:', msg);
            return {
              id: msg.id,
              text: msg.message,
              type: msg.sender_role === 'user' ? 'user' : 'agent',
              time: TimezoneHelper.formatTimeForDisplay(msg.created_at),
              isFile: !!msg.file_url,
              fileUrl: msg.file_url,
              fileName: msg.file_name
            };
          });
          
          console.log('Processed messages:', user.messages);
          this.updateUserLastMessage(user, res.data.messages);
          
          // Force UI update untuk selected user
          if (this.selectedUser && this.selectedUser.id === user.id) {
            this.$forceUpdate();
          }
        } else {
          console.error('API response not successful:', res.data);
        }
      } catch (error) {
        console.error('Error loading user messages:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
      }
    },
    
    // Method untuk update last message dan unread count
    updateUserLastMessage(user, messages) {
      console.log('Updating last message for user:', user.name, 'messages count:', messages.length);
      
      if (messages.length > 0) {
        const lastMessage = messages[messages.length - 1];
        console.log('Last message:', lastMessage);
        
        user.lastMessage = lastMessage.message;
        
        // Hitung unread count (pesan dari user yang belum dibaca)
        user.unreadCount = messages.filter(msg => 
          msg.sender_role === 'user' && !msg.is_read
        ).length;
        
        // Reset unread count jika conversation sudah dibuka
        if (this.openedConversations.has(user.conversationId)) {
          user.unreadCount = 0;
        }
        
        console.log('Updated user data:', {
          name: user.name,
          lastMessage: user.lastMessage,
          unreadCount: user.unreadCount,
          isOpened: this.openedConversations.has(user.conversationId)
        });
      } else {
        console.log('No messages found for user:', user.name);
      }
    },
    
    // Method untuk polling pesan baru
    startChatPolling() {
      this.chatPollingInterval = setInterval(async () => {
        try {
          if (this.selectedUser) {
            console.log('Polling messages for selected user:', this.selectedUser.name);
            await this.loadUserMessages(this.selectedUser);
          }
          // Reload conversations untuk update unread count
          console.log('Polling conversations...');
          await this.loadConversations();
        } catch (error) {
          console.error('Error in chat polling:', error);
        }
      }, 3000); // Poll setiap 3 detik untuk update lebih cepat
    },
    
    stopChatPolling() {
      if (this.chatPollingInterval) {
        clearInterval(this.chatPollingInterval);
        this.chatPollingInterval = null;
      }
    },
    
    // Method untuk generate fixed avatar color berdasarkan user ID
    getFixedAvatarColor(userId) {
      const colors = [
        '#51cf66', // Green
        '#339af0', // Blue
        '#ffd43b', // Yellow
        '#ff6b6b', // Red
        '#ae3ec9', // Purple
        '#20c997', // Teal
        '#fd7e14', // Orange
        '#6f42c1', // Indigo
        '#e83e8c', // Pink
        '#28a745', // Success Green
        '#17a2b8', // Info Blue
        '#ffc107', // Warning Yellow
        '#dc3545', // Danger Red
        '#6c757d', // Secondary Gray
        '#343a40'  // Dark Gray
      ];
      
      // Menggunakan user ID untuk mendapatkan warna yang konsisten
      const colorIndex = userId % colors.length;
      return colors[colorIndex];
    },
    
    // Method untuk clear error message
    clearError() {
      this.errorMessage = '';
    },
    
    // Method untuk set filter
    setFilter(filter) {
      this.currentFilter = filter;
      console.log('Filter changed to:', filter);
      
      // Reset selected user jika user yang dipilih tidak ada di filtered list
      if (this.selectedUser) {
        const isSelectedUserInFiltered = this.filteredConversations.some(user => user.id === this.selectedUser.id);
        if (!isSelectedUserInFiltered) {
          this.selectedUser = null;
        }
      }
    },
    
    // Method untuk mark messages sebagai read
    async markMessagesAsRead(conversationId) {
      try {
        console.log('Marking messages as read for conversation:', conversationId);
        
        const formData = new FormData();
        formData.append('conversation_id', conversationId);
        formData.append('admin_id', this.adminId);
        
        const response = await axios.post('https://mindcareindependent.com/api/mark_messages_read.php', formData);
        
        if (response.data.success) {
          console.log('Messages marked as read successfully');
        } else {
          console.error('Failed to mark messages as read:', response.data);
        }
      } catch (error) {
        console.error('Error marking messages as read:', error);
      }
    },
    
    // Method untuk manual refresh
    async manualRefresh() {
      try {
        console.log('Manual refresh triggered');
        this.isLoading = true;
        
        // Clear existing data
        this.conversations = [];
        if (this.selectedUser) {
          this.selectedUser.messages = [];
        }
        
        await this.$nextTick();
        
        // Reload conversations
        await this.loadConversations();
        
        // Reload selected user messages if any
        if (this.selectedUser) {
          await this.loadUserMessages(this.selectedUser);
        }
        
        console.log('Manual refresh completed');
      } catch (error) {
        console.error('Error in manual refresh:', error);
        this.errorMessage = 'Gagal refresh data. Coba lagi.';
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background: #f8f9fa;
}

.dashboard-container {
  display: flex;
  height: calc(100vh - 72px);
}

/* Sidebar Styles */
.dashboard-sidebar {
  width: 320px;
  background: white;
  border-right: 1px solid #e9ecef;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.sidebar-header {
  padding: 24px 20px 16px 20px;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.refresh-btn {
  background: #6C3483;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background: #5a2d6b;
}

.dashboard-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.inbox-section {
  padding: 20px;
  border-bottom: 1px solid #e9ecef;
}

.inbox-title {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  margin: 0 0 12px 0;
}

.inbox-filters {
  display: flex;
  gap: 16px;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.filter-item.active {
  background: #6C3483;
  color: white;
}

.filter-item:not(.active):hover {
  background: #f8f9fa;
}

.filter-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 600;
}

.filter-item:not(.active) .filter-count {
  background: #e9ecef;
  color: #666;
}

.conversation-list {
  flex: 1;
  overflow-y: auto;
  padding: 16px 0;
}

.conversation-header {
  font-size: 0.875rem;
  font-weight: 600;
  color: #666;
  margin: 0 0 12px 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.conversation-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  cursor: pointer;
  transition: background-color 0.2s;
  position: relative;
}

.conversation-item:hover {
  background: #f8f9fa;
}

.conversation-item.active {
  background: #e3f2fd;
  border-right: 3px solid #6C3483;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  border: 2px solid #e9ecef;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
  border-radius: 50%;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 2px;
  font-size: 0.875rem;
}

.user-message {
  color: #666;
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.unread-badge {
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

/* Main Chat Area */
.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: white;
}

.chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.chat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #e9ecef;
  background: white;
}

.chat-user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.close-chat {
  background: none;
  border: none;
  font-size: 24px;
  color: #666;
  cursor: pointer;
  padding: 4px;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.close-chat:hover {
  background: #f8f9fa;
  color: #333;
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.message {
  display: flex;
  margin-bottom: 8px;
}

.message.user {
  justify-content: flex-start;
}

.message.agent {
  justify-content: flex-end;
}

.message.system {
  justify-content: center;
}

.message-bubble {
  position: relative;
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 18px;
  word-wrap: break-word;
}

.message.user .message-bubble {
  background: #e9ecef;
  color: #333;
  border-bottom-left-radius: 4px;
}

.message.agent .message-bubble {
  background: #007bff;
  color: white;
  border-bottom-right-radius: 4px;
}

.message.system .message-bubble {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  font-size: 14px;
  text-align: center;
}

.message-bubble p {
  margin: 0 0 4px 0;
  line-height: 1.4;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  display: block;
  text-align: right;
}

.message.system .message-time {
  text-align: center;
  margin-top: 4px;
}

/* Message Input Area */
.message-input-area {
  background: white;
  border-top: 1px solid #e9ecef;
  padding: 16px 20px;
}

.input-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.input-field {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8f9fa;
  border-radius: 8px;
  padding: 8px 12px;
}

.input-icons {
  display: flex;
  gap: 8px;
}

.icon-btn {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.icon-btn:hover {
  background: #e9ecef;
  color: #333;
}

.message-input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
  color: #333;
  resize: none;
  min-height: 20px;
  max-height: 120px;
  overflow-y: auto;
  font-family: inherit;
  line-height: 1.4;
}

.message-input::placeholder {
  color: #999;
}

.action-buttons {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}



.send-btn {
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.send-btn:hover {
  background: #c82333;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #666;
  text-align: center;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  color: #333;
}

.empty-state p {
  margin: 0;
  color: #666;
}

/* Error Message */
.error-message {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #dc3545;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  z-index: 1000;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Loading State */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  color: #666;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #6C3483;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-conversations {
  text-align: center;
  padding: 40px 20px;
  color: #666;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-container {
    flex-direction: column;
  }
  
  .dashboard-sidebar {
    width: 100%;
    height: 300px;
  }
  
  .chat-main {
    height: calc(100vh - 372px);
  }
}
</style> 