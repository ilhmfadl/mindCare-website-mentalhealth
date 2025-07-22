<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <template v-if="mode === 'list'">
        <div class="questioner-table-outer">
          <div class="questioner-table-container">
            <div class="questioner-header">
              <div class="table-title">Daftar Pertanyaan</div>
              <button class="btn-add" type="button" @click="addFromList">
                <span class="btn-add-icon">✚</span> Tambahkan Pertanyaan Baru
              </button>
            </div>
            <div class="table-wrapper">
              <table class="questioner-table">
                <thead>
                  <tr>
                    <th>Kategori</th>
                    <th>Jumlah Pertanyaan</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in categories" :key="item.name">
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
        <div class="add-question-card-outer">
          <div class="add-question-card">
            <h2 class="form-title">Informasi Pertanyaan</h2>
            <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="category">Kategori Pertanyaan</label>
                <select id="category" v-model="category" required :disabled="!!detailCategory">
                  <option value="" disabled>Pilih kategori pertanyaan</option>
                  <option v-for="cat in categories.map(c => c.name)" :key="cat" :value="cat">{{ cat }}</option>
                </select>
              </div>
              <div class="form-group">
                <label for="question">Masukkan pertanyaan</label>
                <textarea id="question" v-model="question" rows="6" required placeholder="Your first name"></textarea>
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
        <div class="detail-question-outer">
          <div class="detail-question-container">
            <div class="detail-header">
              <button class="btn-back" @click="mode = 'list'">← Kembali</button>
              <h2 class="detail-title">Daftar Pertanyaan - {{ categoryName }}</h2>
              <button class="btn-add" type="button" style="margin-left:auto" @click="addFromDetail">
                <span class="btn-add-icon">✚</span> Tambah Pertanyaan
              </button>
            </div>
            <ul class="question-list">
              <li v-for="(q, i) in localQuestions" :key="i" class="question-item">
                <span class="question-index">{{ i + 1 }}.</span>
                <div class="question-content">
                  <template v-if="editIndex === i">
                    <input v-model="editValue" class="edit-input" />
                  </template>
                  <template v-else>
                    {{ q }}
                  </template>
                </div>
                <div class="question-actions">
                  <template v-if="editIndex === i">
                    <button class="btn-save-edit" @click="saveEdit(i)">Simpan</button>
                    <button class="btn-cancel-edit" @click="cancelEdit">Batal</button>
                  </template>
                  <template v-else>
                    <button class="btn-edit" @click="startEdit(i, q)">Edit</button>
                    <button class="btn-delete" @click="confirmDelete(i)">Delete</button>
                  </template>
                </div>
              </li>
            </ul>
            <ConfirmationModal
              :visible="showDeleteModal"
              title="Konfirmasi Hapus"
              :message="'Yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.'"
              @confirm="deleteQuestion"
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
const dummyQuestions = {
  depresi: [
    'Saya merasa sedih atau murung hampir setiap hari.',
    'Saya kehilangan minat pada aktivitas yang biasanya saya nikmati.',
    'Saya merasa lelah atau kurang energi.',
    'Saya merasa tidak berharga atau bersalah berlebihan.',
    'Saya mengalami kesulitan tidur atau tidur berlebihan.'
  ],
  anxiety: [
    'Saya sering merasa cemas atau khawatir berlebihan.',
    'Saya sulit mengendalikan rasa khawatir.',
    'Saya merasa gelisah atau tegang.',
    'Saya mudah lelah karena kecemasan.',
    'Saya mengalami gangguan tidur karena rasa cemas.'
  ],
  skizofrenia: [
    'Saya mendengar suara-suara yang tidak didengar orang lain.',
    'Saya merasa ada yang mengendalikan pikiran saya.',
    'Saya sulit membedakan kenyataan dan khayalan.',
    'Saya menarik diri dari lingkungan sosial.',
    'Saya mengalami perubahan emosi yang drastis.'
  ],
  kepribadian: [
    'Saya sering mengalami perubahan suasana hati yang ekstrem.',
    'Saya kesulitan menjaga hubungan dengan orang lain.',
    'Saya merasa identitas diri saya tidak jelas.',
    'Saya sering bertindak impulsif.',
    'Saya merasa takut ditinggalkan.'
  ],
  ocd: [
    'Saya memiliki pikiran atau dorongan yang tidak diinginkan dan berulang.',
    'Saya merasa harus melakukan sesuatu berulang-ulang untuk meredakan kecemasan.',
    'Saya menghabiskan banyak waktu untuk ritual tertentu.',
    'Saya menyadari pikiran/ritual saya berlebihan, tapi sulit dikendalikan.',
    'Saya merasa terganggu dengan kebiasaan ini.'
  ],
  bipolar: [
    'Saya pernah mengalami periode sangat bersemangat atau sangat sedih.',
    'Saya merasa energi saya sangat tinggi atau sangat rendah.',
    'Saya mengalami perubahan suasana hati yang drastis.',
    'Saya sulit tidur saat merasa sangat bersemangat.',
    'Saya pernah melakukan hal impulsif saat suasana hati naik.'
  ],
  makan: [
    'Saya sangat khawatir dengan berat badan atau bentuk tubuh.',
    'Saya membatasi makan secara berlebihan atau makan berlebihan secara tiba-tiba.',
    'Saya merasa bersalah setelah makan.',
    'Saya sering memikirkan makanan sepanjang hari.',
    'Saya pernah memuntahkan makanan secara sengaja.'
  ],
  ptsd: [
    'Saya pernah mengalami peristiwa traumatis.',
    'Saya sering teringat kembali peristiwa tersebut.',
    'Saya menghindari hal-hal yang mengingatkan pada trauma.',
    'Saya mudah terkejut atau marah.',
    'Saya sulit tidur atau sering mimpi buruk.'
  ]
};
const categoryNames = {
  depresi: 'Depresi',
  anxiety: 'Anxiety',
  skizofrenia: 'Skizofrenia',
  kepribadian: 'Gangguan Kepribadian',
  ocd: 'OCD',
  bipolar: 'Bipolar',
  makan: 'Gangguan Makan',
  ptsd: 'PTSD'
};
export default {
  name: 'QuestionerManagement',
  components: { AdminHeader, ConfirmationModal },
  data() {
    // Inisialisasi state pertanyaan per kategori
    const questionsByCategory = {};
    Object.keys(dummyQuestions).forEach(key => {
      questionsByCategory[key] = [...dummyQuestions[key]];
    });
    return {
      mode: 'list',
      categories: [
        { name: 'Depresi', count: dummyQuestions.depresi.length, slug: 'depresi' },
        { name: 'Anxiety', count: dummyQuestions.anxiety.length, slug: 'anxiety' },
        { name: 'Skizofrenia', count: dummyQuestions.skizofrenia.length, slug: 'skizofrenia' },
        { name: 'Gangguan kepribadian', count: dummyQuestions.kepribadian.length, slug: 'kepribadian' },
        { name: 'OCD', count: dummyQuestions.ocd.length, slug: 'ocd' },
        { name: 'Bipolar', count: dummyQuestions.bipolar.length, slug: 'bipolar' },
        { name: 'Gangguan Makan', count: dummyQuestions.makan.length, slug: 'makan' },
        { name: 'PTSD', count: dummyQuestions.ptsd.length, slug: 'ptsd' },
      ],
      // Add form
      category: '',
      question: '',
      // Detail
      detailCategory: '',
      localQuestions: [],
      questionsByCategory,
      editIndex: null,
      editValue: '',
      showDeleteModal: false,
      deleteIndex: null
    };
  },
  computed: {
    categoryName() {
      return categoryNames[this.detailCategory] || this.detailCategory;
    }
  },
  methods: {
    openDetail(item) {
      this.detailCategory = item.slug;
      this.localQuestions = this.questionsByCategory[item.slug];
      this.editIndex = null;
      this.editValue = '';
      this.mode = 'detail';
    },
    // Add form
    submitForm() {
      // Tambah pertanyaan ke kategori yang dipilih
      const cat = this.categories.find(c => c.name === this.category);
      const catSlug = cat ? cat.slug : null;
      if (cat && catSlug) {
        cat.count++;
        if (!this.questionsByCategory[catSlug]) {
          this.questionsByCategory[catSlug] = [];
        }
        this.questionsByCategory[catSlug].push(this.question);
        // Jika sedang melihat detail kategori yang sama, update localQuestions
        if (this.mode === 'detail' && this.detailCategory === catSlug) {
          this.localQuestions = this.questionsByCategory[catSlug];
        }
      }
      this.category = '';
      this.question = '';
      // Setelah tambah, jika dari detail, kembali ke detail, jika dari list, kembali ke list
      if (this.detailCategory && this.mode === 'add') {
        this.mode = 'detail';
      } else {
        this.mode = 'list';
      }
    },
    addFromDetail() {
      // Set kategori otomatis sesuai detail, lalu buka form add
      const cat = this.categories.find(c => c.slug === this.detailCategory);
      this.category = cat ? cat.name : '';
      this.mode = 'add';
    },
    addFromList() {
      this.category = '';
      this.detailCategory = '';
      this.mode = 'add';
    },
    cancelAdd() {
      // Jika dari detail, kembali ke detail, jika dari list, kembali ke list
      if (this.detailCategory) {
        this.mode = 'detail';
      } else {
        this.mode = 'list';
      }
    },
    // Detail edit/delete
    startEdit(idx, val) {
      this.editIndex = idx;
      this.editValue = val;
    },
    saveEdit(idx) {
      if (this.editValue.trim()) {
        this.$set ? this.$set(this.localQuestions, idx, this.editValue) : this.localQuestions.splice(idx, 1, this.editValue);
        // Sinkronkan ke questionsByCategory
        if (this.detailCategory && this.questionsByCategory[this.detailCategory]) {
          this.questionsByCategory[this.detailCategory][idx] = this.editValue;
        }
      }
      this.editIndex = null;
      this.editValue = '';
    },
    cancelEdit() {
      this.editIndex = null;
      this.editValue = '';
    },
    confirmDelete(idx) {
      this.deleteIndex = idx;
      this.showDeleteModal = true;
    },
    deleteQuestion() {
      if (this.deleteIndex !== null) {
        // Hapus hanya dari questionsByCategory, lalu update localQuestions
        if (this.detailCategory && this.questionsByCategory[this.detailCategory]) {
          this.questionsByCategory[this.detailCategory].splice(this.deleteIndex, 1);
          this.localQuestions = [...this.questionsByCategory[this.detailCategory]];
        }
        // Kurangi jumlah pertanyaan pada kategori terkait
        const cat = this.categories.find(c => c.slug === this.detailCategory);
        if (cat) cat.count = this.questionsByCategory[this.detailCategory].length;
      }
      this.showDeleteModal = false;
      this.deleteIndex = null;
    }
  }
};
</script>

<style scoped>
/* Gabungkan semua style dari ketiga file, tidak diubah, agar tampilan tetap sama */
.admin-page-bg {
  min-height: 100vh;
  background: #faf7f3;
}
main {
  flex: 1;
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
  /* background: #fff; */
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
.questioner-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  /* background: #fff; */
  background: #faf7f3;
  font-family: inherit;
}
.questioner-table th, .questioner-table td {
  padding: 14px 10px;
  text-align: left;
  font-size: 1rem;
}
.questioner-table thead th {
  color: #b5b5b5;
  font-weight: 600;
  background: #f7f3fa;
  border-bottom: 2px solid #ede7f6;
}
.questioner-table tbody tr {
  border-bottom: 1px solid #f3f3f3;
  transition: background 0.18s;
}
.questioner-table tbody tr:hover {
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
.add-question-card-outer {
  width: 100%;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 32px;
  padding-bottom: 32px;
}
.add-question-card {
  /* background: #fff; */
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
select, textarea {
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  padding: 14px 14px;
  font-size: 1.05rem;
  font-family: inherit;
  background: #fff;
  transition: border 0.2s;
  outline: none;
}
select:focus, textarea:focus {
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
.detail-question-outer {
  width: 100%;
  max-width: 1350px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  box-sizing: border-box;
}
.detail-question-container {
  margin-top: 32px;
  /* background: #fff; */
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
.question-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.question-item {
  font-size: 1.08rem;
  color: #23263b;
  background: #faf7f3;
  border-radius: 8px;
  margin-bottom: 16px;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 1px 4px rgba(108,52,131,0.04);
}
.question-index {
  font-weight: 700;
  color: #6C3483;
  margin-right: 8px;
}
.question-content {
  flex: 1;
  min-width: 0;
  word-break: break-word;
}
.question-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
  align-items: center;
}
.question-actions .btn-save-edit,
.question-actions .btn-cancel-edit {
  margin-left: 0;
}
.edit-input {
  width: 100%;
  font-size: 1.08rem;
  padding: 8px 12px;
  border: 1.5px solid #e0e0e0;
  border-radius: 6px;
  margin-right: 0;
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
@media (max-width: 900px) {
  .questioner-table-outer, .detail-question-outer {
    padding-left: 8px;
    padding-right: 8px;
  }
  .questioner-table-container, .detail-question-container {
    padding: 16px 2px 10px 2px;
  }
  .table-title, .detail-title {
    font-size: 1.08rem;
  }
  .questioner-table th, .questioner-table td {
    padding: 8px 4px;
    font-size: 0.92rem;
  }
  .question-item {
    font-size: 0.97rem;
    padding: 10px 8px;
  }
}
</style> 