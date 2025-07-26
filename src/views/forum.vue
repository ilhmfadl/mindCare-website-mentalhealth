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
          <li><a href="#">Tanyakan kepada orang yang memiliki kendala yang sama dengan anda!</a></li>
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
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import ForumModal from '../components/ForumModal.vue';
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
  
  // Parse tanggal dari database (format: YYYY-MM-DD HH:mm:ss)
  const date = new Date(dateString);
  const now = new Date();
  
  // Cek apakah hari ini
  const isToday = date.toDateString() === now.toDateString();
  const isYesterday = new Date(now.getTime() - 24 * 60 * 60 * 1000).toDateString() === date.toDateString();
  
  // Jika hari ini
  if (isToday) {
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    if (diffInMinutes < 1) return 'Baru saja';
    if (diffInMinutes < 60) return `${diffInMinutes} menit yang lalu`;
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} jam yang lalu`;
  }
  
  // Jika kemarin
  if (isYesterday) {
    return 'Kemarin';
  }
  
  // Jika lebih dari kemarin, tampilkan tanggal
  const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
  if (diffInDays < 7) {
    return `${diffInDays} hari yang lalu`;
  }
  
  // Jika lebih dari seminggu, tampilkan tanggal lengkap
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
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
          
          const localTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
          formData.append('current_time', localTime);
          
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

</script>

<style scoped>
.forum-page {
  width: 100%;
  min-height: 100vh;
  background: #fcf9f6;
}

.forum-hero {
  background: linear-gradient(45deg, #725c96 30%, #c09df7 100%);
  padding: 100px 0 60px 0;
  color: #ffffff;
  position: relative;
}

.forum-hero::before{
    content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="p" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><path d="M50 0V100M0 50H100" stroke="%23B19CDA" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="none"/><rect width="100%" height="100%" fill="url(%23p)"/></svg>');
  opacity: 0.1;
  z-index: 1;
}

.forum-hero-content {
  max-width: 1200px;
  margin: 0 auto;
  color: #fff;
  padding-left: 40px;
}
.forum-hero-content h1 {
  font-size: 3.2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  letter-spacing: -1px;
}
.forum-hero-content p {
  font-size: 1.25rem;
  opacity: 0.93;
  margin-top: 0.2rem;
}

.forum-main-content {
  display: flex;
  max-width: 1200px;
  margin: -60px auto 0 auto;
  padding: 0 40px 40px 40px;
  gap: 32px;
  position: relative;
  z-index: 2;
}

.forum-sidebar {
  flex: 0 0 220px;
  background: #f8f3fd;
  border-radius: 18px;
  padding: 32px 18px 24px 18px;
  box-shadow: 0 2px 8px rgba(19, 18, 24, 0.178);
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 180px;
  margin-top: 80px;
  height: fit-content;
}
.sidebar-profile {
  margin-bottom: 18px;
}

.sidebar-categories h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 10px;
  color: #6a4c9b;
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
  color: #6a4c9b;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.sidebar-categories li.active, .sidebar-categories li:hover {
  background: #e5e0f7;
  color: #8b5cf6;
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
  background: #f3f0f7;
  font-size: 1.05rem;
  color: #6a4c9b;
  outline: none;
  box-shadow: 0 1px 4px rgba(160, 130, 255, 0.04);
}
.search-icon {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
.refresh-btn {
  background: #6a4c9b;
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
  background: #ec744a;
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
  background: #d45d2c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(236, 116, 74, 0.15);
}

.ask-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* Special styling for login button */
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
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(160, 130, 255, 0.09);
  padding: 26px 28px 20px 28px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  transition: box-shadow 0.2s, transform 0.2s;
  border: 1px solid #f3f0f7;
}
.forum-question-card:hover {
  box-shadow: 0 8px 24px rgba(160, 130, 255, 0.16);
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
  background: #e5e7eb;
  box-shadow: 0 1px 4px rgba(160, 130, 255, 0.08);
}
.question-user {
  font-weight: 600;
  color: #6a4c9b;
  font-size: 1rem;
  display: block;
}
.question-time {
  font-size: 0.85rem;
  color: #b6a7d6;
  display: block;
}
.question-menu {
  margin-left: auto;
  font-size: 1.5rem;
  color: #b6a7d6;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s;
  position: relative;
}
.question-menu:hover {
  background: #f3f0f7;
}
.question-menu-dropdown {
  position: absolute;
  top: 28px;
  right: 0;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(160, 130, 255, 0.13);
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
  color: #6a4c9b;
  cursor: pointer;
  border-radius: 8px;
  transition: background 0.2s;
}
.question-menu-dropdown button:hover {
  background: #f3f0f7;
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
    padding: 60px 0 18px 0;
  }
  .forum-hero-content {
    padding-left: 6px;
    padding-top: 40px;
  }
  .forum-hero-content h1 {
    font-size: 1.1rem;
    margin-bottom: 4px;
    margin-left: 20px;
  }
  .forum-hero-content p {
    font-size: 0.85rem;
    margin-left: 20px;
  }
  .forum-main-content {
    flex-direction: column;
    padding: 0 2px 12px 2px;
    gap: 0;
  }
  .forum-sidebar {
    display: none; /* Sembunyikan sidebar di mobile */
  }
  .mobile-filter-row {
    display: flex; /* Tampilkan mobile filter di mobile */
  }
  .forum-featured-links {
    padding: 20px 2px;
    margin-top: 60px;
    margin-bottom: 0px;
    min-width: 0;
    width: 100%;
    font-size: 0.85rem;
  }
  .forum-content {
    margin-top: 12px;
    font-size: 0.85rem;
  }
  .forum-search-row {
    flex-direction: column;
    gap: 6px;
    margin-bottom: 10px;
  }
  .forum-search-box {
    padding-right: 0;
  }
  .forum-search-box input {
    padding: 8px 28px 8px 8px;
    font-size: 0.85rem;
    border-radius: 8px;
  }
  .refresh-btn {
    padding: 8px;
    border-radius: 8px;
  }
  .refresh-btn svg {
    width: 16px;
    height: 16px;
  }
  .ask-btn {
    padding: 7px 12px;
    font-size: 0.85rem;
    border-radius: 8px;
  }
  .forum-questions-list {
    gap: 10px;
  }
  .forum-question-card {
    padding: 8px 2px 6px 2px;
    border-radius: 8px;
    font-size: 0.85rem;
  }
  .question-header {
    gap: 6px;
  }
  .question-avatar {
    width: 22px;
    height: 22px;
  }
  .question-user {
    font-size: 0.85rem;
  }
  .question-time {
    font-size: 0.7rem;
  }
  .question-title {
    font-size: 0.92rem;
    margin-bottom: 1px;
  }
  .question-desc {
    font-size: 0.8rem;
    margin-bottom: 1px;
  }
  .question-footer {
    gap: 6px;
    margin-top: 2px;
  }
  .question-tag {
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 5px;
  }
  .question-comments {
    font-size: 0.75rem;
    gap: 2px;
  }
  .forum-featured-links h4 {
    font-size: 0.9rem;
    margin-bottom: 4px;
  }
  .forum-featured-links a {
    font-size: 0.85rem;
  }
  .forum-detail-panel {
    max-width: 100vw;
    padding: 8px 2px 6px 2px;
    border-radius: 0;
    font-size: 0.85rem;
  }
  .detail-avatar {
    width: 28px;
    height: 28px;
  }
  .detail-user {
    font-size: 0.85rem;
  }
  .detail-time {
    font-size: 0.7rem;
  }
  .detail-title {
    font-size: 0.92rem;
    margin-bottom: 2px;
  }
  .detail-desc {
    font-size: 0.8rem;
    margin-bottom: 4px;
  }
  .detail-tag span {
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 5px;
    margin-bottom: 6px;
  }
  .detail-responses-box textarea {
    font-size: 0.85rem;
    padding: 6px 1px;
    border-radius: 6px;
  }
  .detail-responses-actions {
    gap: 4px;
  }
  .cancel-btn, .response-btn {
    padding: 4px 10px;
    font-size: 0.85rem;
    border-radius: 6px;
  }
  .detail-answers {
    gap: 8px;
    margin-top: 8px;
  }
  .detail-answer {
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 0.85rem;
  }
  .answer-header {
    font-size: 0.8rem;
    gap: 4px;
  }
  .answer-time {
    font-size: 0.7rem;
  }
  .answer-body {
    font-size: 0.8rem;
  }
  .forum-detail-overlay ~ .forum-main-content,
  .forum-main-content.panel-shift {
    margin-left: 8vw;
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
    max-width: 88vw;
    margin-left: 12vw;
    box-shadow: 0 2px 12px rgba(80,60,120,0.13);
  }
  .close-ask {
    top: 8px;
    right: 8px;
    font-size: 1.3rem;
  }
  .ask-form-group {
    margin-bottom: 8px;
  }
  .ask-form-group label {
    font-size: 0.85rem;
    margin-bottom: 2px;
  }
  .ask-form-group input, .ask-form-group textarea, .ask-form-group select {
    padding: 6px 8px;
    border-radius: 6px;
    font-size: 0.85rem;
  }
  .ask-form-group textarea {
    min-height: 40px;
  }
  .ask-form-actions {
    gap: 4px;
    margin-top: 4px;
  }
  .cancel-btn, .response-btn {
    padding: 4px 10px;
    font-size: 0.85rem;
    border-radius: 6px;
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
  gap: 8px;
  margin-bottom: 16px;
  padding: 0 4px;
}

.mobile-dropdown-filter {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  font-size: 0.9rem;
  color: #374151;
}

.mobile-my-questions-btn {
  padding: 8px 16px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  color: #6a4c9b;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.mobile-my-questions-btn.active {
  background: #f3f0f7;
  border-color: #6a4c9b;
  color: #6a4c9b;
}

.mobile-my-questions-btn:hover {
  background: #f9f7fb;
}

@media (max-width: 700px) {
  .loading-state, .empty-state {
    padding: 40px 10px;
  }
  
  .empty-icon {
    font-size: 3rem;
  }
  
  .empty-state h3 {
    font-size: 1.1rem;
  }
  
  .empty-state p {
    font-size: 0.9rem;
  }
  
  .ask-first-btn {
    padding: 10px 24px;
    font-size: 0.95rem;
  }
  
  .detail-answer {
    padding: 12px;
    margin-bottom: 12px;
    border-radius: 8px;
  }
  
  .answer-header {
    margin-bottom: 6px;
  }
  
  .answer-user {
    font-size: 0.9rem;
  }
  
  .answer-time {
    font-size: 0.8rem;
  }
  
  .answer-body {
    font-size: 0.9rem;
  }
  
  .responses-header h3 {
    font-size: 1rem;
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
    padding-bottom: 80px; /* Kurangi padding untuk mobile */
    max-height: 50vh; /* Kurangi tinggi maksimal untuk mobile */
  }
  
  .responses-header {
    margin-bottom: 12px;
    padding-bottom: 8px;
  }
  
  .responses-header h3 {
    font-size: 1rem;
  }
  
  .answer-user-info {
    gap: 6px; /* Kurangi gap untuk mobile */
  }
  
  .answer-avatar {
    width: 28px; /* Kurangi ukuran avatar untuk mobile */
    height: 28px;
  }
  
  .answer-user-details {
    gap: 3px; /* Kurangi jarak untuk mobile */
  }
  
  .answer-actions {
    gap: 8px; /* Kurangi gap untuk mobile */
    justify-content: flex-end; /* Tetap rata kanan di mobile */
  }
  
  .vote-section {
    gap: 4px; /* Kurangi gap untuk mobile */
  }
  
  .vote-btn {
    font-size: 1rem; /* Kurangi ukuran untuk mobile */
    padding: 3px 4px;
  }
  
  .vote-count {
    font-size: 0.85rem; /* Kurangi ukuran untuk mobile */
  }
  
  .response-actions {
    gap: 6px;
  }
  
  .edit-response-btn,
  .delete-response-btn {
    font-size: 0.9rem;
    padding: 3px;
  }
  
  .edit-response-textarea {
    padding: 10px;
    font-size: 0.9rem;
    min-height: 70px;
  }
  
  .edit-response-actions {
    gap: 6px;
    margin-top: 6px;
  }
  
  .cancel-edit-btn,
  .save-edit-btn {
    padding: 5px 10px;
    font-size: 0.85rem;
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
