<template>
  <div class="questioner-table-outer">
    <div class="questioner-table-container">
      <div class="questioner-header">
        <div class="table-title">Daftar Forum Diskusi</div>
      </div>
      <div class="table-wrapper" v-if="mode === 'list'">
        <table class="questioner-table">
          <thead>
            <tr>
              <th>Kategori</th>
              <th>Jumlah Pertanyaan</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cat in categoriesWithCount" :key="cat.name">
              <td>{{ cat.name }}</td>
              <td>{{ cat.count }}</td>
              <td>
                <button class="btn-detail" @click="openDetail(cat)" title="Detail"><span class="arrow">→</span></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="mode === 'detail'">
        <div class="detail-header">
          <button class="btn-back" @click="mode = 'list'">← Kembali</button>
          <h2 class="detail-title">Daftar Pertanyaan - {{ selectedCategory.name }}</h2>
        </div>
        <div class="search-row">
          <input class="search-input" v-model="searchQuery" placeholder="Cari pertanyaan..." />
        </div>
        <ul class="question-list">
          <li v-for="(q, i) in filteredQuestions" :key="q.id" class="question-item">
            <span class="question-index">{{ i + 1 }}.</span>
            <div class="question-content">
              <div class="question-title">{{ q.title }}</div>
              <div class="question-user">oleh {{ q.user }}</div>
            </div>
            <div class="question-actions">
              <button class="btn-lihat" @click="openQuestionDetail(q)">Lihat</button>
              <button class="btn-delete" @click="confirmDelete(q)">Hapus</button>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal Detail Pertanyaan -->
    <div v-if="showDetail" class="modal-backdrop" @click.self="closeDetail">
      <div class="modal-detail">
        <h3>Detail Pertanyaan</h3>
        <div class="detail-question-title">{{ selectedQuestion.title }}</div>
        <div class="detail-question-meta">oleh {{ selectedQuestion.user }}</div>
        <div class="detail-question-desc">{{ selectedQuestion.desc }}</div>
        <div><b>Jawaban:</b></div>
        <ul class="detail-answers-list">
          <li v-for="ans in selectedQuestion.answers" :key="ans.id" class="detail-answer-item">
            <span class="detail-answer-user">{{ ans.user }}</span>
            <span class="detail-answer-time">{{ ans.time }}</span>
            <div class="detail-answer-text">{{ ans.text }}</div>
          </li>
          <li v-if="!selectedQuestion.answers || selectedQuestion.answers.length === 0" style="color:#888">Belum ada jawaban.</li>
        </ul>
        <button class="btn-cancel-edit" @click="closeDetail">Tutup</button>
      </div>
    </div>

    <!-- Konfirmasi Hapus -->
    <ConfirmationModal
      :visible="showDeleteConfirm"
      title="Konfirmasi Hapus"
      message="Yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan."
      @confirm="deleteQuestion"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
export default {
  name: 'ForumTable',
  components: { ConfirmationModal },
  data() {
    return {
      mode: 'list',
      categories: [
        {
          name: 'Depresi',
          questions: []
        },
        {
          name: 'Anxiety',
          questions: [
            {
              id: 3,
              title: 'I want to study Svelte JS Framework. What is the best resource should I use?',
              user: 'AizhanMaratovna',
              desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequat aliquet maecenas ut sit nulla',
              answers: [
                { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
              ]
            }
          ]
        },
        {
          name: 'Skizofrenia',
          questions: []
        },
        {
          name: 'Bipolar',
          questions: []
        },
        {
          name: 'Personality Disorders',
          questions: []
        },
        {
          name: 'Obsessif-Kompulsif (OCD)',
          questions: [
            {
              id: 2,
              title: 'Ada yang pernah kena ocd ga?',
              user: 'Linuxoid',
              desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Bibendum vitae etiam lectus amet enim.',
              answers: [
                { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
              ]
            }
          ]
        },
        {
          name: 'Eating Disorders',
          questions: []
        },
        {
          name: 'PTSD',
          questions: [
            {
              id: 1,
              title: 'Gimana caranya mengatasi masalah PTSD?',
              user: 'Golanginya',
              desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequat aliquet maecenas ut sit nulla',
              answers: [
                { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ornare rutrum amet, a nunc mi lacinia in iaculis. Pharetra ut integer nibh urna. Placerat ut adipiscing nulla lectus vulputate massa, scelerisque. Netus nisl nulla placerat dignissim ipsum arcu.' },
                { id: 2, user: 'morgenshern', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ornare rutrum amet, a nunc mi lacinia in iaculis. Pharetra ut integer nibh urna. Placerat ut adipiscing nulla lectus vulputate massa, scelerisque. Netus nisl nulla placerat dignissim ipsum arcu.' },
                { id: 3, user: 'kizaru', time: '12 November 2020 19:35', text: 'Mi ac id faucibus laoreet. Nulla quis in interdum imperdiet. Lacus mollis massa netus.' },
              ]
            }
          ]
        }
      ],
      selectedCategory: {},
      showDetail: false,
      selectedQuestion: {},
      showDeleteConfirm: false,
      questionToDelete: null,
      searchQuery: '',
    };
  },
  computed: {
    // Hitung jumlah pertanyaan per kategori
    categoriesWithCount() {
      return this.categories.map(cat => ({ ...cat, count: cat.questions.length }));
    },
    filteredQuestions() {
      if (!this.selectedCategory.questions) return [];
      if (!this.searchQuery.trim()) return this.selectedCategory.questions;
      const q = this.searchQuery.trim().toLowerCase();
      return this.selectedCategory.questions.filter(item =>
        item.title.toLowerCase().includes(q) ||
        (item.user && item.user.toLowerCase().includes(q))
      );
    },
  },
  methods: {
    openDetail(cat) {
      this.selectedCategory = cat;
      this.mode = 'detail';
    },
    openQuestionDetail(q) {
      this.selectedQuestion = q;
      this.showDetail = true;
    },
    closeDetail() {
      this.showDetail = false;
      this.selectedQuestion = {};
    },
    confirmDelete(q) {
      this.questionToDelete = q;
      this.showDeleteConfirm = true;
    },
    deleteQuestion() {
      if (this.selectedCategory && this.selectedCategory.questions) {
        this.selectedCategory.questions = this.selectedCategory.questions.filter(q => q !== this.questionToDelete);
        this.selectedCategory.count = this.selectedCategory.questions.length;
      }
      this.showDeleteConfirm = false;
      this.questionToDelete = null;
      if (this.showDetail) this.closeDetail();
    }
  }
};
</script>

<style scoped>
.questioner-table-outer {
  width: 100%;
  max-width: 1280px;
  margin: 32px auto 0 auto;
  padding: 0 32px;
}
.questioner-table-container {
  background: #faf7f3;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.07);
  padding: 32px 24px 24px 24px;
}
.table-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #2d2350;
  margin-bottom: 18px;
}
.table-wrapper {
  overflow-x: auto;
}
table.questioner-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #faf7f3;
  font-family: inherit;
}
th, td {
  padding: 18px 20px;
  font-size: 1.01rem;
  background: transparent;
}
th {
  background: #faf7fc;
  color: #b5b5b5;
  font-weight: 600;
  font-size: 1.08rem;
  letter-spacing: 0.2px;
  border: none;
  text-align: left;
  border-bottom: 2px solid #ede7f6;
}
th:first-child {
  border-top-left-radius: 0;
}
th:last-child {
  border-top-right-radius: 0;
}
tbody tr {
  transition: background 0.18s;
  background: none !important;
}
td {
  color: #23263b;
  vertical-align: middle;
  border: none;
}
td:nth-child(2) {
  color: #4B2E7A;
  font-size: 1.08rem;
}
td:last-child {
  text-align: center;
}
.btn-detail {
  background: #f6f1fa;
  color: #6C3483;
  border: none;
  border-radius: 50%;
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  box-shadow: 0 1px 4px rgba(108,52,131,0.08);
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  margin: 0 auto;
}
.btn-detail:hover {
  background: #e5e0f7;
  color: #51236a;
  box-shadow: 0 2px 8px rgba(108,52,131,0.13);
}
.arrow {
  font-size: 1.1rem;
  font-weight: bold;
}
@media (max-width: 900px) {
  .questioner-table-container {
    padding: 8px 2px 2px 2px;
  }
  th, td {
    padding: 8px 4px;
    font-size: 0.92rem;
  }
  .btn-detail {
    font-size: 0.92rem;
    width: 28px;
    height: 28px;
  }
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
  background: #fff;
  border-radius: 10px;
  margin-bottom: 10px;
  padding: 10px 8px;
  display: flex;
  align-items: flex-start;
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
.question-title {
  font-weight: 700;
  font-size: 0.97rem;
  color: #23263b;
  margin-bottom: 2px;
}
.question-user {
  font-size: 0.92rem;
  color: #b5b5b5;
  margin-bottom: 2px;
}
.question-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
  align-items: center;
}
.btn-lihat {
  background: #ede7f6;
  color: #6C3483;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-size: 0.92rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-lihat:hover {
  background: #d1c4e9;
  color: #51236a;
}
.btn-delete {
  background: #f87171;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 5px 12px;
  font-size: 0.92rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-delete:hover {
  background: #ef4444;
}
@media (max-width: 900px) {
  .question-item {
    padding: 6px 2px;
    font-size: 0.89rem;
  }
  .question-title {
    font-size: 0.89rem;
  }
  .btn-detail, .btn-delete {
    font-size: 0.92rem;
    padding: 6px 10px;
    width: 28px;
    height: 28px;
  }
}
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(40,30,60,0.18);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-detail, .modal-confirm {
  background: #fff;
  padding: 36px 32px 28px 32px;
  border-radius: 18px;
  box-shadow: 0 2px 16px rgba(80,60,120,0.13);
  min-width: 340px;
  max-width: 95vw;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-detail h3 {
  margin-top: 0;
  color: #6a4c9b;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 18px;
}
.detail-question-title {
  font-size: 1.18rem;
  font-weight: 700;
  color: #23263b;
  margin-bottom: 8px;
}
.detail-question-meta {
  font-size: 0.97rem;
  color: #b5b5b5;
  margin-bottom: 12px;
}
.detail-question-desc {
  font-size: 1.04rem;
  color: #444;
  margin-bottom: 18px;
  white-space: pre-line;
}
.detail-answers-list {
  margin-top: 18px;
  padding-left: 0;
  list-style: none;
}
.detail-answer-item {
  background: #faf7f3;
  border-radius: 10px;
  padding: 12px 16px;
  margin-bottom: 10px;
}
.detail-answer-user {
  font-weight: 600;
  color: #6C3483;
  font-size: 0.98rem;
}
.detail-answer-time {
  font-size: 0.92rem;
  color: #b5b5b5;
  margin-left: 8px;
}
.detail-answer-text {
  font-size: 1.01rem;
  color: #23263b;
  margin-top: 2px;
}
.btn-cancel-edit {
  background: #a94442;
  color: #fff;
  border-radius: 8px;
  padding: 10px 24px;
  font-size: 1rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
  margin-top: 18px;
}
.btn-cancel-edit:hover {
  background: #922b21;
}
.search-row {
  margin-bottom: 16px;
  display: flex;
  justify-content: flex-end;
}
.search-input {
  padding: 8px 16px;
  border-radius: 8px;
  border: 1.5px solid #e0e0e0;
  font-size: 0.97rem;
  width: 260px;
  max-width: 100%;
  outline: none;
  transition: border 0.2s;
}
.search-input:focus {
  border-color: #6C3483;
}
</style> 