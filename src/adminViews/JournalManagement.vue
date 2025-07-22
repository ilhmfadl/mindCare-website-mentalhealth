<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <template v-if="mode === 'list'">
        <div class="journal-table-outer">
          <div class="journal-table-container">
            <div class="journal-header">
              <div class="table-title">Daftar Jurnal</div>
              <button class="btn-add" type="button" @click="mode = 'add'">
                <span class="btn-add-icon">✚</span> Tambah Jurnal
              </button>
            </div>
            <div class="table-wrapper">
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
                <textarea id="quote" v-model="quote" rows="6" required placeholder="Your first name"></textarea>
              </div>
              <div class="form-actions">
                <button type="button" class="btn-cancel" @click="cancelAdd">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
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
                    <div class="journal-source"><b>Sumber:</b> {{ j.source }}</div>
                    <div class="journal-quote"><b>Kutipan:</b> {{ j.quote }}</div>
                  </template>
                </div>
                <div class="journal-actions">
                  <template v-if="editIndex === i">
                    <button class="btn-save-edit" @click="saveEdit(i)">Simpan</button>
                    <button class="btn-cancel-edit" @click="cancelEdit">Batal</button>
                  </template>
                  <template v-else>
                    <button class="btn-edit" @click="startEdit(i, j)">Edit</button>
                    <button class="btn-delete" @click="confirmDelete(i)">Delete</button>
                  </template>
                </div>
              </li>
            </ul>
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
const dummyJournals = {
  Depresi: [
    { source: 'https://journal1.com', quote: 'Kutipan jurnal depresi 1' },
    { source: 'https://journal2.com', quote: 'Kutipan jurnal depresi 2' },
    { source: 'https://journal3.com', quote: 'Kutipan jurnal depresi 3' }
  ],
  Anxiety: [
    { source: 'https://anxiety.com', quote: 'Kutipan jurnal anxiety' },
    { source: 'https://anxiety2.com', quote: 'Kutipan jurnal anxiety 2' }
  ],
  Skizofrenia: [],
  'Gangguan kepribadian': [],
  OCD: [],
  Bipolar: [],
  'Gangguan Makan': [],
  PTSD: []
};
export default {
  name: 'JournalManagement',
  components: { AdminHeader, ConfirmationModal },
  data() {
    // Inisialisasi state jurnal per kategori
    const journalsByCategory = {};
    Object.keys(dummyJournals).forEach(key => {
      journalsByCategory[key] = [...dummyJournals[key]];
    });
    return {
      mode: 'list',
      journals: [
        { name: 'Depresi', count: dummyJournals.Depresi.length },
        { name: 'Anxiety', count: dummyJournals.Anxiety.length },
        { name: 'Skizofrenia', count: dummyJournals.Skizofrenia.length },
        { name: 'Gangguan kepribadian', count: dummyJournals['Gangguan kepribadian'].length },
        { name: 'OCD', count: dummyJournals.OCD.length },
        { name: 'Bipolar', count: dummyJournals.Bipolar.length },
        { name: 'Gangguan Makan', count: dummyJournals['Gangguan Makan'].length },
        { name: 'PTSD', count: dummyJournals.PTSD.length },
      ],
      category: '',
      source: '',
      quote: '',
      // Detail
      detailCategory: '',
      localJournals: [],
      journalsByCategory,
      editIndex: null,
      editSource: '',
      editQuote: '',
      showDeleteModal: false,
      deleteIndex: null
    };
  },
  methods: {
    openDetail(item) {
      this.detailCategory = item.name;
      this.localJournals = this.journalsByCategory[item.name];
      this.editIndex = null;
      this.editSource = '';
      this.editQuote = '';
      this.mode = 'detail';
    },
    submitForm() {
      // Tambah jurnal ke kategori yang dipilih
      if (!this.journalsByCategory[this.category]) {
        this.journalsByCategory[this.category] = [];
      }
      this.journalsByCategory[this.category].push({ source: this.source, quote: this.quote });
      // Update count di tabel
      const cat = this.journals.find(j => j.name === this.category);
      if (cat) cat.count = this.journalsByCategory[this.category].length;
      // Jika sedang melihat detail kategori yang sama, update localJournals
      if (this.mode === 'detail' && this.detailCategory === this.category) {
        this.localJournals = this.journalsByCategory[this.category];
      }
      this.category = '';
      this.source = '';
      this.quote = '';
      if (this.detailCategory && this.mode === 'add') {
        this.mode = 'detail';
      } else {
        this.mode = 'list';
      }
    },
    startEdit(idx, j) {
      this.editIndex = idx;
      this.editSource = j.source;
      this.editQuote = j.quote;
    },
    saveEdit(idx) {
      if (this.editSource.trim() && this.editQuote.trim()) {
        this.$set ? this.$set(this.localJournals, idx, { source: this.editSource, quote: this.editQuote }) : this.localJournals.splice(idx, 1, { source: this.editSource, quote: this.editQuote });
        // Sinkronkan ke journalsByCategory
        if (this.detailCategory && this.journalsByCategory[this.detailCategory]) {
          this.journalsByCategory[this.detailCategory][idx] = { source: this.editSource, quote: this.editQuote };
        }
      }
      this.editIndex = null;
      this.editSource = '';
      this.editQuote = '';
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
    deleteJournal() {
      if (this.deleteIndex !== null) {
        // Hapus hanya dari journalsByCategory, lalu update localJournals
        if (this.detailCategory && this.journalsByCategory[this.detailCategory]) {
          this.journalsByCategory[this.detailCategory].splice(this.deleteIndex, 1);
          this.localJournals = [...this.journalsByCategory[this.detailCategory]];
        }
        // Update count di tabel
        const cat = this.journals.find(j => j.name === this.detailCategory);
        if (cat) cat.count = this.journalsByCategory[this.detailCategory].length;
      }
      this.showDeleteModal = false;
      this.deleteIndex = null;
    },
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
  color: #b5b5b5;
  font-weight: 600;
  background: #f7f3fa;
  border-bottom: 2px solid #ede7f6;
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
</style> 