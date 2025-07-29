<template>
  <div class="forum-page">
    <!-- Hero Section -->
    <section class="forum-hero">
      <div class="forum-hero-content">
        <h1>Bertanya?...</h1>
        <p>Tempat berdiskusi</p>
      </div>
    </section>

    <div class="forum-main-content">
      <!-- Sidebar -->
      <aside class="forum-sidebar">
        <div class="sidebar-profile">
          <div class="sidebar-user-info" style="text-align: center;">
            <div style="font-weight: bold; color: #4B4B9F;">{{ user.name }}</div>
            <div style="font-size: 12px; color: #666;">{{ user.id ? 'active' : 'Belum login' }}</div>
          </div>
        </div>
        <div class="sidebar-categories">
          <h3>Filter</h3>
          <select class="dropdown-filter" v-model="filterType">
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
          </select>
          <button class="my-questions-btn" :class="{active: showMyQuestions}" @click="toggleMyQuestions">
            Pertanyaan Saya
          </button>
        </div>
      </aside>

      <!-- Main Forum Content -->
      <main class="forum-content">
        <div class="forum-search-row">
          <div class="forum-search-box">
            <input type="text" placeholder="Masukkan Teks yang ingin anda cari" v-model="searchQuery" />
            <span class="search-icon">
              <svg width="20" height="20" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
          </div>
          <button class="refresh-btn" @click="manualRefresh" :disabled="loading" title="Refresh semua data">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M1 4v6h6M23 20v-6h-6"/>
              <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
            </svg>
          </button>
          <button class="ask-btn" @click="handleAskButtonClick" :disabled="!user.id" :title="!user.id ? 'Login terlebih dahulu untuk bertanya' : 'Ajukan pertanyaan'">
            {{ user.id ? 'tanya?' : 'Login dulu' }}
          </button>
        </div>
        
        <!-- Mobile Filter Row -->
        <div class="mobile-filter-row">
          <select class="mobile-dropdown-filter" v-model="filterType">
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
          </select>
          <button class="mobile-my-questions-btn" :class="{active: showMyQuestions}" @click="toggleMyQuestions">
            Pertanyaan Saya
          </button>
        </div>

        <div class="forum-questions-list">
          
          <!-- Questions Count -->
          <div v-if="!loading && filteredQuestions.length > 0" class="questions-count" style="margin-bottom: 15px; color: #666; font-size: 14px;">
            Menampilkan {{ filteredQuestions.length }} dari {{ questions.length }} pertanyaan
          </div>
          
          <!-- Error State -->
          <div v-if="errorMessage && !loading" class="error-state" style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
            <strong>Error:</strong> {{ errorMessage }}
            <button @click="fetchQuestions" style="margin-left: 10px; padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Coba Lagi</button>
          </div>
          
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Memuat pertanyaan...</p>
          </div>
          
          <!-- Questions List -->
          <div
            v-else-if="filteredQuestions.length > 0"
            v-for="question in filteredQuestions"
            :key="question.id"
            class="forum-question-card"
            @click="openDetail(question)"
            style="cursor:pointer;"
          >
            <div class="question-header">
              <img class="question-avatar" :src="question.avatar" alt="User" />
              <div>
                <span class="question-user">{{ question.user }}</span>
                <span class="question-time">{{ question.time }}</span>
                <span v-if="question.updated && question.updated !== question.created_at" class="question-updated" style="font-size: 12px; color: #666; margin-left: 10px;">
                  (diperbarui {{ formatTime(question.updated) }})
                </span>
              </div>
              <span v-if="String(question.user_id) === String(user.id)" class="question-menu" @click.stop="toggleMenu(question.id)">⋮
                <div v-if="showMenuId === question.id" class="question-menu-dropdown">
                  <button @click.stop="editQuestion(question)">Edit</button>
                  <button @click.stop="deleteQuestion(question)">Hapus</button>
                </div>
              </span>
            </div>
            <div class="question-body">
              <div class="question-title">{{ question.title }}</div>
              <div class="question-desc">{{ question.desc }}</div>
            </div>
            <div class="question-footer">
              <span class="question-comments">
                <svg width="16" height="16" fill="none" stroke="#b6a7d6" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg> 
                {{ question.response_count || 0 }}
              </span>
            </div>
          </div>
          
          <!-- Empty State -->
          <div v-else class="empty-state">
            <div class="empty-icon">💬</div>
            <h3>Belum ada pertanyaan</h3>
            <p>Jadilah yang pertama mengajukan pertanyaan!</p>
            <button class="ask-first-btn" @click="openAsk">Ajukan Pertanyaan</button>
          </div>
        </div>
      </main>

      <!-- Featured Links -->
      <aside class="forum-featured-links">
        <h4>Featured links</h4>
        <ul>
          <li><a href="#" @click="goToPojokEdukasi">Cari info yang lebih akurat melalui jurnal!</a></li>
          <li><a href="#" @click="openChat">konsul dengan ahlinya</a></li>
        </ul>
      </aside>
    </div>

    <!-- Slide-in Detail Overlay -->
    <transition name="slide-detail">
      <div v-if="showDetail" class="forum-detail-overlay">
        <div class="forum-detail-panel">
          <button class="close-detail" @click="closeDetail">&times;</button>
          <div class="detail-header">
            <img class="detail-avatar" :src="selectedQuestion.avatar" alt="User" />
            <div>
              <span class="detail-user">@{{ selectedQuestion.user }}</span>
              <span class="detail-time">{{ selectedQuestion.time }}</span>
            </div>
          </div>
          <div class="detail-title">{{ selectedQuestion.title }}</div>
          <div class="detail-desc">{{ selectedQuestion.fullDesc }}</div>
          <!-- Kategori/tag dihapus -->
          <div class="detail-responses-box">
            <textarea v-model="responseText" placeholder="    Berikan tanggapan anda" rows="2"></textarea>
            <div class="detail-responses-actions">
              <button class="cancel-btn" @click="closeDetail">Cancel</button>
              <button 
                class="response-btn" 
                @click="submitResponse"
                :disabled="submittingResponse"
              >
                {{ submittingResponse ? 'Mengirim...' : 'Respon' }}
              </button>
            </div>
          </div>
          <div class="detail-answers">
            <div class="responses-header">
              <h3>Respon ({{ responses.length }})</h3>
              <button 
                @click="refreshResponses" 
                class="refresh-btn"
                :disabled="loadingResponses"
              >
                {{ loadingResponses ? 'Memuat...' : '🔄' }}
              </button>
            </div>
            <div v-if="loadingResponses" class="loading-responses">
              <p>Memuat respon...</p>
            </div>
            <div v-else-if="responses.length === 0" class="no-responses">
              <p>Belum ada respon untuk pertanyaan ini.</p>
            </div>
            <div v-else class="detail-answers">
              <div v-for="response in responses" :key="response.id" class="detail-answer">
              <div class="answer-header">
                  <div class="answer-user-info">
                    <img class="answer-avatar" :src="response.avatar" alt="User" />
                    <div class="answer-user-details">
                      <span class="answer-user">@{{ response.username }}</span>
                      <span class="answer-time">{{ response.formatted_time }}</span>
              </div>
            </div>
                  <div class="answer-actions">
                    <div v-if="String(response.user_id) !== String(user.id)" class="vote-section">
                      <button 
                        @click="voteResponse(response, 'upvote')" 
                        class="vote-btn upvote-btn"
                        :class="{ 'voted': response.userVoted === 'upvote' }"
                      >
                        ▲
                      </button>
                      <span class="vote-count">{{ response.vote || 0 }}</span>
                    </div>
                    <div v-if="String(response.user_id) === String(user.id)" class="response-actions">
                      <button 
                        v-if="!response.isEditing" 
                        @click="editResponse(response)" 
                        class="edit-response-btn"
                      >
                        ✏️
                      </button>
                      <button 
                        v-if="!response.isEditing" 
                        @click="deleteResponse(response)" 
                        class="delete-response-btn"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                </div>
                <div v-if="!response.isEditing" class="answer-body">
                  {{ response.response_text }}
                </div>
                <div v-else class="edit-response-form">
                  <textarea 
                    v-model="response.editText" 
                    class="edit-response-textarea"
                    rows="3"
                  ></textarea>
                  <div class="edit-response-actions">
                    <button @click="cancelEditResponse(response)" class="cancel-edit-btn">
                      Batal
                    </button>
                    <button @click="saveEditResponse(response)" class="save-edit-btn">
                      Simpan
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="forum-detail-backdrop" @click="closeDetail"></div>
      </div>
    </transition>

    <!-- Slide-in Ask Overlay -->
    <transition name="slide-ask">
      <div v-if="showAsk" class="forum-ask-overlay">
        <div class="forum-ask-panel">
          <button class="close-ask" @click="closeAsk">&times;</button>
          <h2>{{ editQuestionId ? 'Edit Pertanyaan' : 'Buat Pertanyaan Baru' }}</h2>
          <form @submit.prevent="submitAsk">
            <div class="ask-form-group">
              <label>Judul Pertanyaan</label>
              <input v-model="askForm.title" type="text" required placeholder="Judul pertanyaan" />
            </div>
            <div class="ask-form-group">
              <label>Deskripsi Singkat</label>
              <textarea v-model="askForm.desc" required placeholder="Deskripsi singkat" rows="2"></textarea>
            </div>
            <!-- Kategori input dihapus -->
            <div class="ask-form-actions">
              <button type="button" class="cancel-btn" @click="closeAsk">Batal</button>
              <button type="submit" class="response-btn">{{ editQuestionId ? 'Update' : 'Kirim' }}</button>
            </div>
          </form>
        </div>
        <div class="forum-ask-backdrop" @click="closeAsk"></div>
      </div>
    </transition>
  </div>
  
  <!-- Forum Modal -->
  <ForumModal
    :visible="showModal"
    :title="modalTitle"
    :message="modalMessage"
    :confirm-text="modalConfirmText"
    @confirm="handleModalConfirm"
    @cancel="handleModalCancel"
  />
  
  <!-- Chat Popup Component -->
  <ChatPopup ref="chatPopup" />

</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import ForumModal from '../components/ForumModal.vue';
import ChatPopup from '../components/ChatPopup.vue';
import TimezoneHelper from '../utils/timezoneHelper.js';

// Get user from localStorage or use default
const getCurrentUser = () => {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    const parsedUser = JSON.parse(storedUser);
    return {
      id: parsedUser.id,
      name: parsedUser.username || parsedUser.fullName || 'Anonymous User',
  avatar: 'https://randomuser.me/api/portraits/lego/1.jpg',
      role: parsedUser.role
    };
  }
  return {
    id: null,
    name: 'Anonymous User',
    avatar: 'https://randomuser.me/api/portraits/lego/1.jpg',
    role: null
  };
};

const user = ref(getCurrentUser());
const chatPopup = ref(null);


const categories = [
  'Semua',
  'Psikotik',
  'Neurosis',
  'PTSD',
];

const selectedCategory = ref('Semua');
function selectCategory(category) {
  selectedCategory.value = category;
}

const searchQuery = ref('');

const questions = ref([]);
const loading = ref(false);
const errorMessage = ref('');

function formatTime(dateString) {
  if (!dateString) return '';
  
  // Gunakan TimezoneHelper untuk format waktu yang akurat
  // Untuk forum, gunakan format relatif yang lebih pendek
  try {
    const tz = TimezoneHelper.getClientTimezone();
    const date = new Date(dateString + 'Z');
    const now = new Date();
    
    const diffMs = now - date;
    const diffMinutes = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffMinutes < 1) {
      return 'Baru saja';
    } else if (diffMinutes < 60) {
      return `${diffMinutes}m`;
    } else if (diffHours < 24) {
      return `${diffHours}j`;
    } else if (diffDays === 1) {
      return 'Kemarin';
    } else {
      return date.toLocaleTimeString('en-US', {
        timeZone: tz,
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    }
  } catch (e) {
    return TimezoneHelper.formatRelativeTime(dateString);
  }
}

async function fetchQuestions() {
  loading.value = true;
  try {
    const apiUrl = import.meta.env.PROD 
      ? 'https://mindcareindependent.com/api/forum_questions_complete.php'
      : '/backend/api/forum_questions_complete.php';
    
    // Tambahkan timestamp untuk cache-busting
    const timestamp = new Date().getTime();
    const urlWithCache = `${apiUrl}?t=${timestamp}`;
    
    const res = await axios.get(urlWithCache);
    
    if (res.data && res.data.success) {
      if (res.data.data && res.data.data.length > 0) {
        questions.value = res.data.data.map(q => ({
          id: q.id,
          user: q.username || 'Anonymous User',
          avatar: q.avatar || 'https://randomuser.me/api/portraits/lego/1.jpg',
          time: formatTime(q.created_at),
          created_at: q.created_at,
          updated: q.updated_at,
          title: q.title || 'No Title',
          desc: q.desc || 'No Description',
          fullDesc: q.desc || 'No Description',
          tag: '',
          comments: q.response_count || 0,
          response_count: q.response_count || 0,
          answers: [],
          user_id: q.user_id,
        }));
        
        // Debug: Log avatar data
        console.log('Questions with avatar data:', questions.value.map(q => ({
          id: q.id,
          user: q.user,
          avatar: q.avatar,
          avatar_from_api: q.avatar
        })));
        
        errorMessage.value = '';
      } else {
        questions.value = [];
        errorMessage.value = 'Belum ada pertanyaan di database. Silakan tambahkan pertanyaan pertama!';
      }
    } else {
      console.error('API returned error:', res.data?.message);
      questions.value = [];
      errorMessage.value = res.data?.message || 'Gagal memuat pertanyaan.';
    }
  } catch (e) {
    console.error('Error fetching questions:', e);
    errorMessage.value = 'Gagal memuat pertanyaan. Silakan coba lagi.';
    questions.value = [];
  } finally {
    loading.value = false;
  }
}
onMounted(async () => {
  // Perform comprehensive refresh on mount
  await performComprehensiveRefresh();
  
  // Auto refresh every 30 seconds - DISABLED
  // const interval = setInterval(async () => {
  //   await performComprehensiveRefresh();
  // }, 30000);
  
  // Listen for storage changes (login/logout)
  const handleStorageChange = (e) => {
    if (e.key === 'user') {
      user.value = getCurrentUser();
    }
  };
  
  // Listen for page focus (when user returns to tab)
  window.addEventListener('focus', handlePageFocus);
  
  // Listen for visibility change (when tab becomes visible)
  document.addEventListener('visibilitychange', handleVisibilityChange);
  
  window.addEventListener('storage', handleStorageChange);
  
  // Cleanup interval and event listener on component unmount
  onUnmounted(() => {
    // clearInterval(interval); // Commented out since interval is disabled
    window.removeEventListener('focus', handlePageFocus);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    window.removeEventListener('storage', handleStorageChange);
  });
});

const showDetail = ref(false);
const selectedQuestion = ref({});

function openDetail(question) {
  selectedQuestion.value = question;
  showDetail.value = true;
  // Clear responses first
  responses.value = [];
  
  // Set loading state
  loadingResponses.value = true;
  
  // Auto fetch responses untuk pertanyaan ini
  console.log('Auto fetching responses for question:', question.id);
  fetchResponses(question.id);
}
function closeDetail() {
  showDetail.value = false;
}

const showAsk = ref(false);
const askForm = ref({
  title: '',
  desc: '',
  tag: categories[0],
});
function openAsk() {
  // Check if user is logged in
  if (!user.value.id) {
    showConfirmationModal(
      'Peringatan',
      'Silakan login terlebih dahulu untuk bertanya!',
      'OK'
    );
    return;
  }
  showAsk.value = true;
}
const editQuestionId = ref(null);
function editQuestion(question) {
  showConfirmationModal(
    'Edit Pertanyaan',
    `Yakin ingin mengedit pertanyaan "${question.title}"?`,
    'Edit',
    () => {
  askForm.value = {
    title: question.title,
    desc: question.desc,
    tag: question.tag,
  };
  editQuestionId.value = question.id;
  showAsk.value = true;
  showMenuId.value = null;
    }
  );
}
function closeAsk() {
  if (editQuestionId.value) {
    // Jika sedang dalam mode edit, tanyakan konfirmasi
    showConfirmationModal(
      'Batalkan Edit',
      'Yakin ingin membatalkan edit pertanyaan? Perubahan tidak akan disimpan.',
      'Batalkan',
      () => {
  showAsk.value = false;
  askForm.value = { title: '', desc: '', tag: categories[0] };
  editQuestionId.value = null;
}
    );
  } else if (askForm.value.title.trim() || askForm.value.desc.trim()) {
    // Jika sedang dalam mode tambah baru dan ada isi, tanyakan konfirmasi
    showConfirmationModal(
      'Batalkan Tambah Pertanyaan',
      'Yakin ingin membatalkan? Pertanyaan tidak akan disimpan.',
      'Batalkan',
      () => {
        showAsk.value = false;
        askForm.value = { title: '', desc: '', tag: categories[0] };
        editQuestionId.value = null;
      }
    );
  } else {
    // Jika sedang dalam mode tambah baru dan kosong, langsung tutup
    showAsk.value = false;
    askForm.value = { title: '', desc: '', tag: categories[0] };
    editQuestionId.value = null;
  }
}
async function submitAsk() {
  const now = new Date();
  if (editQuestionId.value) {
    // Edit existing question - tampilkan konfirmasi
    showConfirmationModal(
      'Konfirmasi Update',
      'Yakin ingin menyimpan perubahan pada pertanyaan ini?',
      'Simpan',
      async () => {
        // Tutup panel form ask terlebih dahulu
        showAsk.value = false;
        
        try {
          const formData = new FormData();
          formData.append('id', editQuestionId.value);
          formData.append('user_id', user.value.id);
          formData.append('title', askForm.value.title);
          formData.append('desc', askForm.value.desc);
          
          // Kirim waktu lokal pengguna
          const year = now.getFullYear();
          const month = String(now.getMonth() + 1).padStart(2, '0');
          const day = String(now.getDate()).padStart(2, '0');
          const hours = String(now.getHours()).padStart(2, '0');
          const minutes = String(now.getMinutes()).padStart(2, '0');
          const seconds = String(now.getSeconds()).padStart(2, '0');
          
          const localTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
          formData.append('current_time', localTime);
          
          const updateApiUrl = import.meta.env.PROD 
            ? 'https://mindcareindependent.com/api/update_forum_question.php'
            : '/backend/api/update_forum_question.php';
          
          const res = await axios.post(updateApiUrl, formData);
          
          if (res.data && res.data.success) {
            // Refresh questions list
            await fetchQuestions();
            // Reset form
            askForm.value = { title: '', desc: '', tag: categories[0] };
            editQuestionId.value = null;
            showConfirmationModal(
              'Berhasil!',
              'Pertanyaan berhasil diupdate!',
              'OK'
            );
          } else {
            console.error('Failed to update question:', res.data?.message);
            showConfirmationModal(
              'Gagal',
              'Gagal mengupdate pertanyaan: ' + (res.data?.message || 'Terjadi kesalahan'),
              'OK'
            );
          }
        } catch (e) {
          console.error('Error updating question:', e);
          showConfirmationModal(
            'Gagal',
            'Gagal mengupdate pertanyaan. Silakan coba lagi.',
            'OK'
          );
        }
      }
    );
  } else {
    // Check if user is logged in
    if (!user.value.id) {
      showConfirmationModal(
        'Peringatan',
        'Silakan login terlebih dahulu untuk bertanya!',
        'OK'
      );
      return;
    }
    
    // Tambah baru ke backend - tampilkan konfirmasi
    showConfirmationModal(
      'Konfirmasi Kirim',
      'Yakin ingin mengirim pertanyaan ini?',
      'Kirim',
      async () => {
        // Tutup panel form ask terlebih dahulu
        showAsk.value = false;
        
        try {
          const formData = new FormData();
          formData.append('user_id', user.value.id);
          formData.append('title', askForm.value.title);
          formData.append('desc', askForm.value.desc);
          
          // Kirim waktu lokal pengguna
          const year = now.getFullYear();
          const month = String(now.getMonth() + 1).padStart(2, '0');
          const day = String(now.getDate()).padStart(2, '0');
          const hours = String(now.getHours()).padStart(2, '0');
          const minutes = String(now.getMinutes()).padStart(2, '0');
          const seconds = String(now.getSeconds()).padStart(2, '0');
          
          // Gunakan TimezoneHelper untuk waktu yang akurat
          const timeData = TimezoneHelper.addTimezoneToRequest();
          formData.append('current_time', timeData.current_time);
          formData.append('timezone', timeData.timezone);
          
          const createApiUrl = import.meta.env.PROD 
            ? 'https://mindcareindependent.com/api/forum_questions_complete.php'
            : '/backend/api/forum_questions_complete.php';
          
          const res = await axios.post(createApiUrl, formData);
          
          if (res.data && res.data.success) {
            // Refresh questions list
            await fetchQuestions();
            // Reset form
            askForm.value = { title: '', desc: '', tag: categories[0] };
    editQuestionId.value = null;
            // Optional: Show success message
            console.log('Pertanyaan berhasil dibuat!');
            showConfirmationModal(
              'Berhasil!',
              'Pertanyaan berhasil dikirim!',
              'OK'
            );
  } else {
            console.error('Failed to create question:', res.data?.message);
            // Optional: Show error message to user
            showConfirmationModal(
              'Gagal',
              'Gagal membuat pertanyaan: ' + (res.data?.message || 'Terjadi kesalahan'),
              'OK'
            );
          }
        } catch (e) {
          console.error('Error creating question:', e);
          // Optional: Show error message to user
          showConfirmationModal(
            'Gagal',
            'Gagal membuat pertanyaan. Silakan coba lagi.',
            'OK'
          );
  }
      }
    );
  }
}

// State untuk responses
const responses = ref([]);
const loadingResponses = ref(false);
const submittingResponse = ref(false);

const responseText = ref('');
async function submitResponse() {
  if (!responseText.value.trim()) return;
  
  console.log('Submitting response...');
  console.log('User ID:', user.value.id);
  console.log('Question ID:', selectedQuestion.value?.id);
  console.log('Response text:', responseText.value.trim());
  
  // Check if user is logged in
  if (!user.value.id) {
    showConfirmationModal(
      'Peringatan',
      'Silakan login terlebih dahulu untuk memberikan respon!',
      'OK'
    );
    return;
  }
  
  // Check if question is selected
  if (!selectedQuestion.value || !selectedQuestion.value.id) {
    showConfirmationModal(
      'Peringatan',
      'Pertanyaan tidak ditemukan!',
      'OK'
    );
    return;
  }
  
  submittingResponse.value = true; // Set loading state
  try {
    const formData = new FormData();
    formData.append('question_id', selectedQuestion.value.id);
    formData.append('user_id', user.value.id);
    formData.append('response_text', responseText.value.trim());
    
    // Gunakan TimezoneHelper untuk waktu yang akurat
    const timeData = TimezoneHelper.addTimezoneToRequest();
    formData.append('current_time', timeData.current_time);
    formData.append('timezone', timeData.timezone);
    
    console.log('FormData entries:');
    for (let [key, value] of formData.entries()) {
      console.log(key, ':', value);
    }
    
    const createResponseApiUrl = import.meta.env.PROD 
      ? 'https://mindcareindependent.com/api/create_forum_response.php'
      : '/backend/api/create_forum_response.php';
    
    console.log('API URL:', createResponseApiUrl);
    const res = await axios.post(createResponseApiUrl, formData);
    console.log('API Response:', res.data);
    
    if (res.data && res.data.success) {
      // Clear response text first
  responseText.value = '';
      
      // Refresh responses dari database untuk memastikan data konsisten
      console.log('Refreshing responses from database...');
      await fetchResponses(selectedQuestion.value.id);
      
      // Update counter di pertanyaan yang sedang ditampilkan
      const questionIndex = questions.value.findIndex(q => q.id === selectedQuestion.value.id);
      if (questionIndex !== -1) {
        questions.value[questionIndex].response_count = (questions.value[questionIndex].response_count || 0) + 1;
        questions.value[questionIndex].comments = questions.value[questionIndex].response_count;
      }
      
      // Optional: Tampilkan notifikasi sukses
      console.log('Respon berhasil dikirim!');
      console.log('Response data:', res.data.data);
    } else {
      console.error('Failed to create response:', res.data?.message);
      showConfirmationModal(
        'Gagal',
        'Gagal mengirim respon: ' + (res.data?.message || 'Terjadi kesalahan'),
        'OK'
      );
    }
  } catch (e) {
    console.error('Error creating response:', e);
    showConfirmationModal(
      'Gagal',
      'Gagal mengirim respon. Silakan coba lagi.',
      'OK'
    );
  } finally {
    submittingResponse.value = false; // Reset loading state
  }
}

// Function untuk mengambil responses
async function fetchResponses(questionId) {
  if (!questionId) return;
  
  console.log('Fetching responses for question ID:', questionId);
  loadingResponses.value = true;
  try {
    // Gunakan API normal dengan cache busting dan user_id
    const timestamp = new Date().getTime();
    const user_id = user.value.id || 0;
    const getResponsesApiUrl = import.meta.env.PROD 
      ? `https://mindcareindependent.com/api/get_forum_responses.php?question_id=${questionId}&user_id=${user_id}&t=${timestamp}`
      : `/backend/api/get_forum_responses.php?question_id=${questionId}&user_id=${user_id}&t=${timestamp}`;
    
    console.log('API URL:', getResponsesApiUrl);
    const res = await axios.get(getResponsesApiUrl);
    console.log('API Response:', res.data);
    
    // Check if response exists and has data
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      responses.value = res.data.data.map(response => ({
        ...response,
        formatted_time: formatTime(response.created_at),
        isEditing: false, // Add isEditing property
        editText: response.response_text, // Add editText property
        vote: response.vote || 0, // Add vote property
        userVoted: response.user_vote_type, // Add userVoted property from API
        avatar: response.avatar || 'https://randomuser.me/api/portraits/lego/1.jpg' // Add avatar property
      }));
      console.log('Mapped responses:', responses.value);
      
      // Debug: Log avatar data for responses
      console.log('Responses with avatar data:', responses.value.map(r => ({
        id: r.id,
        username: r.username,
        avatar: r.avatar,
        avatar_from_api: r.avatar
      })));
    } else {
      console.error('Failed to fetch responses:', res.data?.message || 'Invalid response format');
      console.error('Response data:', res.data);
      responses.value = [];
    }
  } catch (e) {
    console.error('Error fetching responses:', e);
    console.error('Error details:', {
      message: e.message,
      response: e.response?.data,
      status: e.response?.status
    });
    responses.value = [];
  } finally {
    loadingResponses.value = false;
  }
}

// Function untuk edit respon
function editResponse(response) {
  console.log('Editing response:', response);
  response.isEditing = true;
  response.editText = response.response_text; // Set edit text to current text
}

// Function untuk cancel edit respon
function cancelEditResponse(response) {
  console.log('Canceling edit for response:', response);
  response.isEditing = false;
  response.editText = response.response_text; // Reset to original text
}

// Function untuk save edit respon
async function saveEditResponse(response) {
  if (!response.editText.trim()) {
    showConfirmationModal(
      'Peringatan',
      'Respon tidak boleh kosong!',
      'OK'
    );
    return;
  }
  
  console.log('Saving edited response:', response);
  
  try {
    const formData = new FormData();
    formData.append('id', response.id);
    formData.append('user_id', user.value.id);
    formData.append('response_text', response.editText.trim());
    
    // Kirim waktu lokal pengguna
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const localTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    formData.append('current_time', localTime);
    
    const updateResponseApiUrl = import.meta.env.PROD 
      ? 'https://mindcareindependent.com/api/update_forum_response.php'
      : '/backend/api/update_forum_response.php';
    
    console.log('Update API URL:', updateResponseApiUrl);
    const res = await axios.post(updateResponseApiUrl, formData);
    console.log('Update API Response:', res.data);
    
    if (res.data && res.data.success) {
      // Update response text
      response.response_text = response.editText.trim();
      response.isEditing = false;
      
      // Update updated_at time
      response.updated_at = localTime;
      response.formatted_time = formatTime(localTime);
      
      console.log('Response updated successfully!');
    } else {
      console.error('Failed to update response:', res.data?.message);
      showConfirmationModal(
        'Gagal',
        'Gagal mengupdate respon: ' + (res.data?.message || 'Terjadi kesalahan'),
        'OK'
      );
    }
  } catch (e) {
    console.error('Error updating response:', e);
    showConfirmationModal(
      'Gagal',
      'Gagal mengupdate respon. Silakan coba lagi.',
      'OK'
    );
  }
}

// Function untuk delete respon
async function deleteResponse(response) {
  showConfirmationModal(
    'Hapus Respon',
    'Yakin ingin menghapus respon ini?',
    'Hapus',
    async () => {
      console.log('Deleting response:', response);
      
      try {
        const formData = new FormData();
        formData.append('id', response.id);
        formData.append('user_id', user.value.id);
        
        const deleteResponseApiUrl = import.meta.env.PROD 
          ? 'https://mindcareindependent.com/api/delete_forum_response.php'
          : '/backend/api/delete_forum_response.php';
        
        console.log('Delete API URL:', deleteResponseApiUrl);
        const res = await axios.post(deleteResponseApiUrl, formData);
        console.log('Delete API Response:', res.data);
        
        if (res.data && res.data.success) {
          // Remove response from local array
          const responseIndex = responses.value.findIndex(r => r.id === response.id);
          if (responseIndex !== -1) {
            responses.value.splice(responseIndex, 1);
          }
          
          // Update counter di pertanyaan yang sedang ditampilkan
          const questionIndex = questions.value.findIndex(q => q.id === selectedQuestion.value.id);
          if (questionIndex !== -1) {
            questions.value[questionIndex].response_count = Math.max(0, (questions.value[questionIndex].response_count || 0) - 1);
            questions.value[questionIndex].comments = questions.value[questionIndex].response_count;
          }
          
          console.log('Response deleted successfully!');
        } else {
          console.error('Failed to delete response:', res.data?.message);
          showConfirmationModal(
            'Gagal',
            'Gagal menghapus respon: ' + (res.data?.message || 'Terjadi kesalahan'),
            'OK'
          );
        }
      } catch (e) {
        console.error('Error deleting response:', e);
        showConfirmationModal(
          'Gagal',
          'Gagal menghapus respon. Silakan coba lagi.',
          'OK'
        );
      }
    }
  );
}

// Function untuk auto-load responses jika ada pertanyaan yang sedang ditampilkan
async function autoLoadResponsesIfNeeded() {
  // Jika ada pertanyaan yang sedang ditampilkan, load responses
  if (selectedQuestion.value && selectedQuestion.value.id) {
    console.log('Auto loading responses for existing selected question:', selectedQuestion.value.id);
    await fetchResponses(selectedQuestion.value.id);
  } else {
    console.log('No selected question found, skipping auto-load responses');
  }
}

// Function untuk refresh responses manual
async function refreshResponses() {
  if (selectedQuestion.value && selectedQuestion.value.id) {
    await fetchResponses(selectedQuestion.value.id);
  }
}

// Function untuk vote respon
async function voteResponse(response, voteType) {
  // Check if user is logged in
  if (!user.value.id) {
    showConfirmationModal(
      'Peringatan',
      'Silakan login terlebih dahulu untuk memberikan vote!',
      'OK'
    );
    return;
  }
  
  // Check if user is voting their own response
  if (String(response.user_id) === String(user.value.id)) {
    showConfirmationModal(
      'Peringatan',
      'Anda tidak bisa vote respon Anda sendiri!',
      'OK'
    );
    return;
  }
  
  console.log('Voting response:', response.id, 'Type:', voteType, 'Current userVoted:', response.userVoted);
  
  try {
    const formData = new FormData();
    formData.append('response_id', response.id);
    formData.append('user_id', user.value.id);
    formData.append('vote_type', voteType);
    
    const voteApiUrl = import.meta.env.PROD 
      ? 'https://mindcareindependent.com/api/vote_response.php'
      : '/backend/api/vote_response.php';
    
    console.log('Vote API URL:', voteApiUrl);
    const res = await axios.post(voteApiUrl, formData);
    console.log('Vote API Response:', res.data);
    
    if (res.data && res.data.success) {
      // Update vote count locally
      response.vote = res.data.data.new_vote;
      
      // Update user vote status based on action
      if (res.data.data.action === 'added') {
        response.userVoted = voteType;
      } else if (res.data.data.action === 'removed') {
        response.userVoted = null;
      } else if (res.data.data.action === 'changed') {
        response.userVoted = voteType;
      }
      
      console.log('Vote successful! New vote count:', response.vote, 'User voted:', response.userVoted);
      
      // Optional: Show success message
      // showConfirmationModal('Sukses', 'Vote berhasil ditambahkan!', 'OK');
    } else {
      console.error('Failed to vote:', res.data?.message);
      showConfirmationModal(
        'Gagal',
        'Gagal memberikan vote: ' + (res.data?.message || 'Terjadi kesalahan'),
        'OK'
      );
    }
  } catch (e) {
    console.error('Error voting response:', e);
    showConfirmationModal(
      'Gagal',
      'Gagal memberikan vote. Silakan coba lagi.',
      'OK'
    );
  }
}

const showMenuId = ref(null);

// Modal state
const showModal = ref(false);
const modalTitle = ref('');
const modalMessage = ref('');
const modalConfirmText = ref('Yakin');
const modalAction = ref(null);

// Modal functions
function showConfirmationModal(title, message, confirmText = 'Yakin', action) {
  modalTitle.value = title;
  modalMessage.value = message;
  modalConfirmText.value = confirmText;
  modalAction.value = action;
  showModal.value = true;
}

function handleModalConfirm() {
  if (modalAction.value) {
    modalAction.value();
  }
  showModal.value = false;
}

function handleModalCancel() {
  showModal.value = false;
}

function toggleMenu(id) {
  showMenuId.value = showMenuId.value === id ? null : id;
}

function deleteQuestion(question) {
  showConfirmationModal(
    'Konfirmasi Hapus',
    `Yakin ingin menghapus pertanyaan "${question.title}"?`,
    'Hapus',
    async () => {
      const formData = new FormData();
      formData.append('id', question.id);
      formData.append('user_id', user.value.id);
      
      const deleteApiUrl = import.meta.env.PROD
        ? 'https://mindcareindependent.com/api/delete_forum_question.php'
        : '/backend/api/delete_forum_question.php';
        
      try {
        const res = await axios.post(deleteApiUrl, formData);
        if (res.data && res.data.success) {
          
          // Hapus dari array local terlebih dahulu
          const index = questions.value.findIndex(q => q.id === question.id);
          if (index !== -1) {
            questions.value.splice(index, 1);
          }
          
          // Ambil ulang data dari database
          try {
            await fetchQuestions();
            showConfirmationModal(
              'Berhasil!',
              'Pertanyaan berhasil dihapus!',
              'OK'
            );
          } catch (error) {
            console.error('Error refreshing data:', error);
            showConfirmationModal(
              'Gagal',
              'Pertanyaan berhasil dihapus, tapi ada masalah saat refresh data.',
              'OK'
            );
          }
        } else {
          let errorMsg = res.data?.message || 'Gagal menghapus pertanyaan.';
          if (res.data?.debug) {
            errorMsg += `\n\nDebug info:\nRequest ID: ${res.data.debug.request_id}\nRequest User ID: ${res.data.debug.request_user_id}\nDB User ID: ${res.data.debug.db_user_id}\nMatch: ${res.data.debug.match}`;
          }
          showConfirmationModal(
            'Gagal',
            errorMsg,
            'OK'
          );
        }
      } catch (error) {
        console.error('=== DELETE ERROR ===');
        console.error('Delete error:', error);
        showConfirmationModal(
          'Gagal',
          'Gagal menghapus pertanyaan.',
          'OK'
        );
      }
  showMenuId.value = null;
    }
  );
}

const filterType = ref('terbaru');
const showMyQuestions = ref(false);
function toggleMyQuestions() {
  showMyQuestions.value = !showMyQuestions.value;
}

const filteredQuestions = computed(() => {
  let result = questions.value;
  
  // Filter by "My Questions" if enabled
  if (showMyQuestions.value) {
    result = result.filter(q => String(q.user_id) === String(user.value.id));
  }
  
  // Filter by category if not "Semua"
  if (selectedCategory.value !== 'Semua') {
    result = result.filter(q => q.tag === selectedCategory.value);
  }
  
  // Filter by search query
  if (searchQuery.value.trim() !== '') {
    const q = searchQuery.value.trim().toLowerCase();
    result = result.filter(
      item => item.title.toLowerCase().includes(q) || item.desc.toLowerCase().includes(q)
    );
  }
  
  // Sort by creation time
  if (filterType.value === 'terbaru') {
    result = [...result].sort((a, b) => new Date(b.updated || b.time) - new Date(a.updated || a.time));
  } else {
    result = [...result].sort((a, b) => new Date(a.updated || a.time) - new Date(b.updated || b.time));
  }
  
  return result;
});

// Function untuk melakukan refresh komprehensif
async function performComprehensiveRefresh() {
  console.log('🚀 Starting comprehensive refresh...');
  
  // Set loading state
  loading.value = true;
  
  try {
    // Refresh user data first
    user.value = getCurrentUser();
    console.log('✅ User data refreshed');
    
    // Fetch questions with fresh data
    await fetchQuestions();
    console.log('✅ Questions refreshed');
    
    // Auto load responses if needed
    await autoLoadResponsesIfNeeded();
    console.log('✅ Responses auto-loaded');
    
    // Clear any error messages
    errorMessage.value = '';
    
    console.log('🎉 Comprehensive refresh completed successfully!');
  } catch (error) {
    console.error('❌ Error during comprehensive refresh:', error);
    errorMessage.value = 'Gagal memuat data. Silakan coba lagi.';
  } finally {
    loading.value = false;
  }
}

// Function untuk manual refresh (bisa dipanggil dari tombol)
async function manualRefresh() {
  console.log('🔄 Manual refresh triggered');
  await performComprehensiveRefresh();
}

// Function untuk refresh saat route berubah atau halaman difokuskan
function handlePageFocus() {
  console.log('📱 Page focused, triggering refresh');
  performComprehensiveRefresh();
}

// Function untuk refresh saat user kembali ke tab
function handleVisibilityChange() {
  if (!document.hidden) {
    console.log('👁️ Tab became visible, triggering refresh');
    performComprehensiveRefresh();
  }
}

// Function untuk mengarahkan ke halaman login
function redirectToLogin() {
  console.log('🔐 Redirecting to login page...');
  console.log('Current path:', window.location.pathname);
  console.log('User status:', user.value.id ? 'Logged in' : 'Not logged in');
  
  // Menggunakan router untuk navigasi ke halaman login
  if (window.location.pathname.includes('/forum')) {
    // Jika di halaman forum, redirect ke login
    console.log('📍 Redirecting from forum to login page');
    window.location.href = '/login';
  } else {
    // Jika di halaman lain, gunakan router
    console.log('📍 Redirecting from other page to login page');
    // this.$router.push('/login'); // Uncomment jika menggunakan Vue Router
    window.location.href = '/login';
  }
}

function handleAskButtonClick() {
  console.log('🖱️ Ask button clicked');
  console.log('User ID:', user.value.id);
  
  // Check if user is logged in
  if (!user.value.id) {
    console.log('❌ User not logged in, redirecting to login');
    // Redirect ke halaman login
    redirectToLogin();
    return;
  }
  
  console.log('✅ User logged in, opening ask panel');
  // Jika sudah login, buka panel ask
  openAsk();
}

function goToPojokEdukasi() {
  console.log('🔗 Navigating to pojok edukasi ruang artikel...');
  console.log('Current path:', window.location.pathname);
  
  // Menggunakan router untuk navigasi ke halaman pojok edukasi bagian ruang artikel
  if (window.location.pathname.includes('/forum')) {
    // Jika di halaman forum, redirect ke pojok edukasi dengan anchor ke ruang artikel
    console.log('📍 Redirecting from forum to pojok edukasi ruang artikel');
    window.location.href = '/edukasi#ruang-artikel';
  } else {
    // Jika di halaman lain, gunakan router
    console.log('📍 Redirecting from other page to pojok edukasi ruang artikel');
    // this.$router.push('/edukasi#ruang-artikel'); // Uncomment jika menggunakan Vue Router
    window.location.href = '/edukasi#ruang-artikel';
  }
}

function openChat() {
  // Trigger chat popup
  if (chatPopup.value) {
    chatPopup.value.toggleChat();
  }
}



</script>

<style scoped>
.forum-page {
  background-color: var(--bg-primary);
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

.forum-hero {
  background: linear-gradient(135deg, #6a4c9b 0%, #8b5cf6 100%);
  color: white;
  padding: 80px 0;
  text-align: center;
}

.forum-hero-content h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.forum-hero-content p {
  font-size: 1.2rem;
  opacity: 0.9;
}

.forum-main-content {
  display: flex;
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.forum-sidebar {
  flex: 0 0 280px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 180px;
  margin-top: 80px;
  height: fit-content;
  background-color: var(--card-bg);
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 12px var(--shadow-light);
}

.sidebar-profile {
  margin-bottom: 18px;
}

.sidebar-categories h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 10px;
  color: var(--text-primary);
}

.sidebar-categories ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-categories li {
  padding: 8px 14px;
  border-radius: 8px;
  margin-bottom: 6px;
  font-size: 1rem;
  color: var(--text-secondary);
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.sidebar-categories li.active, .sidebar-categories li:hover {
  background: var(--bg-tertiary);
  color: var(--accent-purple);
}

.forum-content {
  flex: 1 1 0%;
  min-width: 0;
  margin-top: 80px;
}

.forum-search-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 28px;
}

.forum-search-box {
  flex: 1;
  position: relative;
  padding-right: 70px;
}

.forum-search-box input {
  width: 100%;
  padding: 16px 48px 16px 20px;
  border-radius: 14px;
  border: none;
  background: var(--input-bg);
  font-size: 1.05rem;
  color: var(--text-primary);
  outline: none;
  box-shadow: 0 1px 4px var(--shadow-light);
}

.search-icon {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.refresh-btn {
  background: var(--accent-purple);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.refresh-btn:hover:not(:disabled) {
  background: #5a3c8b;
  transform: translateY(-1px);
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.ask-btn {
  background: var(--button-primary);
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-btn:hover:not(:disabled) {
  background: var(--button-hover);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(236, 116, 74, 0.15);
}

.ask-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.ask-btn:not(:disabled):active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(236, 116, 74, 0.1);
}

.forum-questions-list {
  display: flex;
  flex-direction: column;
  gap: 26px;
}

.forum-question-card {
  background: var(--card-bg);
  border-radius: 16px;
  box-shadow: 0 2px 12px var(--shadow-light);
  padding: 26px 28px 20px 28px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: box-shadow 0.2s, transform 0.2s;
  border: 1px solid var(--border-color);
}

.forum-question-card:hover {
  box-shadow: 0 8px 24px var(--shadow-medium);
  transform: translateY(-2px) scale(1.01);
}

.question-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 4px;
}

.question-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  background: var(--border-color);
  box-shadow: 0 1px 4px var(--shadow-light);
}

.question-user {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1rem;
  display: block;
}

.question-time {
  font-size: 0.85rem;
  color: var(--text-muted);
  display: block;
}

.question-menu {
  margin-left: auto;
  font-size: 1.5rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s;
  position: relative;
}

.question-menu:hover {
  background: var(--bg-tertiary);
}

.question-menu-dropdown {
  position: absolute;
  top: 28px;
  right: 0;
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  box-shadow: 0 2px 8px var(--shadow-medium);
  z-index: 10;
  min-width: 100px;
  display: flex;
  flex-direction: column;
}

.question-menu-dropdown button {
  background: none;
  border: none;
  padding: 10px 16px;
  text-align: left;
  font-size: 1rem;
  color: var(--text-primary);
  cursor: pointer;
  transition: background 0.2s;
}

.question-menu-dropdown button:hover {
  background: var(--bg-tertiary);
}

.question-title {
  font-weight: 700;
  font-size: 1.13rem;
  margin-bottom: 2px;
  color: #222;
}

.question-desc {
  font-size: 1rem;
  color: #6b7280;
  margin-bottom: 2px;
}

.question-footer {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 4px;
}

.question-tag {
  background: #e5e0f7;
  color: #8b5cf6;
  font-size: 0.89rem;
  padding: 4px 14px;
  border-radius: 8px;
  font-weight: 600;
}

.question-comments {
  font-size: 0.97rem;
  color: #b6a7d6;
  display: flex;
  align-items: center;
  gap: 4px;
}

.forum-featured-links {
  flex: 0 0 220px;
  background: #f7f5f9;
  border-radius: 18px;
  padding: 28px 18px;
  box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
  margin-top: 80px;
  min-width: 180px;
  height: fit-content;
  align-self: flex-start;
}

.forum-featured-links h4 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 10px;
}

.forum-featured-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.forum-featured-links li {
  margin-bottom: 10px;
}

.forum-featured-links a {
  color: #3b82f6;
  text-decoration: none;
  font-size: 1.01rem;
  transition: color 0.2s, transform 0.2s;
  display: inline-block;
  cursor: pointer;
}

.forum-featured-links a:hover {
  color: #1d4ed8;
  transform: translateX(2px);
}

.forum-featured-links a:active {
  transform: translateX(1px);
}

@media (max-width: 1100px) {
  .forum-main-content {
    flex-direction: column;
    padding: 0 16px 40px 16px;
    gap: 0;
  }
  .forum-sidebar, .forum-featured-links {
    margin-bottom: 24px;
    margin-top: 0;
    flex: unset;
    min-width: 0;
    width: 100%;
    align-self: unset;
  }
  .forum-featured-links {
    margin-top: 24px;
  }
}

@media (max-width: 700px) {
  .forum-hero {
    padding: 40px 0 20px 0;
  }
  .forum-hero-content {
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 20px;
  }
  .forum-hero-content h1 {
    font-size: 1.8rem;
    margin-bottom: 8px;
    margin-left: 0;
    text-align: center;
  }
  .forum-hero-content p {
    font-size: 1rem;
    margin-left: 0;
    text-align: center;
  }
  .forum-main-content {
    flex-direction: column;
    padding: 0 16px 20px 16px;
    gap: 0;
    margin-top: -40px;
  }
  .forum-sidebar {
    display: none; /* Sembunyikan sidebar di mobile */
  }
  .mobile-filter-row {
    display: flex; /* Tampilkan mobile filter di mobile */
    margin-bottom: 16px;
    gap: 12px;
    flex-wrap: wrap; /* Allow wrapping on very small screens */
  }
  
  .mobile-dropdown-filter {
    flex: 1;
    min-width: 120px; /* Ensure minimum width */
  }
  
  .mobile-my-questions-btn {
    flex-shrink: 0; /* Prevent button from shrinking */
  }
  .forum-featured-links {
    padding: 20px 16px;
    margin-top: 20px;
    margin-bottom: 0px;
    min-width: 0;
    width: 100%;
    font-size: 0.9rem;
    border-radius: 12px;
  }
  .forum-content {
    margin-top: 0;
    font-size: 0.9rem;
  }
  .forum-search-row {
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
    padding: 16px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .forum-search-box {
    padding-right: 0;
  }
  .forum-search-box input {
    padding: 12px 16px;
    font-size: 0.9rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
  }
  .refresh-btn {
    padding: 12px;
    border-radius: 8px;
    align-self: flex-end;
  }
  .refresh-btn svg {
    width: 18px;
    height: 18px;
  }
  .ask-btn {
    padding: 12px 24px;
    font-size: 0.9rem;
    border-radius: 8px;
    align-self: flex-end;
  }
  .forum-questions-list {
    gap: 12px;
  }
  .forum-question-card {
    padding: 16px;
    border-radius: 12px;
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .question-header {
    gap: 8px;
    margin-bottom: 8px;
  }
  .question-avatar {
    width: 32px;
    height: 32px;
  }
  .question-user {
    font-size: 0.9rem;
    font-weight: 600;
  }
  .question-time {
    font-size: 0.8rem;
  }
  .question-title {
    font-size: 1rem;
    margin-bottom: 4px;
    line-height: 1.4;
  }
  .question-desc {
    font-size: 0.85rem;
    margin-bottom: 8px;
    line-height: 1.5;
  }
  .question-footer {
    gap: 8px;
    margin-top: 8px;
  }
  .question-tag {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 6px;
  }
  .question-comments {
    font-size: 0.8rem;
    gap: 4px;
  }
  .forum-featured-links h4 {
    font-size: 1rem;
    margin-bottom: 8px;
  }
  .forum-featured-links a {
    font-size: 0.9rem;
    line-height: 1.4;
  }
  .forum-detail-panel {
    max-width: 100vw;
    padding: 16px;
    border-radius: 0;
    font-size: 0.9rem;
  }
  .detail-avatar {
    width: 36px;
    height: 36px;
  }
  .detail-user {
    font-size: 0.9rem;
  }
  .detail-time {
    font-size: 0.8rem;
  }
  .detail-title {
    font-size: 1.1rem;
    margin-bottom: 8px;
    line-height: 1.4;
  }
  .detail-desc {
    font-size: 0.9rem;
    margin-bottom: 16px;
    line-height: 1.5;
  }
  .detail-tag span {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 6px;
    margin-bottom: 8px;
  }
  .detail-responses-box textarea {
    font-size: 0.9rem;
    padding: 12px;
    border-radius: 8px;
    min-height: 80px;
  }
  .detail-responses-actions {
    gap: 8px;
    margin-top: 8px;
  }
  .cancel-btn, .response-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    border-radius: 8px;
  }
  .detail-answers {
    gap: 12px;
    margin-top: 16px;
  }
  .detail-answer {
    border-radius: 8px;
    padding: 12px;
    font-size: 0.9rem;
  }
  .answer-header {
    font-size: 0.85rem;
    gap: 8px;
    margin-bottom: 12px;
  }
  .answer-time {
    font-size: 0.8rem;
  }
  .answer-body {
    font-size: 0.9rem;
    line-height: 1.5;
  }
  .forum-detail-overlay ~ .forum-main-content,
  .forum-main-content.panel-shift {
    margin-left: 0;
  }
}

.slide-detail-enter-active, .slide-detail-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-detail-enter-from, .slide-detail-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
.slide-detail-enter-to, .slide-detail-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.forum-detail-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2000;
  display: flex;
  justify-content: flex-end;
  /* pastikan tidak membatasi overflow panel */
  overflow: hidden;
}
.forum-detail-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: relative;
  z-index: 2010;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-detail-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2005;
}
.close-detail {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2020;
}
.detail-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 10px;
}
.detail-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  background: #e5e7eb;
}
.detail-user {
  font-weight: 700;
  color: #6a4c9b;
  font-size: 1.1rem;
  display: block;
}
.detail-time {
  font-size: 0.95rem;
  color: #b6a7d6;
  display: block;
}
.detail-title {
  font-weight: 700;
  font-size: 1.18rem;
  margin-bottom: 8px;
  color: #222;
}
.detail-desc {
  font-size: 1.01rem;
  color: #444;
  margin-bottom: 12px;
  white-space: pre-line;
}
.detail-tag span {
  background: #e5e0f7;
  color: #8b5cf6;
  font-size: 0.92rem;
  padding: 4px 14px;
  border-radius: 8px;
  font-weight: 600;
  margin-bottom: 18px;
  display: inline-block;
}
.detail-responses-box {
  margin: 18px 0 18px 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.detail-responses-box textarea {
  width: 100%;
  padding: 14px 1px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1.08rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
  font-family: 'Inter', 'Segoe UI', 'sans-serif';
  transition: border 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 4px rgba(160, 130, 255, 0.06);
  resize: vertical;
}
.detail-responses-box textarea:focus {
  border: 1.5px solid #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
  background: #f3f0f7;
}
.detail-responses-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
.cancel-btn {
  background: #e5e7eb;
  color: #6a4c9b;
  border: none;
  border-radius: 8px;
  padding: 7px 18px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.response-btn{
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 7px 18px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.response-btn:hover {
  background: #d45d2c;
}
.response-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: #ccc;
}
.response-btn:disabled:hover {
  background-color: #ccc;
}
.detail-answers {
  margin-top: 20px;
  padding-bottom: 100px; /* Tambahkan padding bottom agar respon paling bawah terlihat */
  max-height: 60vh; /* Batasi tinggi maksimal */
  overflow-y: auto; /* Tambahkan scroll jika konten terlalu panjang */
  scrollbar-width: thin; /* Firefox */
  scrollbar-color: #b6a7d6 #f0f0f0; /* Firefox */
}

/* Custom scrollbar untuk Webkit browsers */
.detail-answers::-webkit-scrollbar {
  width: 6px;
}

.detail-answers::-webkit-scrollbar-track {
  background: #f0f0f0;
  border-radius: 3px;
}

.detail-answers::-webkit-scrollbar-thumb {
  background: #b6a7d6;
  border-radius: 3px;
}

.detail-answers::-webkit-scrollbar-thumb:hover {
  background: #8b5cf6;
}

.detail-answer {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  border-left: 4px solid #6a4c9b;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}

.detail-answer:hover {
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transform: translateY(-1px);
}

.detail-answer:last-child {
  margin-bottom: 0;
}

.answer-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.answer-user-info {
  display: flex;
  align-items: center;
  gap: 8px; /* Tambahkan gap untuk avatar */
}

.answer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  background: #e5e7eb;
  flex-shrink: 0; /* Mencegah avatar menyusut */
}

.answer-user-details {
  display: flex;
  flex-direction: column;
  gap: 4px; /* Tambahkan jarak antara username dan waktu */
}

.answer-user {
  font-weight: 600;
  color: #6a4c9b;
  font-size: 0.95rem;
}

.answer-time {
  font-size: 0.85rem;
  color: #888;
}

.answer-body {
  font-size: 0.95rem;
  color: #444;
  line-height: 1.5;
  white-space: pre-line;
}
@media (max-width: 700px) {
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
}

.slide-ask-enter-active, .slide-ask-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-ask-enter-from, .slide-ask-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-ask-enter-to, .slide-ask-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.forum-ask-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2100;
  display: flex;
  justify-content: flex-end;
}
.forum-ask-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: absolute;
  top: 0;
  right: 0;
  left: auto;
  z-index: 2110;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-ask-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2105;
}
.close-ask {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2120;
}
.ask-form-group {
  margin-bottom: 18px;
  display: flex;
  flex-direction: column;
}
.ask-form-group label {
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 6px;
}
.ask-form-group input, .ask-form-group textarea, .ask-form-group select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
}
.ask-form-group textarea {
  min-height: 70px;
  resize: vertical;
}
.ask-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 700px) {
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
  }
  .forum-ask-panel {
    max-width: 100vw;
    margin-left: 0;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
    border-radius: 0;
  }
  .close-ask {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  .ask-form-group {
    margin-bottom: 16px;
  }
  .ask-form-group label {
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #e5e7eb;
  }
  .ask-form-group textarea {
    min-height: 80px;
    resize: vertical;
  }
  .ask-form-actions {
    gap: 12px;
    margin-top: 20px;
  }
  .cancel-btn, .response-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  /* Detail panel improvements for mobile */
  .forum-detail-panel {
    padding: 20px 16px;
    border-radius: 0;
  }
  
  .close-detail {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  
  .detail-header {
    margin-bottom: 16px;
  }
  
  .detail-title {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .detail-desc {
    font-size: 1rem;
    margin-bottom: 20px;
  }
  
  .detail-responses-box {
    margin: 20px 0;
  }
  
  .detail-responses-box textarea {
    min-height: 100px;
    font-size: 1rem;
  }
  
  .detail-responses-actions {
    margin-top: 12px;
  }
  
  .detail-answers {
    margin-top: 20px;
    padding-bottom: 100px;
  }
}
@media (min-width: 701px) {
  .dropdown-categories {
    display: none !important;
  }
  .sidebar-categories ul {
    display: block !important;
  }
}
@media (min-width: 701px) {
  .forum-search-row {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #faf7f3;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 14px;
    padding-top: 8px;
    padding-bottom: 8px;
  }
  .forum-sidebar {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f8f3fd;
    box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
    border-radius: 18px;
  }
  .forum-featured-links {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f7f5f9;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 18px;
  }
}
.dropdown-filter {
  display: block;
  width: 100%;
  padding: 7px 10px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  margin-bottom: 8px;
  background: #faf7f3;
  color: #6a4c9b;
}
.my-questions-btn {
  width: 100%;
  padding: 8px 0;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f7f5f9;
  color: #6a4c9b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.my-questions-btn.active, .my-questions-btn:hover {
  background: #e5e0f7;
  color: #8b5cf6;
}

/* Loading and Empty States */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  color: #6a4c9b;
}

.loading-spinner {
  border: 4px solid #f3f0f7;
  border-top: 4px solid #6a4c9b;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.7;
}

.empty-state h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #6a4c9b;
}

.empty-state p {
  font-size: 1rem;
  color: #8b7a9b;
  margin-bottom: 20px;
}

.ask-first-btn {
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-first-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
}

/* Mobile Filter Styles */
.mobile-filter-row {
  display: none;
  gap: 12px;
  margin-bottom: 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  font-size: 0.9rem;
  color: #6a4c9b;
  font-weight: 500;
}

.mobile-dropdown-filter:focus {
  outline: none;
  border-color: #6a4c9b;
  box-shadow: 0 0 0 3px rgba(106, 76, 155, 0.1);
}

.mobile-my-questions-btn {
  padding: 12px 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.mobile-my-questions-btn.active {
  background: #e5e0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
  box-shadow: 0 2px 4px rgba(106, 76, 155, 0.2);
}

.mobile-my-questions-btn:hover {
  background: #f3f0f7;
  transform: translateY(-1px);
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 20px;
    margin: 20px 0;
  }
  
  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }
  
  .empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .empty-state p {
    font-size: 1rem;
    margin-bottom: 24px;
    line-height: 1.5;
  }
  
  .ask-first-btn {
    padding: 12px 28px;
    font-size: 1rem;
    border-radius: 8px;
  }
  
  .detail-answer {
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 12px;
  }
  
  .answer-header {
    margin-bottom: 12px;
  }
  
  .answer-user {
    font-size: 0.95rem;
  }
  
  .answer-time {
    font-size: 0.85rem;
  }
  
  .answer-body {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
  }
  
  .responses-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }
}
.loading-responses, .no-responses {
  text-align: center;
  padding: 20px;
  color: #888;
  font-style: italic;
}
.loading-responses p, .no-responses p {
  margin: 0;
}

.responses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.responses-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin: 0;
}

.refresh-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #f0f0f0;
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Response Actions */
.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.edit-response-btn,
.delete-response-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-response-btn:hover {
  background-color: #f0f0f0;
}

.delete-response-btn:hover {
  background-color: #ffe6e6;
}

/* Edit Response Form */
.edit-response-form {
  margin-top: 8px;
}

.edit-response-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  background: #faf7f3;
  color: #444;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.edit-response-textarea:focus {
  border-color: #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.edit-response-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.cancel-edit-btn,
.save-edit-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-edit-btn {
  background: #e5e7eb;
  color: #6a4c9b;
}

.cancel-edit-btn:hover {
  background: #d1d5db;
}

.save-edit-btn {
  background: #ec744a;
  color: white;
}

.save-edit-btn:hover {
  background: #d45d2c;
}

/* Mobile Responsive */
@media (max-width: 700px) {
  .detail-answers {
    padding-bottom: 100px; /* Tambah padding untuk mobile */
    max-height: 60vh; /* Tingkatkan tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }
  
  .answer-user-info {
    gap: 8px; /* Tingkatkan gap untuk mobile */
  }
  
  .answer-avatar {
    width: 32px; /* Tingkatkan ukuran avatar untuk mobile */
    height: 32px;
  }
  
  .answer-user-details {
    gap: 4px; /* Tingkatkan jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 12px; /* Tingkatkan gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 6px; /* Tingkatkan gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1.1rem; /* Tingkatkan ukuran untuk mobile */
    padding: 4px 6px;
  }
  
  .vote-count {
    font-size: 0.9rem; /* Tingkatkan ukuran untuk mobile */
    font-weight: 600;
  }
  
  .response-actions {
    gap: 8px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 1rem;
    padding: 4px;
  }
  
  .edit-response-textarea {
    padding: 12px;
    font-size: 0.95rem;
    min-height: 80px;
  }
  
  .edit-response-actions {
    gap: 8px;
    margin-top: 8px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  /* Additional mobile improvements */
  .question-menu {
    font-size: 1.2rem;
    padding: 6px;
  }
  
  .question-menu-dropdown {
    min-width: 120px;
    right: -10px;
  }
  
  .question-menu-dropdown button {
    padding: 12px 16px;
    font-size: 0.9rem;
  }
  
  /* Improve touch targets */
  .forum-question-card {
    min-height: 80px;
  }
  
  .ask-btn, .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better spacing for mobile */
  .forum-questions-list {
    padding: 0 4px;
  }
  
  .questions-count {
    padding: 0 4px;
    margin-bottom: 16px;
  }
  
  .error-state {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  .success-message {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  /* Additional mobile improvements */
  .forum-hero-content {
    text-align: center;
  }
  
  .forum-hero-content h1 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  /* Improve touch targets for better mobile UX */
  .forum-question-card {
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(106, 76, 155, 0.1);
  }
  
  .forum-question-card:active {
    transform: scale(0.98);
  }
  
  /* Better spacing for mobile */
  .forum-main-content {
    padding-bottom: 40px;
  }
  
  /* Improve mobile navigation */
  .forum-detail-overlay,
  .forum-ask-overlay {
    -webkit-overflow-scrolling: touch;
  }
  
  /* Better mobile form handling */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea {
    -webkit-appearance: none;
    border-radius: 8px;
  }
  
  /* Prevent zoom on input focus on iOS */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea,
  .forum-search-box input {
    font-size: 16px;
  }
}

.answer-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: flex-end; /* Rata kanan semua elemen */
}

.vote-section {
  display: flex;
  align-items: center;
  gap: 6px;
}

.vote-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: all 0.2s;
  color: #b6a7d6;
}

.vote-btn:hover {
  background-color: #f0f0f0;
  color: #6a4c9b;
}

.vote-btn.voted {
  color: #ec744a;
  background-color: #fff5f2;
  transform: scale(1.1);
}

.vote-btn.voted:hover {
  background-color: #ffe6e6;
  color: #d45d2c;
}

.vote-count {
  font-size: 0.9rem;
  font-weight: 600;
  color: #6a4c9b;
  min-width: 20px;
  text-align: center;
}

.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Dark mode styles for forum */
[data-theme="dark"] .forum-sidebar {
  background-color: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 12px var(--shadow-light);
}

[data-theme="dark"] .sidebar-categories h3 {
  color: var(--text-primary);
}

[data-theme="dark"] .sidebar-categories li {
  color: var(--text-secondary);
}

[data-theme="dark"] .sidebar-categories li.active,
[data-theme="dark"] .sidebar-categories li:hover {
  background: var(--bg-tertiary);
  color: var(--accent-purple);
}

[data-theme="dark"] .dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn.active,
[data-theme="dark"] .my-questions-btn:hover {
  background: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .forum-search-row {
  background: var(--bg-tertiary);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input {
  background: var(--input-bg);
  color: var(--text-primary);
  box-shadow: 0 1px 4px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input::placeholder {
  color: var(--text-muted);
}

[data-theme="dark"] .search-icon svg {
  stroke: var(--text-muted);
}

[data-theme="dark"] .forum-featured-links {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-featured-links h4 {
  color: var(--accent-purple);
}

[data-theme="dark"] .forum-featured-links a {
  color: var(--accent-blue);
}

[data-theme="dark"] .forum-featured-links a:hover {
  color: var(--accent-purple);
}

/* Mobile dark mode styles */
[data-theme="dark"] .mobile-filter-row {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .mobile-dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn.active {
  background: var(--accent-purple);
  border-color: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .question-title {
  color: #ffffff;
}

[data-theme="dark"] .question-desc {
  color: #e0e0e0;
}

[data-theme="dark"] .forum-question-card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 12px var(--shadow-light);
}

[data-theme="dark"] .question-user {
  color: #ffffff;
}

[data-theme="dark"] .question-time {
  color: #b0b0b0;
}
@media (max-width: 700px) {
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
}

.slide-ask-enter-active, .slide-ask-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-ask-enter-from, .slide-ask-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-ask-enter-to, .slide-ask-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.forum-ask-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2100;
  display: flex;
  justify-content: flex-end;
}
.forum-ask-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: absolute;
  top: 0;
  right: 0;
  left: auto;
  z-index: 2110;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-ask-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2105;
}
.close-ask {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2120;
}
.ask-form-group {
  margin-bottom: 18px;
  display: flex;
  flex-direction: column;
}
.ask-form-group label {
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 6px;
}
.ask-form-group input, .ask-form-group textarea, .ask-form-group select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
}
.ask-form-group textarea {
  min-height: 70px;
  resize: vertical;
}
.ask-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 700px) {
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
  }
  .forum-ask-panel {
    max-width: 100vw;
    margin-left: 0;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
    border-radius: 0;
  }
  .close-ask {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  .ask-form-group {
    margin-bottom: 16px;
  }
  .ask-form-group label {
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #e5e7eb;
  }
  .ask-form-group textarea {
    min-height: 80px;
    resize: vertical;
  }
  .ask-form-actions {
    gap: 12px;
    margin-top: 20px;
  }
  .cancel-btn, .response-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  /* Detail panel improvements for mobile */
  .forum-detail-panel {
    padding: 20px 16px;
    border-radius: 0;
  }
  
  .close-detail {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  
  .detail-header {
    margin-bottom: 16px;
  }
  
  .detail-title {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .detail-desc {
    font-size: 1rem;
    margin-bottom: 20px;
  }
  
  .detail-responses-box {
    margin: 20px 0;
  }
  
  .detail-responses-box textarea {
    min-height: 100px;
    font-size: 1rem;
  }
  
  .detail-responses-actions {
    margin-top: 12px;
  }
  
  .detail-answers {
    margin-top: 20px;
    padding-bottom: 100px;
  }
}
@media (min-width: 701px) {
  .dropdown-categories {
    display: none !important;
  }
  .sidebar-categories ul {
    display: block !important;
  }
}
@media (min-width: 701px) {
  .forum-search-row {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #faf7f3;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 14px;
    padding-top: 8px;
    padding-bottom: 8px;
  }
  .forum-sidebar {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f8f3fd;
    box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
    border-radius: 18px;
  }
  .forum-featured-links {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f7f5f9;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 18px;
  }
}
.dropdown-filter {
  display: block;
  width: 100%;
  padding: 7px 10px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  margin-bottom: 8px;
  background: #faf7f3;
  color: #6a4c9b;
}
.my-questions-btn {
  width: 100%;
  padding: 8px 0;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f7f5f9;
  color: #6a4c9b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.my-questions-btn.active, .my-questions-btn:hover {
  background: #e5e0f7;
  color: #8b5cf6;
}

/* Loading and Empty States */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  color: #6a4c9b;
}

.loading-spinner {
  border: 4px solid #f3f0f7;
  border-top: 4px solid #6a4c9b;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.7;
}

.empty-state h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #6a4c9b;
}

.empty-state p {
  font-size: 1rem;
  color: #8b7a9b;
  margin-bottom: 20px;
}

.ask-first-btn {
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-first-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
}

/* Mobile Filter Styles */
.mobile-filter-row {
  display: none;
  gap: 12px;
  margin-bottom: 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  font-size: 0.9rem;
  color: #6a4c9b;
  font-weight: 500;
}

.mobile-dropdown-filter:focus {
  outline: none;
  border-color: #6a4c9b;
  box-shadow: 0 0 0 3px rgba(106, 76, 155, 0.1);
}

.mobile-my-questions-btn {
  padding: 12px 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.mobile-my-questions-btn.active {
  background: #e5e0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
  box-shadow: 0 2px 4px rgba(106, 76, 155, 0.2);
}

.mobile-my-questions-btn:hover {
  background: #f3f0f7;
  transform: translateY(-1px);
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 20px;
    margin: 20px 0;
  }
  
  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }
  
  .empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .empty-state p {
    font-size: 1rem;
    margin-bottom: 24px;
    line-height: 1.5;
  }
  
  .ask-first-btn {
    padding: 12px 28px;
    font-size: 1rem;
    border-radius: 8px;
  }
  
  .detail-answer {
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 12px;
  }
  
  .answer-header {
    margin-bottom: 12px;
  }
  
  .answer-user {
    font-size: 0.95rem;
  }
  
  .answer-time {
    font-size: 0.85rem;
  }
  
  .answer-body {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
  }
  
  .responses-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }
}
.loading-responses, .no-responses {
  text-align: center;
  padding: 20px;
  color: #888;
  font-style: italic;
}
.loading-responses p, .no-responses p {
  margin: 0;
}

.responses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.responses-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin: 0;
}

.refresh-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #f0f0f0;
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Response Actions */
.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.edit-response-btn,
.delete-response-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-response-btn:hover {
  background-color: #f0f0f0;
}

.delete-response-btn:hover {
  background-color: #ffe6e6;
}

/* Edit Response Form */
.edit-response-form {
  margin-top: 8px;
}

.edit-response-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  background: #faf7f3;
  color: #444;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.edit-response-textarea:focus {
  border-color: #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.edit-response-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.cancel-edit-btn,
.save-edit-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-edit-btn {
  background: #e5e7eb;
  color: #6a4c9b;
}

.cancel-edit-btn:hover {
  background: #d1d5db;
}

.save-edit-btn {
  background: #ec744a;
  color: white;
}

.save-edit-btn:hover {
  background: #d45d2c;
}

/* Mobile Responsive */
@media (max-width: 700px) {
  .detail-answers {
    padding-bottom: 100px; /* Tambah padding untuk mobile */
    max-height: 60vh; /* Tingkatkan tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }
  
  .answer-user-info {
    gap: 8px; /* Tingkatkan gap untuk mobile */
  }
  
  .answer-avatar {
    width: 32px; /* Tingkatkan ukuran avatar untuk mobile */
    height: 32px;
  }
  
  .answer-user-details {
    gap: 4px; /* Tingkatkan jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 12px; /* Tingkatkan gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 6px; /* Tingkatkan gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1.1rem; /* Tingkatkan ukuran untuk mobile */
    padding: 4px 6px;
  }
  
  .vote-count {
    font-size: 0.9rem; /* Tingkatkan ukuran untuk mobile */
    font-weight: 600;
  }
  
  .response-actions {
    gap: 8px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 1rem;
    padding: 4px;
  }
  
  .edit-response-textarea {
    padding: 12px;
    font-size: 0.95rem;
    min-height: 80px;
  }
  
  .edit-response-actions {
    gap: 8px;
    margin-top: 8px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  /* Additional mobile improvements */
  .question-menu {
    font-size: 1.2rem;
    padding: 6px;
  }
  
  .question-menu-dropdown {
    min-width: 120px;
    right: -10px;
  }
  
  .question-menu-dropdown button {
    padding: 12px 16px;
    font-size: 0.9rem;
  }
  
  /* Improve touch targets */
  .forum-question-card {
    min-height: 80px;
  }
  
  .ask-btn, .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better spacing for mobile */
  .forum-questions-list {
    padding: 0 4px;
  }
  
  .questions-count {
    padding: 0 4px;
    margin-bottom: 16px;
  }
  
  .error-state {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  .success-message {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  /* Additional mobile improvements */
  .forum-hero-content {
    text-align: center;
  }
  
  .forum-hero-content h1 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  /* Improve touch targets for better mobile UX */
  .forum-question-card {
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(106, 76, 155, 0.1);
  }
  
  .forum-question-card:active {
    transform: scale(0.98);
  }
  
  /* Better spacing for mobile */
  .forum-main-content {
    padding-bottom: 40px;
  }
  
  /* Improve mobile navigation */
  .forum-detail-overlay,
  .forum-ask-overlay {
    -webkit-overflow-scrolling: touch;
  }
  
  /* Better mobile form handling */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea {
    -webkit-appearance: none;
    border-radius: 8px;
  }
  
  /* Prevent zoom on input focus on iOS */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea,
  .forum-search-box input {
    font-size: 16px;
  }
}

.answer-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: flex-end; /* Rata kanan semua elemen */
}

.vote-section {
  display: flex;
  align-items: center;
  gap: 6px;
}

.vote-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: all 0.2s;
  color: #b6a7d6;
}

.vote-btn:hover {
  background-color: #f0f0f0;
  color: #6a4c9b;
}

.vote-btn.voted {
  color: #ec744a;
  background-color: #fff5f2;
  transform: scale(1.1);
}

.vote-btn.voted:hover {
  background-color: #ffe6e6;
  color: #d45d2c;
}

.vote-count {
  font-size: 0.9rem;
  font-weight: 600;
  color: #6a4c9b;
  min-width: 20px;
  text-align: center;
}

.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Dark mode styles for forum */
[data-theme="dark"] .forum-sidebar {
  background-color: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 12px var(--shadow-light);
}

[data-theme="dark"] .sidebar-categories h3 {
  color: var(--text-primary);
}

[data-theme="dark"] .sidebar-categories li {
  color: var(--text-secondary);
}

[data-theme="dark"] .sidebar-categories li.active,
[data-theme="dark"] .sidebar-categories li:hover {
  background: var(--bg-tertiary);
  color: var(--accent-purple);
}

[data-theme="dark"] .dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn.active,
[data-theme="dark"] .my-questions-btn:hover {
  background: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .forum-search-row {
  background: var(--bg-tertiary);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input {
  background: var(--input-bg);
  color: var(--text-primary);
  box-shadow: 0 1px 4px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input::placeholder {
  color: var(--text-muted);
}

[data-theme="dark"] .search-icon svg {
  stroke: var(--text-muted);
}

[data-theme="dark"] .forum-featured-links {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-featured-links h4 {
  color: var(--accent-purple);
}

[data-theme="dark"] .forum-featured-links a {
  color: var(--accent-blue);
}

[data-theme="dark"] .forum-featured-links a:hover {
  color: var(--accent-purple);
}

/* Mobile dark mode styles */
[data-theme="dark"] .mobile-filter-row {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .mobile-dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn.active {
  background: var(--accent-purple);
  border-color: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .mobile-my-questions-btn:hover {
  background: var(--bg-secondary);
}

@media (max-width: 700px) {
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
}

.slide-ask-enter-active, .slide-ask-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-ask-enter-from, .slide-ask-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-ask-enter-to, .slide-ask-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.forum-ask-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2100;
  display: flex;
  justify-content: flex-end;
}
.forum-ask-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: absolute;
  top: 0;
  right: 0;
  left: auto;
  z-index: 2110;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-ask-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2105;
}
.close-ask {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2120;
}
.ask-form-group {
  margin-bottom: 18px;
  display: flex;
  flex-direction: column;
}
.ask-form-group label {
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 6px;
}
.ask-form-group input, .ask-form-group textarea, .ask-form-group select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
}
.ask-form-group textarea {
  min-height: 70px;
  resize: vertical;
}
.ask-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 700px) {
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
  }
  .forum-ask-panel {
    max-width: 100vw;
    margin-left: 0;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
    border-radius: 0;
  }
  .close-ask {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  .ask-form-group {
    margin-bottom: 16px;
  }
  .ask-form-group label {
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #e5e7eb;
  }
  .ask-form-group textarea {
    min-height: 80px;
    resize: vertical;
  }
  .ask-form-actions {
    gap: 12px;
    margin-top: 20px;
  }
  .cancel-btn, .response-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  /* Detail panel improvements for mobile */
  .forum-detail-panel {
    padding: 20px 16px;
    border-radius: 0;
  }
  
  .close-detail {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  
  .detail-header {
    margin-bottom: 16px;
  }
  
  .detail-title {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .detail-desc {
    font-size: 1rem;
    margin-bottom: 20px;
  }
  
  .detail-responses-box {
    margin: 20px 0;
  }
  
  .detail-responses-box textarea {
    min-height: 100px;
    font-size: 1rem;
  }
  
  .detail-responses-actions {
    margin-top: 12px;
  }
  
  .detail-answers {
    margin-top: 20px;
    padding-bottom: 100px;
  }
}
@media (min-width: 701px) {
  .dropdown-categories {
    display: none !important;
  }
  .sidebar-categories ul {
    display: block !important;
  }
}
@media (min-width: 701px) {
  .forum-search-row {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #faf7f3;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 14px;
    padding-top: 8px;
    padding-bottom: 8px;
  }
  .forum-sidebar {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f8f3fd;
    box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
    border-radius: 18px;
  }
  .forum-featured-links {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f7f5f9;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 18px;
  }
}
.dropdown-filter {
  display: block;
  width: 100%;
  padding: 7px 10px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  margin-bottom: 8px;
  background: #faf7f3;
  color: #6a4c9b;
}
.my-questions-btn {
  width: 100%;
  padding: 8px 0;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f7f5f9;
  color: #6a4c9b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.my-questions-btn.active, .my-questions-btn:hover {
  background: #e5e0f7;
  color: #8b5cf6;
}

/* Loading and Empty States */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  color: #6a4c9b;
}

.loading-spinner {
  border: 4px solid #f3f0f7;
  border-top: 4px solid #6a4c9b;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.7;
}

.empty-state h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #6a4c9b;
}

.empty-state p {
  font-size: 1rem;
  color: #8b7a9b;
  margin-bottom: 20px;
}

.ask-first-btn {
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-first-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
}

/* Mobile Filter Styles */
.mobile-filter-row {
  display: none;
  gap: 12px;
  margin-bottom: 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  font-size: 0.9rem;
  color: #6a4c9b;
  font-weight: 500;
}

.mobile-dropdown-filter:focus {
  outline: none;
  border-color: #6a4c9b;
  box-shadow: 0 0 0 3px rgba(106, 76, 155, 0.1);
}

.mobile-my-questions-btn {
  padding: 12px 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.mobile-my-questions-btn.active {
  background: #e5e0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
  box-shadow: 0 2px 4px rgba(106, 76, 155, 0.2);
}

.mobile-my-questions-btn:hover {
  background: #f3f0f7;
  transform: translateY(-1px);
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 20px;
    margin: 20px 0;
  }
  
  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }
  
  .empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .empty-state p {
    font-size: 1rem;
    margin-bottom: 24px;
    line-height: 1.5;
  }
  
  .ask-first-btn {
    padding: 12px 28px;
    font-size: 1rem;
    border-radius: 8px;
  }
  
  .detail-answer {
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 12px;
  }
  
  .answer-header {
    margin-bottom: 12px;
  }
  
  .answer-user {
    font-size: 0.95rem;
  }
  
  .answer-time {
    font-size: 0.85rem;
  }
  
  .answer-body {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
  }
  
  .responses-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }
}
.loading-responses, .no-responses {
  text-align: center;
  padding: 20px;
  color: #888;
  font-style: italic;
}
.loading-responses p, .no-responses p {
  margin: 0;
}

.responses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.responses-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin: 0;
}

.refresh-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #f0f0f0;
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Response Actions */
.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.edit-response-btn,
.delete-response-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-response-btn:hover {
  background-color: #f0f0f0;
}

.delete-response-btn:hover {
  background-color: #ffe6e6;
}

/* Edit Response Form */
.edit-response-form {
  margin-top: 8px;
}

.edit-response-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  background: #faf7f3;
  color: #444;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.edit-response-textarea:focus {
  border-color: #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.edit-response-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.cancel-edit-btn,
.save-edit-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-edit-btn {
  background: #e5e7eb;
  color: #6a4c9b;
}

.cancel-edit-btn:hover {
  background: #d1d5db;
}

.save-edit-btn {
  background: #ec744a;
  color: white;
}

.save-edit-btn:hover {
  background: #d45d2c;
}

/* Mobile Responsive */
@media (max-width: 700px) {
  .detail-answers {
    padding-bottom: 100px; /* Tambah padding untuk mobile */
    max-height: 60vh; /* Tingkatkan tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }
  
  .answer-user-info {
    gap: 8px; /* Tingkatkan gap untuk mobile */
  }
  
  .answer-avatar {
    width: 32px; /* Tingkatkan ukuran avatar untuk mobile */
    height: 32px;
  }
  
  .answer-user-details {
    gap: 4px; /* Tingkatkan jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 12px; /* Tingkatkan gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 6px; /* Tingkatkan gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1.1rem; /* Tingkatkan ukuran untuk mobile */
    padding: 4px 6px;
  }
  
  .vote-count {
    font-size: 0.9rem; /* Tingkatkan ukuran untuk mobile */
    font-weight: 600;
  }
  
  .response-actions {
    gap: 8px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 1rem;
    padding: 4px;
  }
  
  .edit-response-textarea {
    padding: 12px;
    font-size: 0.95rem;
    min-height: 80px;
  }
  
  .edit-response-actions {
    gap: 8px;
    margin-top: 8px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  /* Additional mobile improvements */
  .question-menu {
    font-size: 1.2rem;
    padding: 6px;
  }
  
  .question-menu-dropdown {
    min-width: 120px;
    right: -10px;
  }
  
  .question-menu-dropdown button {
    padding: 12px 16px;
    font-size: 0.9rem;
  }
  
  /* Improve touch targets */
  .forum-question-card {
    min-height: 80px;
  }
  
  .ask-btn, .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better spacing for mobile */
  .forum-questions-list {
    padding: 0 4px;
  }
  
  .questions-count {
    padding: 0 4px;
    margin-bottom: 16px;
  }
  
  .error-state {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  .success-message {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  /* Additional mobile improvements */
  .forum-hero-content {
    text-align: center;
  }
  
  .forum-hero-content h1 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  /* Improve touch targets for better mobile UX */
  .forum-question-card {
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(106, 76, 155, 0.1);
  }
  
  .forum-question-card:active {
    transform: scale(0.98);
  }
  
  /* Better spacing for mobile */
  .forum-main-content {
    padding-bottom: 40px;
  }
  
  /* Improve mobile navigation */
  .forum-detail-overlay,
  .forum-ask-overlay {
    -webkit-overflow-scrolling: touch;
  }
  
  /* Better mobile form handling */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea {
    -webkit-appearance: none;
    border-radius: 8px;
  }
  
  /* Prevent zoom on input focus on iOS */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea,
  .forum-search-box input {
    font-size: 16px;
  }
}

.answer-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: flex-end; /* Rata kanan semua elemen */
}

.vote-section {
  display: flex;
  align-items: center;
  gap: 6px;
}

.vote-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: all 0.2s;
  color: #b6a7d6;
}

.vote-btn:hover {
  background-color: #f0f0f0;
  color: #6a4c9b;
}

.vote-btn.voted {
  color: #ec744a;
  background-color: #fff5f2;
  transform: scale(1.1);
}

.vote-btn.voted:hover {
  background-color: #ffe6e6;
  color: #d45d2c;
}

.vote-count {
  font-size: 0.9rem;
  font-weight: 600;
  color: #6a4c9b;
  min-width: 20px;
  text-align: center;
}

.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Dark mode for loading and empty states */
[data-theme="dark"] .loading-state,
[data-theme="dark"] .empty-state {
  color: var(--text-primary);
}

[data-theme="dark"] .loading-spinner {
  border-color: var(--border-color);
  border-top-color: var(--accent-purple);
}

[data-theme="dark"] .empty-state h3 {
  color: var(--accent-purple);
}

[data-theme="dark"] .empty-state p {
  color: var(--text-secondary);
}

@media (max-width: 700px) {
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
}

.slide-ask-enter-active, .slide-ask-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-ask-enter-from, .slide-ask-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-ask-enter-to, .slide-ask-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.forum-ask-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2100;
  display: flex;
  justify-content: flex-end;
}
.forum-ask-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: absolute;
  top: 0;
  right: 0;
  left: auto;
  z-index: 2110;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-ask-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2105;
}
.close-ask {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2120;
}
.ask-form-group {
  margin-bottom: 18px;
  display: flex;
  flex-direction: column;
}
.ask-form-group label {
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 6px;
}
.ask-form-group input, .ask-form-group textarea, .ask-form-group select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
}
.ask-form-group textarea {
  min-height: 70px;
  resize: vertical;
}
.ask-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 700px) {
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
  }
  .forum-ask-panel {
    max-width: 100vw;
    margin-left: 0;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
    border-radius: 0;
  }
  .close-ask {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  .ask-form-group {
    margin-bottom: 16px;
  }
  .ask-form-group label {
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #e5e7eb;
  }
  .ask-form-group textarea {
    min-height: 80px;
    resize: vertical;
  }
  .ask-form-actions {
    gap: 12px;
    margin-top: 20px;
  }
  .cancel-btn, .response-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  /* Detail panel improvements for mobile */
  .forum-detail-panel {
    padding: 20px 16px;
    border-radius: 0;
  }
  
  .close-detail {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  
  .detail-header {
    margin-bottom: 16px;
  }
  
  .detail-title {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .detail-desc {
    font-size: 1rem;
    margin-bottom: 20px;
  }
  
  .detail-responses-box {
    margin: 20px 0;
  }
  
  .detail-responses-box textarea {
    min-height: 100px;
    font-size: 1rem;
  }
  
  .detail-responses-actions {
    margin-top: 12px;
  }
  
  .detail-answers {
    margin-top: 20px;
    padding-bottom: 100px;
  }
}
@media (min-width: 701px) {
  .dropdown-categories {
    display: none !important;
  }
  .sidebar-categories ul {
    display: block !important;
  }
}
@media (min-width: 701px) {
  .forum-search-row {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #faf7f3;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 14px;
    padding-top: 8px;
    padding-bottom: 8px;
  }
  .forum-sidebar {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f8f3fd;
    box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
    border-radius: 18px;
  }
  .forum-featured-links {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f7f5f9;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 18px;
  }
}
.dropdown-filter {
  display: block;
  width: 100%;
  padding: 7px 10px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  margin-bottom: 8px;
  background: #faf7f3;
  color: #6a4c9b;
}
.my-questions-btn {
  width: 100%;
  padding: 8px 0;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f7f5f9;
  color: #6a4c9b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.my-questions-btn.active, .my-questions-btn:hover {
  background: #e5e0f7;
  color: #8b5cf6;
}

/* Loading and Empty States */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  color: #6a4c9b;
}

.loading-spinner {
  border: 4px solid #f3f0f7;
  border-top: 4px solid #6a4c9b;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.7;
}

.empty-state h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #6a4c9b;
}

.empty-state p {
  font-size: 1rem;
  color: #8b7a9b;
  margin-bottom: 20px;
}

.ask-first-btn {
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-first-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
}

/* Mobile Filter Styles */
.mobile-filter-row {
  display: none;
  gap: 12px;
  margin-bottom: 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  font-size: 0.9rem;
  color: #6a4c9b;
  font-weight: 500;
}

.mobile-dropdown-filter:focus {
  outline: none;
  border-color: #6a4c9b;
  box-shadow: 0 0 0 3px rgba(106, 76, 155, 0.1);
}

.mobile-my-questions-btn {
  padding: 12px 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.mobile-my-questions-btn.active {
  background: #e5e0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
  box-shadow: 0 2px 4px rgba(106, 76, 155, 0.2);
}

.mobile-my-questions-btn:hover {
  background: #f3f0f7;
  transform: translateY(-1px);
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 20px;
    margin: 20px 0;
  }
  
  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }
  
  .empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .empty-state p {
    font-size: 1rem;
    margin-bottom: 24px;
    line-height: 1.5;
  }
  
  .ask-first-btn {
    padding: 12px 28px;
    font-size: 1rem;
    border-radius: 8px;
  }
  
  .detail-answer {
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 12px;
  }
  
  .answer-header {
    margin-bottom: 12px;
  }
  
  .answer-user {
    font-size: 0.95rem;
  }
  
  .answer-time {
    font-size: 0.85rem;
  }
  
  .answer-body {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
  }
  
  .responses-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }
}
.loading-responses, .no-responses {
  text-align: center;
  padding: 20px;
  color: #888;
  font-style: italic;
}
.loading-responses p, .no-responses p {
  margin: 0;
}

.responses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.responses-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin: 0;
}

.refresh-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #f0f0f0;
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Response Actions */
.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.edit-response-btn,
.delete-response-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-response-btn:hover {
  background-color: #f0f0f0;
}

.delete-response-btn:hover {
  background-color: #ffe6e6;
}

/* Edit Response Form */
.edit-response-form {
  margin-top: 8px;
}

.edit-response-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  background: #faf7f3;
  color: #444;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.edit-response-textarea:focus {
  border-color: #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.edit-response-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.cancel-edit-btn,
.save-edit-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-edit-btn {
  background: #e5e7eb;
  color: #6a4c9b;
}

.cancel-edit-btn:hover {
  background: #d1d5db;
}

.save-edit-btn {
  background: #ec744a;
  color: white;
}

.save-edit-btn:hover {
  background: #d45d2c;
}

/* Mobile Responsive */
@media (max-width: 700px) {
  .detail-answers {
    padding-bottom: 100px; /* Tambah padding untuk mobile */
    max-height: 60vh; /* Tingkatkan tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }
  
  .answer-user-info {
    gap: 8px; /* Tingkatkan gap untuk mobile */
  }
  
  .answer-avatar {
    width: 32px; /* Tingkatkan ukuran avatar untuk mobile */
    height: 32px;
  }
  
  .answer-user-details {
    gap: 4px; /* Tingkatkan jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 12px; /* Tingkatkan gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 6px; /* Tingkatkan gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1.1rem; /* Tingkatkan ukuran untuk mobile */
    padding: 4px 6px;
  }
  
  .vote-count {
    font-size: 0.9rem; /* Tingkatkan ukuran untuk mobile */
    font-weight: 600;
  }
  
  .response-actions {
    gap: 8px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 1rem;
    padding: 4px;
  }
  
  .edit-response-textarea {
    padding: 12px;
    font-size: 0.95rem;
    min-height: 80px;
  }
  
  .edit-response-actions {
    gap: 8px;
    margin-top: 8px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  /* Additional mobile improvements */
  .question-menu {
    font-size: 1.2rem;
    padding: 6px;
  }
  
  .question-menu-dropdown {
    min-width: 120px;
    right: -10px;
  }
  
  .question-menu-dropdown button {
    padding: 12px 16px;
    font-size: 0.9rem;
  }
  
  /* Improve touch targets */
  .forum-question-card {
    min-height: 80px;
  }
  
  .ask-btn, .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better spacing for mobile */
  .forum-questions-list {
    padding: 0 4px;
  }
  
  .questions-count {
    padding: 0 4px;
    margin-bottom: 16px;
  }
  
  .error-state {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  .success-message {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  /* Additional mobile improvements */
  .forum-hero-content {
    text-align: center;
  }
  
  .forum-hero-content h1 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  /* Improve touch targets for better mobile UX */
  .forum-question-card {
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(106, 76, 155, 0.1);
  }
  
  .forum-question-card:active {
    transform: scale(0.98);
  }
  
  /* Better spacing for mobile */
  .forum-main-content {
    padding-bottom: 40px;
  }
  
  /* Improve mobile navigation */
  .forum-detail-overlay,
  .forum-ask-overlay {
    -webkit-overflow-scrolling: touch;
  }
  
  /* Better mobile form handling */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea {
    -webkit-appearance: none;
    border-radius: 8px;
  }
  
  /* Prevent zoom on input focus on iOS */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea,
  .forum-search-box input {
    font-size: 16px;
  }
}

.answer-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: flex-end; /* Rata kanan semua elemen */
}

.vote-section {
  display: flex;
  align-items: center;
  gap: 6px;
}

.vote-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: all 0.2s;
  color: #b6a7d6;
}

.vote-btn:hover {
  background-color: #f0f0f0;
  color: #6a4c9b;
}

.vote-btn.voted {
  color: #ec744a;
  background-color: #fff5f2;
  transform: scale(1.1);
}

.vote-btn.voted:hover {
  background-color: #ffe6e6;
  color: #d45d2c;
}

.vote-count {
  font-size: 0.9rem;
  font-weight: 600;
  color: #6a4c9b;
  min-width: 20px;
  text-align: center;
}

.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Dark mode styles for forum */
[data-theme="dark"] .forum-sidebar {
  background-color: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 12px var(--shadow-light);
}

[data-theme="dark"] .sidebar-categories h3 {
  color: var(--text-primary);
}

[data-theme="dark"] .sidebar-categories li {
  color: var(--text-secondary);
}

[data-theme="dark"] .sidebar-categories li.active,
[data-theme="dark"] .sidebar-categories li:hover {
  background: var(--bg-tertiary);
  color: var(--accent-purple);
}

[data-theme="dark"] .dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .my-questions-btn.active,
[data-theme="dark"] .my-questions-btn:hover {
  background: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .forum-search-row {
  background: var(--bg-tertiary);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input {
  background: var(--input-bg);
  color: var(--text-primary);
  box-shadow: 0 1px 4px var(--shadow-light);
}

[data-theme="dark"] .forum-search-box input::placeholder {
  color: var(--text-muted);
}

[data-theme="dark"] .search-icon svg {
  stroke: var(--text-muted);
}

[data-theme="dark"] .forum-featured-links {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .forum-featured-links h4 {
  color: var(--accent-purple);
}

[data-theme="dark"] .forum-featured-links a {
  color: var(--accent-blue);
}

[data-theme="dark"] .forum-featured-links a:hover {
  color: var(--accent-purple);
}

/* Mobile dark mode styles */
[data-theme="dark"] .mobile-filter-row {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

[data-theme="dark"] .mobile-dropdown-filter {
  background: var(--input-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

[data-theme="dark"] .mobile-my-questions-btn.active {
  background: var(--accent-purple);
  border-color: var(--accent-purple);
  color: white;
}

[data-theme="dark"] .mobile-my-questions-btn:hover {
  background: var(--bg-secondary);
}

@media (max-width: 700px) {
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
}

.slide-ask-enter-active, .slide-ask-leave-active {
  transition: all 0.35s cubic-bezier(.77,0,.18,1);
}
.slide-ask-enter-from, .slide-ask-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-ask-enter-to, .slide-ask-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.forum-ask-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2100;
  display: flex;
  justify-content: flex-end;
}
.forum-ask-panel {
  background: #fff;
  width: 100%;
  max-width: 600px;
  height: 100vh;
  box-shadow: -4px 0 32px 0 rgba(80,60,120,0.13);
  padding: 32px 32px 24px 32px;
  overflow-y: auto;
  position: absolute;
  top: 0;
  right: 0;
  left: auto;
  z-index: 2110;
  border-top-left-radius: 18px;
  border-bottom-left-radius: 18px;
  display: flex;
  flex-direction: column;
}
.forum-ask-backdrop {
  flex: 1;
  background: rgba(40,30,60,0.18);
  cursor: pointer;
  z-index: 2105;
}
.close-ask {
  position: absolute;
  top: 18px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #b6a7d6;
  cursor: pointer;
  z-index: 2120;
}
.ask-form-group {
  margin-bottom: 18px;
  display: flex;
  flex-direction: column;
}
.ask-form-group label {
  font-weight: 600;
  color: #6a4c9b;
  margin-bottom: 6px;
}
.ask-form-group input, .ask-form-group textarea, .ask-form-group select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 1rem;
  color: #6a4c9b;
  outline: none;
  background: #faf7f3;
}
.ask-form-group textarea {
  min-height: 70px;
  resize: vertical;
}
.ask-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 700px) {
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
  }
  .forum-ask-panel {
    max-width: 100vw;
    margin-left: 0;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
    border-radius: 0;
  }
  .close-ask {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  .ask-form-group {
    margin-bottom: 16px;
  }
  .ask-form-group label {
    font-size: 0.9rem;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    border: 1px solid #e5e7eb;
  }
  .ask-form-group textarea {
    min-height: 80px;
    resize: vertical;
  }
  .ask-form-actions {
    gap: 12px;
    margin-top: 20px;
  }
  .cancel-btn, .response-btn {
    padding: 12px 20px;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
  }
  
  /* Detail panel improvements for mobile */
  .forum-detail-panel {
    padding: 20px 16px;
    border-radius: 0;
  }
  
  .close-detail {
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
  }
  
  .detail-header {
    margin-bottom: 16px;
  }
  
  .detail-title {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .detail-desc {
    font-size: 1rem;
    margin-bottom: 20px;
  }
  
  .detail-responses-box {
    margin: 20px 0;
  }
  
  .detail-responses-box textarea {
    min-height: 100px;
    font-size: 1rem;
  }
  
  .detail-responses-actions {
    margin-top: 12px;
  }
  
  .detail-answers {
    margin-top: 20px;
    padding-bottom: 100px;
  }
}
@media (min-width: 701px) {
  .dropdown-categories {
    display: none !important;
  }
  .sidebar-categories ul {
    display: block !important;
  }
}
@media (min-width: 701px) {
  .forum-search-row {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #faf7f3;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 14px;
    padding-top: 8px;
    padding-bottom: 8px;
  }
  .forum-sidebar {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f8f3fd;
    box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
    border-radius: 18px;
  }
  .forum-featured-links {
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #f7f5f9;
    box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
    border-radius: 18px;
  }
}
.dropdown-filter {
  display: block;
  width: 100%;
  padding: 7px 10px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  margin-bottom: 8px;
  background: #faf7f3;
  color: #6a4c9b;
}
.my-questions-btn {
  width: 100%;
  padding: 8px 0;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f7f5f9;
  color: #6a4c9b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.my-questions-btn.active, .my-questions-btn:hover {
  background: #e5e0f7;
  color: #8b5cf6;
}

/* Loading and Empty States */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  color: #6a4c9b;
}

.loading-spinner {
  border: 4px solid #f3f0f7;
  border-top: 4px solid #6a4c9b;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
  opacity: 0.7;
}

.empty-state h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #6a4c9b;
}

.empty-state p {
  font-size: 1rem;
  color: #8b7a9b;
  margin-bottom: 20px;
}

.ask-first-btn {
  background: #ec744a;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(236, 116, 74, 0.08);
}

.ask-first-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
}

/* Mobile Filter Styles */
.mobile-filter-row {
  display: none;
  gap: 12px;
  margin-bottom: 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  font-size: 0.9rem;
  color: #6a4c9b;
  font-weight: 500;
}

.mobile-dropdown-filter:focus {
  outline: none;
  border-color: #6a4c9b;
  box-shadow: 0 0 0 3px rgba(106, 76, 155, 0.1);
}

.mobile-my-questions-btn {
  padding: 12px 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #faf7f3;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.mobile-my-questions-btn.active {
  background: #e5e0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
  box-shadow: 0 2px 4px rgba(106, 76, 155, 0.2);
}

.mobile-my-questions-btn:hover {
  background: #f3f0f7;
  transform: translateY(-1px);
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 20px;
    margin: 20px 0;
  }
  
  .empty-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }
  
  .empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
  }
  
  .empty-state p {
    font-size: 1rem;
    margin-bottom: 24px;
    line-height: 1.5;
  }
  
  .ask-first-btn {
    padding: 12px 28px;
    font-size: 1rem;
    border-radius: 8px;
  }
  
  .detail-answer {
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 12px;
  }
  
  .answer-header {
    margin-bottom: 12px;
  }
  
  .answer-user {
    font-size: 0.95rem;
  }
  
  .answer-time {
    font-size: 0.85rem;
  }
  
  .answer-body {
    font-size: 0.95rem;
    line-height: 1.5;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
  }
  
  .responses-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }
}
.loading-responses, .no-responses {
  text-align: center;
  padding: 20px;
  color: #888;
  font-style: italic;
}
.loading-responses p, .no-responses p {
  margin: 0;
}

.responses-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.responses-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6a4c9b;
  margin: 0;
}

.refresh-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #f0f0f0;
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Response Actions */
.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.edit-response-btn,
.delete-response-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-response-btn:hover {
  background-color: #f0f0f0;
}

.delete-response-btn:hover {
  background-color: #ffe6e6;
}

/* Edit Response Form */
.edit-response-form {
  margin-top: 8px;
}

.edit-response-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  background: #faf7f3;
  color: #444;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.edit-response-textarea:focus {
  border-color: #8b5cf6;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
}

.edit-response-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 8px;
}

.cancel-edit-btn,
.save-edit-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-edit-btn {
  background: #e5e7eb;
  color: #6a4c9b;
}

.cancel-edit-btn:hover {
  background: #d1d5db;
}

.save-edit-btn {
  background: #ec744a;
  color: white;
}

.save-edit-btn:hover {
  background: #d45d2c;
}

/* Mobile Responsive */
@media (max-width: 700px) {
  .detail-answers {
    padding-bottom: 100px; /* Tambah padding untuk mobile */
    max-height: 60vh; /* Tingkatkan tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 16px;
    padding-bottom: 12px;
  }
  
  .responses-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }
  
  .answer-user-info {
    gap: 8px; /* Tingkatkan gap untuk mobile */
  }
  
  .answer-avatar {
    width: 32px; /* Tingkatkan ukuran avatar untuk mobile */
    height: 32px;
  }
  
  .answer-user-details {
    gap: 4px; /* Tingkatkan jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 12px; /* Tingkatkan gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 6px; /* Tingkatkan gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1.1rem; /* Tingkatkan ukuran untuk mobile */
    padding: 4px 6px;
  }
  
  .vote-count {
    font-size: 0.9rem; /* Tingkatkan ukuran untuk mobile */
    font-weight: 600;
  }
  
  .response-actions {
    gap: 8px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 1rem;
    padding: 4px;
  }
  
  .edit-response-textarea {
    padding: 12px;
    font-size: 0.95rem;
    min-height: 80px;
  }
  
  .edit-response-actions {
    gap: 8px;
    margin-top: 8px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
  }
  
  /* Additional mobile improvements */
  .question-menu {
    font-size: 1.2rem;
    padding: 6px;
  }
  
  .question-menu-dropdown {
    min-width: 120px;
    right: -10px;
  }
  
  .question-menu-dropdown button {
    padding: 12px 16px;
    font-size: 0.9rem;
  }
  
  /* Improve touch targets */
  .forum-question-card {
    min-height: 80px;
  }
  
  .ask-btn, .refresh-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better spacing for mobile */
  .forum-questions-list {
    padding: 0 4px;
  }
  
  .questions-count {
    padding: 0 4px;
    margin-bottom: 16px;
  }
  
  .error-state {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  .success-message {
    margin: 0 4px 16px 4px;
    padding: 16px;
    border-radius: 8px;
  }
  
  /* Additional mobile improvements */
  .forum-hero-content {
    text-align: center;
  }
  
  .forum-hero-content h1 {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  /* Improve touch targets for better mobile UX */
  .forum-question-card {
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(106, 76, 155, 0.1);
  }
  
  .forum-question-card:active {
    transform: scale(0.98);
  }
  
  /* Better spacing for mobile */
  .forum-main-content {
    padding-bottom: 40px;
  }
  
  /* Improve mobile navigation */
  .forum-detail-overlay,
  .forum-ask-overlay {
    -webkit-overflow-scrolling: touch;
  }
  
  /* Better mobile form handling */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea {
    -webkit-appearance: none;
    border-radius: 8px;
  }
  
  /* Prevent zoom on input focus on iOS */
  .detail-responses-box textarea,
  .ask-form-group input,
  .ask-form-group textarea,
  .forum-search-box input {
    font-size: 16px;
  }
}

.answer-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: flex-end; /* Rata kanan semua elemen */
}

.vote-section {
  display: flex;
  align-items: center;
  gap: 6px;
}

.vote-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: all 0.2s;
  color: #b6a7d6;
}

.vote-btn:hover {
  background-color: #f0f0f0;
  color: #6a4c9b;
}

.vote-btn.voted {
  color: #ec744a;
  background-color: #fff5f2;
  transform: scale(1.1);
}

.vote-btn.voted:hover {
  background-color: #ffe6e6;
  color: #d45d2c;
}

.vote-count {
  font-size: 0.9rem;
  font-weight: 600;
  color: #6a4c9b;
  min-width: 20px;
  text-align: center;
}

.response-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}
</style>
