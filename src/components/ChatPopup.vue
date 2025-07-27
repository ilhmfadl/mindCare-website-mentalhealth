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
            <!-- Agent Message -->
            <div class="message agent-message">
              <div class="message-bubble">
                <p>Hi, this is Mindhelp from Mental Health Support. How can I assist you today?</p>
                <span class="message-time">13:34</span>
              </div>
            </div>

            <!-- User Message -->
            <div class="message user-message">
              <div class="message-bubble">
                <p>Hi Mindhelp, I've been feeling anxious lately</p>
                <span class="message-time">13:34</span>
              </div>
            </div>

            <!-- Agent Message -->
            <div class="message agent-message">
              <div class="message-bubble">
                <p>I understand anxiety can be challenging. Let's talk about what might be causing this and explore some coping strategies together.</p>
                <span class="message-time">13:34</span>
              </div>
            </div>



            <!-- User Message -->
            <div class="message user-message">
              <div class="message-bubble">
                <p>I think it's related to work stress</p>
                <span class="message-time">13:34</span>
              </div>
            </div>

            <!-- Agent Message -->
            <div class="message agent-message">
              <div class="message-bubble">
                <p>Work stress is a common trigger for anxiety. Can you tell me more about what specific situations at work are causing you stress?</p>
                <span class="message-time">13:34</span>
              </div>
            </div>

            <!-- Dynamic Messages -->
            <div v-for="message in messages" :key="message.id" class="message" :class="message.type + '-message'">
              <div class="message-bubble" :class="{ 'file-message': message.isFile }">
                <p>{{ message.text }}</p>
                <span class="message-time">{{ message.time }}</span>
              </div>
            </div>
          </div>

          <!-- Message Input Area -->
          <div class="chat-input-area">
            <!-- Upload Status -->
            <div v-if="uploadStatus" class="upload-status" :class="uploadStatus.includes('error') ? 'error' : uploadStatus.includes('success') ? 'success' : 'info'">
              {{ uploadStatus }}
            </div>
            
            <div class="input-container">
              <div class="input-field">
                <input 
                  v-model="newMessage" 
                  @keyup.enter="sendMessage"
                  type="text" 
                  placeholder="Type &quot;/&quot; to use template message"
                  class="message-input"
                />
              </div>
              
              <div class="input-actions">
                <div class="input-icons">
                  <input 
                    type="file" 
                    ref="fileInput" 
                    @change="handleFileUpload" 
                    accept="image/*,.pdf,.doc,.docx,.txt"
                    style="display: none;"
                  />
                  <button class="icon-btn" title="Attach file (max 10MB)" @click="$refs.fileInput.click()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                    </svg>
                  </button>
                  <input 
                    type="file" 
                    ref="imageInput" 
                    @change="handleImageUpload" 
                    accept="image/*"
                    style="display: none;"
                  />
                  <button class="icon-btn" title="Send image (max 5MB)" @click="$refs.imageInput.click()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21,15 16,10 5,21"/>
                    </svg>
                  </button>
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
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'ChatPopup',
  data() {
    return {
      isVisible: false,
      newMessage: '',
      messages: [],
      currentTime: '13:34',
      maxImageSize: 5 * 1024 * 1024, // 5MB
      maxFileSize: 10 * 1024 * 1024, // 10MB
      uploadStatus: ''
    }
  },
  methods: {
    toggleChat() {
      this.isVisible = !this.isVisible
      if (this.isVisible) {
        this.$nextTick(() => {
          this.scrollToBottom()
        })
      }
    },
    openChat() {
      this.isVisible = true
      this.$nextTick(() => {
        this.scrollToBottom()
      })
    },
    closeChat() {
      this.isVisible = false
    },
    sendMessage() {
      if (this.newMessage.trim()) {
        const message = {
          id: Date.now(),
          text: this.newMessage,
          type: 'user',
          time: this.getCurrentTime()
        }
        this.messages.push(message)
        this.newMessage = ''
        this.scrollToBottom()
        
        // Simulate agent response after 1 second
        setTimeout(() => {
          const agentResponse = {
            id: Date.now() + 1,
            text: 'Thank you for sharing. I\'m here to listen and support you. How can I help you further?',
            type: 'agent',
            time: this.getCurrentTime()
          }
          this.messages.push(agentResponse)
          this.scrollToBottom()
        }, 1000)
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
    },
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (!file) return
      
      // Validasi ukuran file (5MB untuk gambar)
      if (file.size > this.maxImageSize) {
        this.showUploadStatus('Ukuran gambar maksimal 5MB!', 'error')
        event.target.value = '' // Reset input
        return
      }
      
      // Validasi tipe file
      if (!file.type.startsWith('image/')) {
        this.showUploadStatus('File harus berupa gambar!', 'error')
        event.target.value = ''
        return
      }
      
      // Proses upload gambar
      this.uploadFile(file, 'image')
      event.target.value = ''
    },
    handleFileUpload(event) {
      const file = event.target.files[0]
      if (!file) return
      
      // Validasi ukuran file (10MB untuk file umum)
      if (file.size > this.maxFileSize) {
        this.showUploadStatus('Ukuran file maksimal 10MB!', 'error')
        event.target.value = ''
        return
      }
      
      // Proses upload file
      this.uploadFile(file, 'file')
      event.target.value = ''
    },
    uploadFile(file, type) {
      // Simulasi upload file
      this.showUploadStatus(`Mengupload ${type === 'image' ? 'gambar' : 'file'}...`, 'info')
      
      setTimeout(() => {
        const message = {
          id: Date.now(),
          text: type === 'image' ? `📷 ${file.name} (${this.formatFileSize(file.size)})` : `📎 ${file.name} (${this.formatFileSize(file.size)})`,
          type: 'user',
          time: this.getCurrentTime(),
          isFile: true,
          fileName: file.name,
          fileSize: file.size
        }
        
        this.messages.push(message)
        this.scrollToBottom()
        this.showUploadStatus(`${type === 'image' ? 'Gambar' : 'File'} berhasil diupload!`, 'success')
        
        // Simulate agent response
        setTimeout(() => {
          const agentResponse = {
            id: Date.now() + 1,
            text: `Thank you for sharing the ${type === 'image' ? 'image' : 'file'}. I'll review it and get back to you with appropriate guidance.`,
            type: 'agent',
            time: this.getCurrentTime()
          }
          this.messages.push(agentResponse)
          this.scrollToBottom()
        }, 1000)
      }, 2000)
    },
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    },
    showUploadStatus(message, type) {
      this.uploadStatus = message
      setTimeout(() => {
        this.uploadStatus = ''
      }, 3000)
    }
  },
  mounted() {
    // Auto-show chat when component is mounted (for demo purposes)
    // this.isVisible = true
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