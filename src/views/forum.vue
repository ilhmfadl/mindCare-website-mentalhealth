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
          <div class="sidebar-avatar" @click="$router.push('/profile')">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" fill="#D1D5DB"/><ellipse cx="12" cy="17" rx="7" ry="5" fill="#D1D5DB"/></svg>
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
          <button class="ask-btn" @click="openAsk">tanya?</button>
        </div>

        <div class="forum-questions-list">
          <div
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
              </div>
              <span v-if="question.user === user.name" class="question-menu" @click.stop="toggleMenu(question.id)">⋮
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
              <span class="question-comments"><svg width="16" height="16" fill="none" stroke="#b6a7d6" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> {{ question.comments }}</span>
            </div>
          </div>
        </div>
      </main>

      <!-- Featured Links -->
      <aside class="forum-featured-links">
        <h4>Featured links</h4>
        <ul>
          <li><a href="#">Cari info yang lebih akurat melalui jurnal!</a></li>
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
              <button class="response-btn" @click="submitResponse">Respon</button>
            </div>
          </div>
          <div class="detail-answers">
            <div v-for="answer in selectedQuestion.answers" :key="answer.id" class="detail-answer">
              <div class="answer-header">
                <span class="answer-user">@{{ answer.user }}</span>
                <span class="answer-time">{{ answer.time }}</span>
              </div>
              <div class="answer-body">{{ answer.text }}</div>
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
          <h2>Buat Pertanyaan Baru</h2>
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
              <button type="submit" class="response-btn">Kirim</button>
            </div>
          </form>
        </div>
        <div class="forum-ask-backdrop" @click="closeAsk"></div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
const user = ref({
  name: 'NamaUserDemo',
  avatar: 'https://randomuser.me/api/portraits/lego/1.jpg',
});

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

const questions = [
  {
    id: 1,
    user: 'Golanginya',
    avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
    time: '8 mins ago',
    title: 'Gimana caranya mengatasi masalah PTSD?',
    desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequat aliquet maecenas ut sit nulla',
    fullDesc: 'Mi magna sed nec nisl mattis. Magna cursus tincidunt rhoncus imperdiet fermentum pretium, pharetra nisl. Euismod.\n\nPosuere arcu arcu consectetur turpis rhoncus tellus. Massa, consectetur massa sit fames nulla eu vehicula ullamcorper. Ante sit mauris elementum sollicitudin arcu sit suspendisse pretium. Nisl egestas fringilla justo bibendum.',
    tag: 'PTSD',
    comments: 15,
    answers: [
      { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ornare rutrum amet, a nunc mi lacinia in iaculis. Pharetra ut integer nibh urna. Placerat ut adipiscing nulla lectus vulputate massa, scelerisque. Netus nisl nulla placerat dignissim ipsum arcu.' },
      { id: 2, user: 'morgenshern', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ornare rutrum amet, a nunc mi lacinia in iaculis. Pharetra ut integer nibh urna. Placerat ut adipiscing nulla lectus vulputate massa, scelerisque. Netus nisl nulla placerat dignissim ipsum arcu.' },
      { id: 3, user: 'kizaru', time: '12 November 2020 19:35', text: 'Mi ac id faucibus laoreet. Nulla quis in interdum imperdiet. Lacus mollis massa netus.' },
    ],
  },
  {
    id: 2,
    user: 'Linuxoid',
    avatar: 'https://randomuser.me/api/portraits/men/45.jpg',
    time: '25 mins ago',
    title: 'Ada yang pernah kena ocd ga?',
    desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Bibendum vitae etiam lectus amet enim.',
    fullDesc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Bibendum vitae etiam lectus amet enim.\n\nPosuere arcu arcu consectetur turpis rhoncus tellus. Massa, consectetur massa sit fames nulla eu vehicula ullamcorper.',
    tag: 'OCD',
    comments: 15,
    answers: [
      { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
    ],
  },
  {
    id: 3,
    user: 'AizhanMaratovna',
    avatar: 'https://randomuser.me/api/portraits/women/65.jpg',
    time: '2 days ago',
    title: 'I want to study Svelte JS Framework. What is the best resource should I use?',
    desc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequat aliquet maecenas ut sit nulla',
    fullDesc: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequat aliquet maecenas ut sit nulla',
    tag: 'Anxiety',
    comments: 15,
    answers: [
      { id: 1, user: 'unkind', time: '12 November 2020 19:35', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
    ],
  },
];

const showDetail = ref(false);
const selectedQuestion = ref({});

function openDetail(question) {
  selectedQuestion.value = question;
  showDetail.value = true;
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
  showAsk.value = true;
}
const editQuestionId = ref(null);
function editQuestion(question) {
  askForm.value = {
    title: question.title,
    desc: question.desc,
    tag: question.tag,
  };
  editQuestionId.value = question.id;
  showAsk.value = true;
  showMenuId.value = null;
}
function closeAsk() {
  showAsk.value = false;
  askForm.value = { title: '', desc: '', tag: categories[0] };
  editQuestionId.value = null;
}
function submitAsk() {
  const now = new Date();
  if (editQuestionId.value) {
    // Edit mode: update pertanyaan yang ada
    const idx = questions.findIndex(q => q.id === editQuestionId.value);
    if (idx !== -1) {
      questions[idx].title = askForm.value.title;
      questions[idx].desc = askForm.value.desc;
      questions[idx].fullDesc = askForm.value.desc;
      questions[idx].tag = askForm.value.tag;
    }
    editQuestionId.value = null;
  } else {
    // Tambah baru
    questions.unshift({
      id: Date.now(),
      user: user.value.name, // Dummy user
      avatar: user.value.avatar, // Dummy avatar
      time: now.toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'long', year: 'numeric' }),
      title: askForm.value.title,
      desc: askForm.value.desc,
      fullDesc: askForm.value.desc, // Gunakan deskripsi singkat juga untuk fullDesc agar detail tetap muncul
      tag: askForm.value.tag,
      comments: 0,
      answers: [],
    });
  }
  closeAsk();
}

const responseText = ref('');
function submitResponse() {
  if (!responseText.value.trim()) return;
  if (!selectedQuestion.value.answers) selectedQuestion.value.answers = [];
  selectedQuestion.value.answers.unshift({
    id: Date.now(),
    user: 'NamaUserDemo', // Dummy user
    time: new Date().toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'long', year: 'numeric' }),
    text: responseText.value,
  });
  selectedQuestion.value.comments = selectedQuestion.value.answers.length;
  responseText.value = '';
}

const showMenuId = ref(null);
function toggleMenu(id) {
  showMenuId.value = showMenuId.value === id ? null : id;
}
function deleteQuestion(question) {
  const idx = questions.findIndex(q => q.id === question.id);
  if (idx !== -1) questions.splice(idx, 1);
  showMenuId.value = null;
}

const filterType = ref('terbaru');
const showMyQuestions = ref(false);
function toggleMyQuestions() {
  showMyQuestions.value = !showMyQuestions.value;
}

const filteredQuestions = computed(() => {
  let result = questions;
  if (showMyQuestions.value) {
    result = result.filter(q => q.user === user.value.name);
  }
  if (selectedCategory.value !== 'Semua') {
    result = result.filter(q => q.tag === selectedCategory.value);
  }
  if (searchQuery.value.trim() !== '') {
    const q = searchQuery.value.trim().toLowerCase();
    result = result.filter(
      item => item.title.toLowerCase().includes(q) || item.desc.toLowerCase().includes(q)
    );
  }
  if (filterType.value === 'terbaru') {
    result = [...result].sort((a, b) => b.id - a.id);
  } else {
    result = [...result].sort((a, b) => a.id - b.id);
  }
  return result;
});
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
.sidebar-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(160, 130, 255, 0.10);
  cursor: pointer;
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
  align-items: relative;
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
.ask-btn {
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
.ask-btn:hover {
  background: #d45d2c;
  box-shadow: 0 4px 16px rgba(236, 116, 74, 0.13);
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
  transition: color 0.2s;
}
.forum-featured-links a:hover {
  color: #1d4ed8;
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
  .forum-sidebar, .forum-featured-links {
    padding: 20px 2px;
    margin-top: 60px;
    margin-bottom: 0px;
    min-width: 0;
    width: 100%;
    font-size: 0.85rem;
  }
  .sidebar-avatar {
    width: 32px;
    height: 32px;
  }
  .sidebar-categories h3 {
    font-size: 0.9rem;
    margin-bottom: 4px;
  }
  .sidebar-categories li {
    font-size: 0.85rem;
    padding: 4px 8px;
    margin-bottom: 2px;
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
.detail-answers {
  margin-top: 18px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.detail-answer {
  background: #f7f5f9;
  border-radius: 10px;
  padding: 14px 18px;
}
.answer-header {
  font-weight: 600;
  color: #6a4c9b;
  font-size: 1rem;
  margin-bottom: 4px;
  display: flex;
  gap: 10px;
}
.answer-time {
  font-size: 0.92rem;
  color: #b6a7d6;
  font-weight: 400;
}
.answer-body {
  font-size: 1rem;
  color: #444;
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
</style>
