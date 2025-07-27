<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <template v-if="mode === 'list'">
        <div class="journal-table-outer">
          <div class="journal-table-container">
            <div class="journal-header">
              <div class="table-title">Daftar Jurnal</div>
              <button class="btn-add" type="button" @click="resetAddForm">
                <span class="btn-add-icon">✚</span> Tambah Jurnal
              </button>
            </div>
            
            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <p>Memuat data jurnal...</p>
            </div>
            
            <!-- Error State -->
            <div v-else-if="error" class="error-state">
              <p>{{ error }}</p>
              <button @click="fetchJournals" class="btn-retry">Coba Lagi</button>
            </div>
            
            <!-- Success Message -->
            <div v-if="successMessage" class="success-message">
              {{ successMessage }}
            </div>
            
            <!-- Table Content -->
            <div v-if="!loading && !error" class="table-wrapper">
              <table class="journal-table">
                <thead>
                  <tr>
                    <th>Kategori</th>
                    <th>Jumlah Jurnal</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in journals" :key="item.name">
                    <td>{{ item.name }}</td>
                    <td>{{ item.count }}</td>
                    <td>
                      <button class="btn-detail" title="Detail" @click="openDetail(item)">
                        <span class="arrow">→</span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </template>
      
      <template v-else-if="mode === 'add'">
        <div class="add-journal-card-outer">
          <div class="add-journal-card">
            <h2 class="form-title">Informasi Jurnal</h2>
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="category">Kategori Jurnal</label>
                <select id="category" v-model="category" required :disabled="!!detailCategory">
                  <option value="" disabled>Pilih kategori pertanyaan</option>
                  <option v-for="cat in journals.map(j => j.name)" :key="cat" :value="cat">{{ cat }}</option>
                </select>
              </div>
              <div class="form-group">
                <label for="source">Masukkan Sumber Jurnal</label>
                <input id="source" v-model="source" type="text" placeholder="Masukkan link sumber jurnal" required />
              </div>
              <div class="form-group">
                <label for="quote">Masukkan Kutipan Jurnal</label>
                <textarea id="quote" v-model="quote" rows="6" required placeholder="Masukkan kutipan jurnal"></textarea>
              </div>
              <div class="form-actions">
                <button type="button" class="btn-cancel" @click="cancelAdd" :disabled="submitting">Batal</button>
                <button type="submit" class="btn-save" :disabled="submitting">
                  <span v-if="submitting">Menyimpan...</span>
                  <span v-else>Simpan</span>
                </button>
              </div>
              
              <!-- Error Message -->
              <div v-if="error" class="error-message">
                {{ error }}
              </div>
              
              <!-- Success Message -->
              <div v-if="successMessage" class="success-message">
                {{ successMessage }}
              </div>
            </form>
          </div>
        </div>
      </template>
      
      <template v-else-if="mode === 'detail'">
        <div class="detail-journal-outer">
          <div class="detail-journal-container">
            <div class="detail-header">
              <button class="btn-back" @click="mode = 'list'">← Kembali</button>
              <h2 class="detail-title">Daftar Jurnal - {{ detailCategory }}</h2>
              <button class="btn-add" type="button" style="margin-left:auto" @click="addFromDetail">
                <span class="btn-add-icon">✚</span> Tambah Jurnal
              </button>
            </div>
            <ul class="journal-list">
              <li v-for="(j, i) in localJournals" :key="i" class="journal-item">
                <div class="journal-content">
                  <template v-if="editIndex === i">
                    <input v-model="editSource" class="edit-input" placeholder="Sumber jurnal" />
                    <textarea v-model="editQuote" class="edit-input" placeholder="Kutipan jurnal"></textarea>
                  </template>
                  <template v-else>
                    <div class="journal-source"><b>Sumber:</b> {{ j.sumber }}</div>
                    <div class="journal-quote"><b>Kutipan:</b> {{ j.kutipan }}</div>
                  </template>
                </div>
                <div class="journal-actions">
                  <template v-if="editIndex === i">
                    <button class="btn-save-edit" @click="saveEdit(i)" :disabled="submitting">
                      <span v-if="submitting">Menyimpan...</span>
                      <span v-else>Simpan</span>
                    </button>
                    <button class="btn-cancel-edit" @click="cancelEdit" :disabled="submitting">Batal</button>
                  </template>
                  <template v-else>
                    <button class="btn-edit" @click="startEdit(i, j)" :disabled="submitting">Edit</button>
                    <button class="btn-delete" @click="confirmDelete(i)" :disabled="submitting">Delete</button>
                  </template>
                </div>
              </li>
            </ul>
            
            <!-- Error Message -->
            <div v-if="error" class="error-message">
              {{ error }}
            </div>
            
            <!-- Success Message -->
            <div v-if="successMessage" class="success-message">
              {{ successMessage }}
            </div>
            
            <ConfirmationModal
              :visible="showDeleteModal"
              title="Konfirmasi Hapus"
              :message="'Yakin ingin menghapus jurnal ini? Tindakan ini tidak dapat dibatalkan.'"
              @confirm="deleteJournal"
              @cancel="showDeleteModal = false"
            />
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
import ConfirmationModal from '../components/ConfirmationModal.vue';
import axios from 'axios';

export default {
  name: 'JournalManagement',
  components: { AdminHeader, ConfirmationModal },
  data() {
    return {
      mode: 'list',
      loading: false,
      error: '',
      submitting: false,
      journals: [
        { name: 'Neurosis', count: 0 },
        { name: 'Psikotik', count: 0 },
        { name: 'PTSD', count: 0 },
      ],
      category: '',
      source: '',
      quote: '',
      // Detail
      detailCategory: '',
      localJournals: [],
      editIndex: null,
      editSource: '',
      editQuote: '',
      showDeleteModal: false,
      deleteIndex: null,
      successMessage: '',
      allJournals: []
    };
  },
  mounted() {
    this.fetchJournals();
  },
  methods: {
    async fetchJournals() {
      this.loading = true;
      this.error = '';
      try {
        const response = await axios.get('https://mindcareindependent.com/api/get_journals.php');
        
        if (response.data.success) {
          const { journals, category_counts } = response.data.data;
          
          // Update category counts
          this.journals = this.journals.map(cat => ({
            ...cat,
            count: category_counts[cat.name.toLowerCase()] || 0
          }));
          
          // Store all journals for filtering
          this.allJournals = journals;
        } else {
          this.error = response.data.message || 'Gagal mengambil data jurnal';
          // Auto hide error message after 5 seconds
          setTimeout(() => {
            this.error = '';
          }, 5000);
        }
      } catch (error) {
        console.error('Error fetching journals:', error);
        this.error = 'Terjadi kesalahan saat mengambil data jurnal';
        // Auto hide error message after 5 seconds
        setTimeout(() => {
          this.error = '';
        }, 5000);
      } finally {
        this.loading = false;
      }
    },
    
    openDetail(item) {
      this.detailCategory = item.name;
      // Filter journals by category
      this.localJournals = this.allJournals.filter(j => 
        j.kategori.toLowerCase() === item.name.toLowerCase()
      );
      this.editIndex = null;
      this.editSource = '';
      this.editQuote = '';
      this.mode = 'detail';
    },
    
    async submitForm() {
      this.submitting = true;
      this.error = '';
      this.successMessage = '';
      
      try {
        const response = await axios.post('https://mindcareindependent.com/api/add_journal.php', {
          kategori: this.category.toLowerCase(),
          sumber: this.source,
          kutipan: this.quote
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.category = '';
          this.source = '';
          this.quote = '';
          
          // Refresh data
          await this.fetchJournals();
          
          // Auto hide success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
          
          if (this.detailCategory && this.mode === 'add') {
            this.mode = 'detail';
            this.openDetail({ name: this.detailCategory });
          } else {
            this.mode = 'list';
          }
        } else {
          this.error = response.data.message || 'Gagal menambahkan jurnal';
          // Auto hide error message after 5 seconds
          setTimeout(() => {
            this.error = '';
          }, 5000);
        }
      } catch (error) {
        console.error('Error adding journal:', error);
        this.error = 'Terjadi kesalahan saat menambahkan jurnal';
        // Auto hide error message after 5 seconds
        setTimeout(() => {
          this.error = '';
        }, 5000);
      } finally {
        this.submitting = false;
      }
    },
    
    startEdit(idx, j) {
      this.editIndex = idx;
      this.editSource = j.sumber;
      this.editQuote = j.kutipan;
    },
    
    async saveEdit(idx) {
      if (!this.editSource.trim() || !this.editQuote.trim()) {
        this.error = 'Sumber dan kutipan tidak boleh kosong';
        return;
      }
      
      this.submitting = true;
      this.error = '';
      this.successMessage = '';
      
      try {
        const journal = this.localJournals[idx];
        const response = await axios.put('https://mindcareindependent.com/api/update_journal.php', {
          id: journal.id,
          kategori: journal.kategori,
          sumber: this.editSource,
          kutipan: this.editQuote
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          // Update local data
          this.localJournals[idx] = response.data.data;
          this.editIndex = null;
          this.editSource = '';
          this.editQuote = '';
          
          // Refresh main data
          await this.fetchJournals();
          
          // Auto hide success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal mengupdate jurnal';
          // Auto hide error message after 5 seconds
          setTimeout(() => {
            this.error = '';
          }, 5000);
        }
      } catch (error) {
        console.error('Error updating journal:', error);
        this.error = 'Terjadi kesalahan saat mengupdate jurnal';
        // Auto hide error message after 5 seconds
        setTimeout(() => {
          this.error = '';
        }, 5000);
      } finally {
        this.submitting = false;
      }
    },
    
    cancelEdit() {
      this.editIndex = null;
      this.editSource = '';
      this.editQuote = '';
    },
    
    confirmDelete(idx) {
      this.deleteIndex = idx;
      this.showDeleteModal = true;
    },
    
    async deleteJournal() {
      this.submitting = true;
      this.error = '';
      this.successMessage = '';
      
      try {
        const journal = this.localJournals[this.deleteIndex];
        const response = await axios.delete(`https://mindcareindependent.com/api/delete_journal.php?id=${journal.id}`);
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          // Remove from local list
          this.localJournals.splice(this.deleteIndex, 1);
          
          // Refresh main data
          await this.fetchJournals();
          
          // Auto hide success message after 3 seconds
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal menghapus jurnal';
          // Auto hide error message after 5 seconds
          setTimeout(() => {
            this.error = '';
          }, 5000);
        }
      } catch (error) {
        console.error('Error deleting journal:', error);
        this.error = 'Terjadi kesalahan saat menghapus jurnal';
        // Auto hide error message after 5 seconds
        setTimeout(() => {
          this.error = '';
        }, 5000);
      } finally {
        this.submitting = false;
        this.showDeleteModal = false;
        this.deleteIndex = null;
      }
    },
    
    // Method ini tidak diperlukan lagi karena data diambil dari API
    
    addFromDetail() {
      this.category = this.detailCategory;
      this.mode = 'add';
    },
    
    cancelAdd() {
      if (this.detailCategory) {
        this.mode = 'detail';
      } else {
        this.mode = 'list';
      }
    },
    
    resetAddForm() {
      this.detailCategory = '';
      this.category = '';
      this.source = '';
      this.quote = '';
      this.mode = 'add';
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
}
.journal-table-outer {
  width: 100%;
  max-width: 1350px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  box-sizing: border-box;
}
.journal-table-container {
  background: #faf7f3;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.06);
  padding: 32px 24px 24px 24px;
  margin-top: 32px;
  max-width: 100%;
}
.journal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
}
.table-title {
  font-size: 1.35rem;
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
  transition: background 0.2s;
}
.btn-add:hover {
  background: #51236a;
}
.btn-add-icon {
  font-size: 1.2rem;
  margin-right: 4px;
}
.table-wrapper {
  overflow-x: auto;
}
.journal-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #faf7f3;
  font-family: inherit;
}
.journal-table th, .journal-table td {
  padding: 14px 10px;
  text-align: left;
  font-size: 1rem;
}
.journal-table thead th {
  color: #6C3483;
  font-weight: 600;
  background: #f8f5ff;
  border-bottom: 2px solid #e0d5f0;
  position: sticky;
  top: 0;
  z-index: 1;
}
.journal-table tbody tr {
  border-bottom: 1px solid #f3f3f3;
  transition: background 0.18s;
}
.journal-table tbody tr:hover {
  background: #f7f3fa;
}
.btn-detail {
  background: #f4f4f4;
  border: none;
  border-radius: 20px;
  padding: 6px 14px;
  cursor: pointer;
  font-size: 1.1rem;
  color: #6C3483;
  transition: background 0.2s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 1px 4px rgba(108,52,131,0.08);
}
.btn-detail:hover {
  background: #ede7f6;
  box-shadow: 0 2px 8px rgba(108,52,131,0.13);
}
.arrow {
  font-size: 1.2rem;
  font-weight: bold;
}
.add-journal-card-outer {
  width: 100%;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 32px;
  padding-bottom: 32px;
}
.add-journal-card {
  background: #faf7f3;
  border-radius: 20px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.06);
  padding: 56px 56px 36px 56px;
  max-width: 950px;
  width: 100%;
  margin: 0 auto;
}
.form-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #23263b;
  margin-bottom: 28px;
  text-align: left;
}
.form-group {
  margin-bottom: 24px;
  display: flex;
  flex-direction: column;
}
label {
  font-weight: 600;
  color: #23263b;
  margin-bottom: 10px;
  font-size: 1.08rem;
}
select, textarea, input[type="text"] {
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  padding: 14px 14px;
  font-size: 1.05rem;
  font-family: inherit;
  background: #fff;
  transition: border 0.2s;
  outline: none;
}
select:focus, textarea:focus, input[type="text"]:focus {
  border-color: #6C3483;
}
textarea {
  resize: vertical;
  min-height: 100px;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 18px;
  margin-top: 38px;
}
.btn-cancel {
  background: #a94442;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 14px 38px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-cancel:hover {
  background: #922b21;
}
.btn-save {
  background: #388e3c;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 14px 38px;
  font-size: 1.08rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-save:hover {
  background: #256029;
}
@media (max-width: 1100px) {
  .add-journal-card {
    padding: 32px 12px 18px 12px;
    max-width: 99vw;
  }
}
@media (max-width: 900px) {
  .journal-table-outer {
    padding-left: 8px;
    padding-right: 8px;
  }
  .journal-table-container {
    padding: 16px 2px 10px 2px;
  }
  .table-title {
    font-size: 1.08rem;
    margin-bottom: 10px;
  }
  .btn-add {
    padding: 8px 14px;
    font-size: 0.92rem;
  }
  .journal-table th, .journal-table td {
    padding: 8px 4px;
    font-size: 0.92rem;
  }
}
.detail-journal-outer {
  width: 100%;
  max-width: 1350px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  box-sizing: border-box;
}
.detail-journal-container {
  margin-top: 32px;
  background: #faf7f3;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.06);
  padding: 48px 48px 36px 48px;
  max-width: 100%;
}
.detail-header {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 32px;
}
.btn-back {
  background: #ede7f6;
  color: #6C3483;
  border: none;
  border-radius: 8px;
  padding: 10px 24px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-back:hover {
  background: #d1c4e9;
}
.detail-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #23263b;
  margin: 0;
}
.journal-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.journal-item {
  font-size: 1.08rem;
  color: #23263b;
  background: #fff;
  border-radius: 8px;
  margin-bottom: 16px;
  padding: 18px 20px;
  display: flex;
  align-items: flex-start;
  gap: 18px;
  box-shadow: 0 1px 4px rgba(108,52,131,0.04);
}
.journal-content {
  flex: 1;
  min-width: 0;
  word-break: break-word;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.journal-source {
  color: #6C3483;
  font-size: 1.01rem;
}
.journal-quote {
  color: #23263b;
  font-size: 1.01rem;
}
.journal-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
  align-items: center;
}
.edit-input {
  width: 100%;
  font-size: 1.08rem;
  padding: 8px 12px;
  border: 1.5px solid #e0e0e0;
  border-radius: 6px;
  margin-bottom: 8px;
}
.btn-edit, .btn-delete, .btn-save-edit, .btn-cancel-edit {
  padding: 6px 16px;
  border-radius: 8px;
  font-size: 0.98rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-edit {
  background: #ede7f6;
  color: #6C3483;
}
.btn-edit:hover {
  background: #d1c4e9;
}
.btn-delete {
  background: #f87171;
  color: #fff;
}
.btn-delete:hover {
  background: #ef4444;
}
.btn-save-edit {
  background: #388e3c;
  color: #fff;
}
.btn-save-edit:hover {
  background: #256029;
}
.btn-cancel-edit {
  background: #a94442;
  color: #fff;
}
.btn-cancel-edit:hover {
  background: #922b21;
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
  border: 4px solid #ede7f6;
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
  text-align: center;
  padding: 40px;
  color: #a94442;
}

.btn-retry {
  background: #a94442;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 16px;
  transition: background 0.2s;
}

.btn-retry:hover {
  background: #922b21;
}

/* Success Message */
.success-message {
  background: #d4edda;
  color: #155724;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  border: 1px solid #c3e6cb;
}

/* Error Message */
.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  border: 1px solid #f5c6cb;
}

/* Disabled Button States */
.btn-add:disabled,
.btn-save:disabled,
.btn-cancel:disabled,
.btn-edit:disabled,
.btn-delete:disabled,
.btn-save-edit:disabled,
.btn-cancel-edit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-add:disabled:hover,
.btn-save:disabled:hover,
.btn-cancel:disabled:hover,
.btn-edit:disabled:hover,
.btn-delete:disabled:hover,
.btn-save-edit:disabled:hover,
.btn-cancel-edit:disabled:hover {
  background: inherit;
}
</style> 