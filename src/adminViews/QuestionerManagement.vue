<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <template v-if="mode === 'list'">
        <div class="questioner-table-outer">
          <div class="questioner-table-container">
            <div class="questioner-header">
              <div class="table-title">Daftar Pertanyaan Tes Diri</div>
              <button class="btn-add" type="button" @click="addFromList">
                <span class="btn-add-icon">✚</span> Tambahkan Pertanyaan Baru
              </button>
            </div>
            
            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <p>Memuat data pertanyaan...</p>
            </div>
            
            <!-- Error State -->
            <div v-if="error" class="error-state">
              <p>{{ error }}</p>
              <button @click="fetchQuestions" class="btn-retry">Coba Lagi</button>
            </div>
            
            <!-- Success Message -->
            <div v-if="successMessage" class="success-message">
              <p>{{ successMessage }}</p>
            </div>
            
            <div v-if="!loading && !error" class="table-wrapper">
              <table class="questioner-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="question in questions" :key="question.nomor">
                    <td>{{ question.nomor }}</td>
                    <td>{{ question.pertanyaan }}</td>
                    <td>
                      <span class="category-badge" :class="'category-' + question.kategori">
                        {{ getCategoryLabel(question.kategori) }}
                      </span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <button class="btn-edit" title="Edit" @click="editQuestion(question)">
                          <span>✏️</span>
                        </button>
                        <button class="btn-delete" title="Hapus" @click="confirmDelete(question.nomor)">
                          <span>🗑️</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </template>
      <template v-else-if="mode === 'add'">
        <div class="add-question-card-outer">
          <div class="add-question-card">
            <h2 class="form-title">Tambah Pertanyaan Baru</h2>
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="question">Pertanyaan</label>
                <textarea 
                  id="question" 
                  v-model="question" 
                  rows="6" 
                  required 
                  placeholder="Masukkan pertanyaan tes diri"
                  :disabled="submitting"
                ></textarea>
              </div>
              <div class="form-group">
                <label for="category">Kategori</label>
                <select 
                  id="category" 
                  v-model="selectedCategory" 
                  required 
                  :disabled="submitting"
                  class="form-select"
                >
                  <option value="">Pilih kategori</option>
                  <option value="neurosis">Neurosis</option>
                  <option value="psikotik">Psikotik</option>
                  <option value="ptsd">PTSD</option>
                </select>
              </div>
              <div class="form-actions">
                <button type="button" class="btn-cancel" @click="cancelAdd" :disabled="submitting">Batal</button>
                <button type="submit" class="btn-save" :disabled="submitting">
                  {{ submitting ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </template>
      <template v-else-if="mode === 'edit'">
        <div class="add-question-card-outer">
          <div class="add-question-card">
            <h2 class="form-title">Edit Pertanyaan</h2>
            <form @submit.prevent="submitEdit">
              <div class="form-group">
                <label for="editQuestion">Pertanyaan</label>
                <textarea 
                  id="editQuestion" 
                  v-model="editValue" 
                  rows="6" 
                  required 
                  placeholder="Edit pertanyaan tes diri"
                  :disabled="submitting"
                ></textarea>
              </div>
              <div class="form-group">
                <label for="editCategory">Kategori</label>
                <select 
                  id="editCategory" 
                  v-model="editCategory" 
                  required 
                  :disabled="submitting"
                  class="form-select"
                >
                  <option value="">Pilih kategori</option>
                  <option value="neurosis">Neurosis</option>
                  <option value="psikotik">Psikotik</option>
                  <option value="ptsd">PTSD</option>
                </select>
              </div>
              <div class="form-actions">
                <button type="button" class="btn-cancel" @click="cancelEdit" :disabled="submitting">Batal</button>
                <button type="submit" class="btn-save" :disabled="submitting">
                  {{ submitting ? 'Mengupdate...' : 'Update' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </template>
    </main>
    
    <ConfirmationModal
      :visible="showDeleteModal"
      title="Konfirmasi Hapus"
      :message="'Yakin ingin menghapus pertanyaan nomor ' + deleteNomor + '? Tindakan ini tidak dapat dibatalkan.'"
      @confirm="deleteQuestion"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
import ConfirmationModal from '../components/ConfirmationModal.vue';
import axios from 'axios';

export default {
  name: 'QuestionerManagement',
  components: { AdminHeader, ConfirmationModal },
  data() {
    return {
      mode: 'list',
      questions: [],
      loading: false,
      error: '',
      successMessage: '',
      submitting: false,
      
      // Add form
      question: '',
      selectedCategory: '',
      
      // Edit form
      editValue: '',
      editNomor: null,
      editCategory: '',
      
      // Delete
      showDeleteModal: false,
      deleteNomor: null
    };
  },
  async mounted() {
    await this.fetchQuestions();
  },
  methods: {
    async fetchQuestions() {
      this.loading = true;
      this.error = '';
      try {
        // Add timestamp to prevent caching
        const timestamp = new Date().getTime();
        const response = await axios.get(`https://mindcareindependent.com/api/tesdiri_questions.php?t=${timestamp}`);
        console.log('Fetch response:', response.data); // Debug log
        if (response.data.success) {
          this.questions = response.data.questions;
          console.log('Updated questions:', this.questions); // Debug log
        } else {
          this.error = 'Gagal mengambil data pertanyaan';
        }
      } catch (error) {
        this.error = 'Gagal koneksi ke server';
        console.error('Error fetching questions:', error);
      } finally {
        this.loading = false;
      }
    },
    
    // Add methods
    addFromList() {
      this.question = '';
      this.selectedCategory = '';
      this.mode = 'add';
    },
    
    async submitForm() {
      if (!this.question.trim() || this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        const response = await axios.post('https://mindcareindependent.com/api/add_tesdiri_question.php', {
          pertanyaan: this.question.trim(),
          kategori: this.selectedCategory
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.question = '';
          this.selectedCategory = '';
          this.mode = 'list';
          
          // Force refresh data to ensure it's updated
          await this.forceRefreshData();
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal menambah pertanyaan';
        }
      } catch (error) {
        console.error('Error adding question:', error);
        this.error = 'Gagal menambah pertanyaan: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },
    
    cancelAdd() {
      this.question = '';
      this.selectedCategory = '';
      this.mode = 'list';
    },
    
    // Edit methods
    editQuestion(question) {
      this.editValue = question.pertanyaan;
      this.editNomor = question.nomor;
      this.editCategory = question.kategori;
      this.mode = 'edit';
    },
    
    async submitEdit() {
      if (!this.editValue.trim() || this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        console.log('Submitting edit:', { nomor: this.editNomor, pertanyaan: this.editValue.trim() }); // Debug log
        
        const response = await axios.put('https://mindcareindependent.com/api/update_tesdiri_question.php', {
          nomor: this.editNomor,
          pertanyaan: this.editValue.trim(),
          kategori: this.editCategory
        });
        
        console.log('Edit response:', response.data); // Debug log
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.editValue = '';
          this.editNomor = null;
          this.editCategory = '';
          this.mode = 'list';
          
          // Force refresh data multiple times to ensure it's updated
          await this.forceRefreshData();
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal mengupdate pertanyaan';
        }
      } catch (error) {
        console.error('Error updating question:', error);
        this.error = 'Gagal mengupdate pertanyaan: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },
    
    // Force refresh data multiple times to ensure it's updated
    async forceRefreshData() {
      // First refresh
      await this.fetchQuestions();
      
      // Second refresh after a short delay
      setTimeout(async () => {
        await this.fetchQuestions();
      }, 200);
      
      // Third refresh after another delay
      setTimeout(async () => {
        await this.fetchQuestions();
      }, 500);
    },
    
    cancelEdit() {
      this.editValue = '';
      this.editNomor = null;
      this.editCategory = '';
      this.mode = 'list';
    },
    
    // Delete methods
    confirmDelete(nomor) {
      this.deleteNomor = nomor;
      this.showDeleteModal = true;
    },
    
    async deleteQuestion() {
      if (this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        const response = await axios.delete('https://mindcareindependent.com/api/delete_tesdiri_question.php', {
          data: { nomor: this.deleteNomor }
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.showDeleteModal = false;
          this.deleteNomor = null;
          
          // Force refresh data to ensure it's updated
          await this.forceRefreshData();
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal menghapus pertanyaan';
        }
      } catch (error) {
        console.error('Error deleting question:', error);
        this.error = 'Gagal menghapus pertanyaan: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },

    // Helper to get category label
    getCategoryLabel(category) {
      switch (category) {
        case 'neurosis':
          return 'Neurosis';
        case 'psikotik':
          return 'Psikotik';
        case 'ptsd':
          return 'PTSD';
        default:
          return 'Tidak Dikategorikan';
      }
    }
  }
};
</script>

<style scoped>
.admin-page-bg {
  min-height: 100vh;
  background: #faf7f3;
}

main {
  flex: 1;
  padding-top: 20px;
}

.questioner-table-outer {
  width: 100%;
  max-width: 1350px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  box-sizing: border-box;
}

.questioner-table-container {
  background: #faf7f3;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.06);
  padding: 32px 24px 24px 24px;
  margin-top: 32px;
  max-width: 100%;
}

.questioner-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.table-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d2350;
  letter-spacing: 0.2px;
}

.btn-add {
  background: #6C3483;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 12px 28px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 2px 8px rgba(108,52,131,0.07);
  transition: all 0.2s ease;
}

.btn-add:hover {
  background: #51236a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108,52,131,0.15);
}

.btn-add-icon {
  font-size: 1.2rem;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  color: #6C3483;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #6C3483;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error State */
.error-state {
  background: #f8d7da;
  color: #721c24;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #f5c6cb;
  margin-bottom: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-retry {
  background: #dc3545;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-retry:hover {
  background: #c82333;
}

/* Success Message */
.success-message {
  background: #d4edda;
  color: #155724;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #c3e6cb;
  margin-bottom: 16px;
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.questioner-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-family: inherit;
}

.questioner-table th, 
.questioner-table td {
  padding: 16px 12px;
  text-align: left;
  font-size: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.questioner-table thead th {
  color: #6C3483;
  font-weight: 600;
  background: #f8f5ff;
  border-bottom: 2px solid #e0d5f0;
  position: sticky;
  top: 0;
  z-index: 10;
}

.questioner-table tbody tr {
  background: #faf7f3;
  transition: background 0.2s ease;
}

.questioner-table tbody tr:hover {
  background: #f8f5ff;
}

.questioner-table tbody tr:last-child td {
  border-bottom: none;
}

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.btn-edit, .btn-delete {
  background: none;
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-edit {
  color: #6C3483;
  background: #f0e8ff;
}

.btn-edit:hover {
  background: #e0d5f0;
  transform: translateY(-1px);
}

.btn-delete {
  color: #dc3545;
  background: #ffe8e8;
}

.btn-delete:hover {
  background: #ffd0d0;
  transform: translateY(-1px);
}

.category-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #fff;
  text-transform: capitalize;
}

.category-neurosis {
  background-color: #6C3483;
}

.category-psikotik {
  background-color: #4CAF50;
}

.category-ptsd {
  background-color: #F44336;
}

.add-question-card-outer {
  width: 100%;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 40px;
}

.add-question-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(108,52,131,0.1);
  padding: 48px;
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
}

.form-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d2350;
  margin-bottom: 32px;
  text-align: center;
}

.form-group {
  margin-bottom: 24px;
}

label {
  font-weight: 600;
  color: #2d2350;
  margin-bottom: 12px;
  font-size: 1.1rem;
  display: block;
}

textarea {
  width: 100%;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 16px;
  font-size: 1rem;
  font-family: inherit;
  background: #fff;
  transition: all 0.2s ease;
  outline: none;
  resize: vertical;
  min-height: 120px;
}

textarea:focus {
  border-color: #6C3483;
  box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
}

textarea:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
}

.form-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  background: #fff;
  transition: all 0.2s ease;
  outline: none;
  cursor: pointer;
}

.form-select:focus {
  border-color: #6C3483;
  box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
}

.form-select:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
  color: #adb5bd;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 32px;
}

.btn-cancel, .btn-save {
  padding: 12px 32px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
}

.btn-cancel {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
}

.btn-cancel:hover:not(:disabled) {
  background: #e9ecef;
  transform: translateY(-1px);
}

.btn-cancel:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-save {
  background: #6C3483;
  color: #fff;
}

.btn-save:hover:not(:disabled) {
  background: #51236a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108,52,131,0.2);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
  .questioner-table-outer {
    padding-left: 16px;
    padding-right: 16px;
  }
  
  .questioner-table-container {
    padding: 20px 16px;
  }
  
  .questioner-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }
  
  .btn-add {
    justify-content: center;
  }
  
  .questioner-table th, 
  .questioner-table td {
    padding: 12px 8px;
    font-size: 0.9rem;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }
  
  .add-question-card {
    padding: 24px;
    margin: 16px;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn-cancel, .btn-save {
    width: 100%;
  }
  
  .error-state {
    flex-direction: column;
    gap: 12px;
  }
}

@media (max-width: 480px) {
  .table-title {
    font-size: 1.2rem;
  }
  
  .questioner-table {
    font-size: 0.85rem;
  }
  
  .questioner-table th, 
  .questioner-table td {
    padding: 8px 4px;
  }
}
</style> 