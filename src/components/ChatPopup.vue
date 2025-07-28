<template>
  <div class="chat-popup-container">
    <!-- Floating Action Button -->
    <div class="fab-container">
      <button class="fab-button" @click="toggleChat" :class="{ 'active': isVisible }">
        <span class="fab-icon">M</span>
      </button>
    </div>

    <!-- Chat Popup Overlay -->
    <transition name="chat-slide">
      <div v-if="isVisible" class="chat-overlay">
        <div class="chat-popup">
          <!-- Header -->
          <div class="chat-header">
            <div class="user-info">
              <div class="user-avatar">
                <span class="avatar-icon">M</span>
              </div>
              <span class="user-name">Mindhelp</span>
            </div>
            <button class="close-btn" @click="toggleChat">&times;</button>
          </div>

          <!-- Chat Messages Area -->
          <div class="chat-messages" ref="messagesContainer">
            <div v-for="message in messages" :key="message.id" class="message" :class="message.type + '-message'">
              <div class="message-bubble" :class="{ 'file-message': message.isFile }">
                <p>{{ message.text }}</p>
                <span class="message-time">{{ message.time }}</span>
              </div>
            </div>
          </div>

          <!-- Message Input Area -->
          <div class="chat-input-area">
            <div class="input-container">
              <div class="input-field">
                <input 
                  v-model="newMessage" 
                  @keyup.enter="sendMessage"
                  type="text" 
                  placeholder="Ketik pesan..."
                  class="message-input"
                />
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
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import TimezoneHelper from '../utils/timezoneHelper.js';

export default {
  name: 'ChatPopup',
  data() {
    return {
      isVisible: false,
      newMessage: '',
      messages: [],
      currentTime: '13:34',
      userId: null, // Ambil dari auth/session
      adminId: null, // Akan diisi dari backend
      conversationId: null, // ID conversation aktif
      chatPollingInterval: null // Untuk polling pesan baru
    }
  },
  methods: {
    async toggleChat() {
      this.isVisible = !this.isVisible;
      if (this.isVisible) {
        // Clear messages dan conversationId saat pertama kali buka chat
        this.messages = [];
        this.conversationId = null;
        // Force reload data
        await this.loadChat();
        this.startChatPolling();
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      } else {
        this.stopChatPolling();
      }
    },
    startChatPolling() {
      // Polling setiap 3 detik untuk pesan baru
      this.chatPollingInterval = setInterval(async () => {
        if (this.isVisible && this.conversationId) {
          // Load chat untuk mendapatkan pesan baru tanpa mengganti yang sudah ada
          await this.loadMessages();
        }
      }, 3000);
    },
    stopChatPolling() {
      if (this.chatPollingInterval) {
        clearInterval(this.chatPollingInterval);
        this.chatPollingInterval = null;
      }
    },
    async loadChat() {
      try {
        // Ambil userId dari localStorage user object
        const user = JSON.parse(localStorage.getItem('user'));
        this.userId = user ? user.id : null;
        
        // Ambil adminId dari backend (admin dengan role admin pertama)
        if (!this.adminId) {
          const res = await axios.get('https://mindcareindependent.com/api/get_admin_id.php');
          this.adminId = parseInt(res.data.admin_id);
        }
        
        // Tambahkan log untuk debug
        console.log('Loading chat with userId:', this.userId, 'adminId:', this.adminId);
        
        if (!this.userId || !this.adminId) {
          console.error('Missing userId or adminId');
          return;
        }
        
        // Jika belum ada conversationId, cari atau buat conversation
        if (!this.conversationId) {
          await this.getOrCreateConversation();
        }
        
        // Load messages jika ada conversationId
        if (this.conversationId) {
          await this.loadMessages();
          // Scroll ke bawah setelah load messages
          this.$nextTick(() => {
            this.scrollToBottom();
          });
        }
      } catch (error) {
        console.error('Error loading chat:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        console.error('Failed to load messages');
      }
    },
    async getOrCreateConversation() {
      try {
        const formData = new FormData();
        formData.append('user_id', this.userId);
        formData.append('admin_id', this.adminId);
        
        console.log('Sending conversation request with:', {
          user_id: this.userId,
          admin_id: this.adminId
        });
        
        const res = await axios.post('https://mindcareindependent.com/api/get_or_create_conversation.php', formData);
        this.conversationId = res.data.conversation_id;
        console.log('Conversation ID:', this.conversationId);
      } catch (error) {
        console.error('Error getting or creating conversation:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        console.error('Failed to create or get conversation');
      }
    },
    async loadMessages() {
      try {
        if (!this.conversationId) {
          console.error('Conversation ID not available.');
          return;
        }
        console.log('Loading messages for conversation ID:', this.conversationId);
        const params = { conversation_id: this.conversationId, _t: Date.now() };
        const res = await axios.get('https://mindcareindependent.com/api/get_messages.php', { params });
        console.log('API Response:', res.data);
        
        if (res.data.success && res.data.messages) {
          const msgs = res.data.messages;
          console.log('Raw messages from API:', msgs);
          console.log('First message details:', msgs[0]);
          
          // Convert database messages to UI format
          const dbMessages = msgs.map(msg => {
            console.log('Processing message:', msg);
            const processedMsg = {
              id: msg.id,
              text: msg.message || msg.mes || 'No message content',
              type: msg.sender_role === 'user' ? 'user' : 'agent',
              time: TimezoneHelper.formatTimeForDisplay(msg.created_at),
              isFile: !!msg.file_url,
              fileUrl: msg.file_url,
              fileName: msg.file_name
            };
            console.log('Processed message:', processedMsg);
            return processedMsg;
          });
          
          // Replace all messages with database messages to ensure consistency
          this.messages = [...dbMessages]; // Force new array
          
          console.log('Final processed messages:', this.messages);
          console.log('Messages array length:', this.messages.length);
          console.log('First message text:', this.messages[0]?.text);
          console.log('First message time:', this.messages[0]?.time);
        } else {
          console.error('API response not successful or no messages:', res.data);
          this.showUploadStatus('Gagal memuat pesan. Coba lagi.', 'error');
        }
      } catch (error) {
        console.error('Error loading messages:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.showUploadStatus('Gagal memuat pesan. Coba lagi.', 'error');
      }
    },
    async sendMessage() {
      if (!this.newMessage.trim()) return;
      if (!this.userId || !this.adminId) {
        this.showUploadStatus('User tidak ditemukan. Silakan login ulang.', 'error');
        return;
      }
      
      const messageText = this.newMessage.trim();
      this.newMessage = '';
      
      const formData = new FormData();
      formData.append('sender_id', this.userId);
      formData.append('receiver_id', parseInt(this.adminId));
      formData.append('sender_role', 'user');
      formData.append('message', messageText);
      
      // Tambahkan waktu client dengan zona waktu yang akurat
      const timeData = TimezoneHelper.addTimezoneToRequest();
      formData.append('current_time', timeData.current_time);
      formData.append('timezone', timeData.timezone);
      
      try {
        console.log('Sending message with data:', {
          sender_id: this.userId,
          receiver_id: parseInt(this.adminId),
          sender_role: 'user',
          message: messageText
        });
        
        console.log('Data types:', {
          sender_id_type: typeof this.userId,
          receiver_id_type: typeof parseInt(this.adminId),
          adminId_original: this.adminId,
          adminId_parsed: parseInt(this.adminId)
        });
        
        const response = await axios.post('https://mindcareindependent.com/api/send_message_new.php', formData);
        console.log('Send message response:', response.data);
        
        // Update conversationId jika belum ada
        if (response.data.conversation_id && !this.conversationId) {
          this.conversationId = response.data.conversation_id;
        }
        
        // Reload messages untuk mendapatkan data terbaru dari database
        await this.loadMessages();
        this.scrollToBottom();
      } catch (err) {
        console.error('Error sending message:', err);
        console.error('Error response:', err.response);
        

        
        let errorMsg = 'Gagal mengirim pesan. Coba lagi.';
        if (err.response) {
          if (err.response.status === 500) {
            errorMsg = 'Server error. Silakan coba lagi nanti.';
          } else if (err.response.status === 400) {
            errorMsg = 'Data tidak valid. Silakan cek input Anda.';
          }
          
          if (err.response.data && err.response.data.error) {
            errorMsg += ' (' + err.response.data.error + ')';
            if (err.response.data.details) {
              errorMsg += ' - ' + JSON.stringify(err.response.data.details);
            }
          }
        } else if (err.request) {
          errorMsg = 'Tidak dapat terhubung ke server. Cek koneksi internet Anda.';
        }
        
        this.showUploadStatus(errorMsg, 'error');
      }
    },
    getCurrentTime() {
      const now = new Date()
      return now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
      })
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer
        if (container) {
          container.scrollTop = container.scrollHeight
        }
      })
    }
  },
  mounted() {
    // Bisa auto-load chat jika ingin
  },
  beforeDestroy() {
    this.stopChatPolling();
  }
}
</script>

<style scoped>
.chat-popup-container {
  position: relative;
  z-index: 1000;
}

/* Floating Action Button */
.fab-container {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 1002; /* Lebih tinggi dari chat popup */
}

.fab-button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #8b5cf6, #a78bfa);
  border: none;
  box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fab-button:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 25px rgba(139, 92, 246, 0.4);
}

.fab-button.active {
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  transform: scale(1.05);
}

.fab-icon {
  color: white;
  font-size: 20px;
  font-weight: bold;
  font-family: 'Inter', sans-serif;
}

/* Chat Overlay */
.chat-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
  z-index: 1000;
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
  padding: 20px;
  pointer-events: none;
}

.chat-popup {
  background: white;
  border-radius: 16px 16px 0 0;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 500px;
  height: 600px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  pointer-events: auto;
  margin-bottom: 80px; /* Memberikan ruang untuk FAB */
  border: 1px solid #e9ecef;
}

/* Header */
.chat-header {
  background: white;
  border-bottom: 1px solid #e9ecef;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #007bff;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 18px;
}

.avatar-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.user-name {
  font-weight: 600;
  color: #333;
  font-size: 16px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  color: #6c757d;
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

.close-btn:hover {
  background: #f8f9fa;
  color: #333;
}

/* Messages Area */
.chat-messages {
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

.agent-message {
  justify-content: flex-start;
  align-items: flex-start;
}

.user-message {
  justify-content: flex-end;
  align-items: flex-end;
}



.message-bubble {
  position: relative;
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 18px;
  word-wrap: break-word;
}

.agent-message .message-bubble {
  background: #e9ecef;
  color: #333;
  border-bottom-left-radius: 4px;
  margin-right: auto;
  max-width: 70%;
}

.user-message .message-bubble {
  background: #007bff;
  color: white;
  border-bottom-right-radius: 4px;
  margin-left: auto;
  max-width: 70%;
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



/* File Message Styling */
.file-message {
  background: #f8f9fa !important;
  border: 1px solid #e9ecef;
}

.file-message p {
  font-weight: 500;
  color: #495057;
}

/* Input Area */
.chat-input-area {
  background: white;
  border-top: 1px solid #e9ecef;
  padding: 16px 20px;
}

/* Upload Status */
.upload-status {
  padding: 8px 12px;
  margin-bottom: 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  text-align: center;
}

.upload-status.error {
  background: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.upload-status.success {
  background: #dcfce7;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}

.upload-status.info {
  background: #dbeafe;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

.input-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.input-field {
  position: relative;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 24px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.message-input {
  flex: 1;
  border: none;
  outline: none;
  font-size: 14px;
  background: transparent;
  color: #333;
}

.message-input::placeholder {
  color: #6c757d;
}

.input-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.input-icons {
  display: flex;
  gap: 8px;
}

.icon-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  color: #6c757d;
  transition: background-color 0.2s;
}

.icon-btn:hover {
  background: #f8f9fa;
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

/* Animation */
.chat-slide-enter-active,
.chat-slide-leave-active {
  transition: all 0.3s ease;
}

.chat-slide-enter-from,
.chat-slide-leave-to {
  opacity: 0;
  transform: translateY(100%);
}

.chat-slide-enter-to,
.chat-slide-leave-from {
  opacity: 1;
  transform: translateY(0);
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 768px) {
  .chat-overlay {
    padding: 10px;
  }
  
  .chat-popup {
    height: 500px;
    max-width: calc(100vw - 20px);
    margin-bottom: 70px;
  }
  
  .fab-container {
    bottom: 20px;
    right: 20px;
  }
  
  .fab-button {
    width: 50px;
    height: 50px;
  }
  
  .fab-icon {
    font-size: 20px;
  }
  
  .chat-header {
    padding: 12px 16px;
  }
  
  .chat-messages {
    padding: 16px;
  }
  
  .message-bubble {
    max-width: 85%;
  }
  
  .chat-input-area {
    padding: 12px 16px;
  }
  
  .input-actions {
    flex-direction: column;
    gap: 8px;
    align-items: stretch;
  }
  
  .action-buttons {
    justify-content: space-between;
  }
  
  .action-btn {
    font-size: 11px;
    padding: 6px 8px;
  }
}
</style> 