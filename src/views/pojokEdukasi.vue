<template>
  <div class="edukasi-bg">
    <div class="edukasi-container edukasi-title-container">
      <h1 class="edukasi-title">Pojok Edukasi</h1>
    </div>
    <!-- Kategori-section keluar dari .edukasi-container agar full-bleed -->
    <div v-for="(item, idx) in topics" :key="item.title" :class="['kategori-section', item.bgClass, { 
      'kategori-section--top': idx === 0, 
      'kategori-section--right': idx % 2 === 1, 
      'kategori-section--bottom': idx === topics.length - 1,
      'kategori-section--title-up': ['PTSD','OCD','Anxiety','Schizophrenia'].includes(item.title)
    }]">
      <router-link v-if="idx === 0" :to="'/edukasi/neurosis'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">Neurosis</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 1" :to="'/edukasi/psikotik'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">Psikotik</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 2" :to="'/edukasi/ptsd'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <div v-else class="kategori-content">
        <div class="kategori-bg-title">{{ item.title }}</div>
        <div class="kategori-img-wrapper">
          <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
        </div>
        <div class="kategori-desc">{{ item.desc }}</div>
      </div>
    </div>
    <!-- Judul Ruang Artikel -->
    <div class="ruang-artikel-title-container" id="ruang-artikel">
      <h2 class="ruang-artikel-title">Ruang Artikel</h2>
    </div>
    <div class="journal-page-layout">
      <!-- Sidebar -->
      <aside class="forum-sidebar">
        <div class="sidebar-categories">
          <h3>Kategori</h3>
          <select class="dropdown-categories" v-model="selectedCategory">
            <option value="Semua">Semua</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
          <ul>
            <li :class="{ active: selectedCategory === 'Semua' }" @click="selectedCategory = 'Semua'">Semua</li>
            <li v-for="cat in categories" :key="cat" :class="{ active: selectedCategory === cat }" @click="selectedCategory = cat">{{ cat }}</li>
          </ul>
        </div>
      </aside>
      <!-- Main Content -->
      <main class="forum-content">
        <div class="forum-search-row">
          <div class="forum-search-box">
            <input type="text" placeholder="Masukkan jurnal yang ingin anda cari" v-model="journalSearch" />
            <span class="search-icon">
              <svg width="20" height="20" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
          </div>
        </div>
        <div class="forum-questions-list">
          <div v-if="loading" class="journal-empty">Memuat jurnal...</div>
          <div v-else-if="error" class="journal-empty">{{ error }}</div>
          <div v-else-if="filteredJournals.length === 0" class="journal-empty">Tidak ada jurnal ditemukan.</div>
          <div v-for="journal in filteredJournals" :key="journal.id" class="forum-question-card">
            <div class="question-body">
              <div class="question-title">{{ journal.title }}</div>
              <div class="question-desc">{{ journal.summary }}</div>
              <a v-if="journal.link" :href="journal.link" target="_blank" class="baca-selengkapnya-btn">Baca Selengkapnya</a>
              <span v-else class="baca-selengkapnya-btn disabled">Link tidak tersedia</span>
            </div>
            <div class="question-footer">
              <span class="question-tag">{{ journal.category }}</span>
            </div>
          </div>
        </div>
      </main>
      <!-- Featured Links -->
      <aside class="forum-featured-links">
        <h4>Featured links</h4>
        <ul>
          <li><a href="#" @click="goToForumDiskusi">Tanyakan kepada orang yang memiliki kendala yang sama dengan anda!</a></li>
          <li><a href="#" @click="openChat">konsul dengan ahlinya</a></li>
        </ul>
      </aside>
    </div>
    
    <!-- Chat Popup Component -->
    <ChatPopup ref="chatPopup" />

  </div>
</template>

<script>
import DepresiImg from '../assets/Depresi.png'
import AnxietyImg from '../assets/anxiety.png'
import PTSDImg from '../assets/PTSD.png'
import ChatPopup from '../components/ChatPopup.vue'
import axios from 'axios';

export default {
  name: 'PojokEdukasi',
  components: {
    ChatPopup
  },
  mounted() {
    // Fetch journals from database
    this.fetchJournals();
    
    // Handle URL parameters for category filtering
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    
    if (categoryParam) {
      // Map URL parameter to category name
      const categoryMap = {
        'ptsd': 'PTSD',
        'neurosis': 'Neurosis', 
        'psychotic': 'Psikotik'
      };
      
      const mappedCategory = categoryMap[categoryParam];
      if (mappedCategory) {
        this.selectedCategory = mappedCategory;
        console.log('📍 Category filter set to:', mappedCategory);
        
        // Scroll to ruang artikel after a short delay
        setTimeout(() => {
          const element = document.getElementById('ruang-artikel');
          if (element) {
            console.log('📍 Scrolling to ruang artikel');
            element.scrollIntoView({ 
              behavior: 'smooth',
              block: 'start'
            });
          }
        }, 500);
      }
    } else {
      // Handle anchor scroll to ruang artikel (existing logic)
    console.log('📍 PojokEdukasi mounted, checking for anchor...');
    console.log('📍 Current hash:', window.location.hash);
    
    if (window.location.hash === '#ruang-artikel') {
      console.log('📍 Found ruang-artikel anchor, scrolling...');
      setTimeout(() => {
        const element = document.getElementById('ruang-artikel');
        if (element) {
          console.log('📍 Element found, scrolling to ruang artikel');
          element.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
          });
        } else {
          console.log('❌ Element not found');
        }
        }, 300);
    } else {
      console.log('📍 No ruang-artikel anchor found');
    }
    }
    
    // Handle scroll position saving
    const saved = sessionStorage.getItem('pojokEdukasiScroll');
    if (saved) {
      window.scrollTo(0, parseInt(saved, 1));
    }
    window.addEventListener('scroll', this.saveScroll);
  },
  data() {
    return {
      topics: [
        {
          title: 'Neurosis',
          desc: 'Gangguan mental ringan seperti kecemasan, fobia, atau depresi ringan yang memengaruhi pikiran dan perilaku, namun tidak sampai kehilangan kontak dengan realita.',
          img: DepresiImg,
          bgClass: 'bg-depresi',
          colorClass: 'color-depresi',
        },
        {
          title: 'Psikotik',
          desc: 'Gangguan mental berat seperti halusinasi, delusi, atau skizofrenia yang menyebabkan seseorang kehilangan kontak dengan realita.',
          img: AnxietyImg,
          bgClass: 'bg-anxiety',
          colorClass: 'color-anxiety',
        },
        {
          title: 'PTSD',
          desc: 'Gangguan setelah mengalami peristiwa traumatis, seperti kecelakaan, bencana, atau kekerasan, yang menyebabkan kilas balik, mimpi buruk, dan kecemasan berat.',
          img: PTSDImg,
          bgClass: 'bg-ptsd',
          colorClass: 'color-ptsd',
        },
      ],
      journalSearch: '',
      selectedCategory: 'Semua',
      categories: [
        'Neurosis',
        'Psikotik',
        'PTSD',
      ],
      journals: [],
      loading: false,
      error: ''
    }
  },
  computed: {
    filteredJournals() {
      let filtered = this.journals;
      if (this.selectedCategory !== 'Semua') {
        filtered = filtered.filter(j => j.category === this.selectedCategory);
      }
      if (this.journalSearch) {
        const search = this.journalSearch.toLowerCase();
        filtered = filtered.filter(journal =>
          journal.title.toLowerCase().includes(search) ||
          journal.summary.toLowerCase().includes(search)
        );
      }
      return filtered;
    }
  },

  beforeDestroy() {
    window.removeEventListener('scroll', this.saveScroll);
  },
  methods: {
    async fetchJournals() {
      this.loading = true;
      this.error = '';
      try {
        const response = await axios.get('https://mindcareindependent.com/api/get_journals.php');
        if (response.data.success) {
          // Transform database data to match frontend structure
          this.journals = response.data.data.journals.map(journal => ({
            id: journal.id,
            title: journal.judul,
            summary: journal.kutipan,
            category: this.mapCategory(journal.kategori),
            source: journal.sumber,
            created_at: journal.created_at,
            updated_at: journal.updated_at,
            link: journal.link // Add the link field
          }));
          console.log('Journals loaded:', this.journals);
        } else {
          this.error = 'Gagal memuat jurnal';
          console.error('Failed to load journals:', response.data.message);
        }
      } catch (error) {
        this.error = 'Terjadi kesalahan saat memuat jurnal';
        console.error('Error fetching journals:', error);
      } finally {
        this.loading = false;
      }
    },
    mapCategory(dbCategory) {
      const categoryMap = {
        'neurosis': 'Neurosis',
        'psikotik': 'Psikotik', 
        'ptsd': 'PTSD'
      };
      return categoryMap[dbCategory] || dbCategory;
    },
    saveScroll() {
      sessionStorage.setItem('pojokEdukasiScroll', window.scrollY);
    },
    goToForumDiskusi() {
      console.log('🔗 Navigating to forum diskusi...');
      console.log('Current path:', window.location.pathname);
      
      // Menggunakan router untuk navigasi ke halaman forum diskusi
      if (window.location.pathname.includes('/edukasi')) {
        // Jika di halaman edukasi, redirect ke forum
        console.log('📍 Redirecting from edukasi to forum');
        window.location.href = '/forum';
      } else {
        // Jika di halaman lain, gunakan router
        console.log('📍 Redirecting from other page to forum');
        // this.$router.push('/forum'); // Uncomment jika menggunakan Vue Router
        window.location.href = '/forum';
      }
    },
    openChat() {
      // Trigger chat popup
      if (this.$refs.chatPopup) {
        this.$refs.chatPopup.toggleChat();
      }
    }

  }
}
</script>

<style scoped>
html {
  scroll-behavior: smooth;
}

.edukasi-bg {
  background-color: var(--bg-primary);
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

.edukasi-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.edukasi-title-container {
  text-align: center;
  padding: 80px 0 40px 0;
}

.edukasi-title {
  font-size: 3rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0;
  letter-spacing: -1px;
}

.kategori-section {
  width: 100%;
  padding: 40px 0;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

.kategori-section--top {
  padding-top: 60px;
}

.kategori-section--bottom {
  padding-bottom: 80px;
}

.kategori-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 260px;
  background: var(--card-bg);
  border-radius: 32px;
  box-shadow: 0 2px 16px 0 var(--shadow-light);
  padding: 32px 24px 24px 24px;
  transition: box-shadow 0.3s, transform 0.3s, background 0.3s;
  border: none;
}

.kategori-content:hover {
  box-shadow: 0 8px 32px 0 var(--shadow-medium), 0 1.5px 8px 0 var(--shadow-light);
  transform: scale(1.03);
  background: var(--card-bg);
}

.kategori-bg-title {
  position: static;
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-primary);
  opacity: 0.95;
  background: none;
  -webkit-background-clip: unset;
  -webkit-text-fill-color: unset;
  background-clip: unset;
  text-shadow: none;
  margin-top: 32px;
  margin-bottom: 10px;
  text-align: center;
  letter-spacing: 1px;
  user-select: none;
  pointer-events: none;
}

.kategori-img-wrapper {
  width: 260px;
  height: 260px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin: 20px auto 12px auto;
  position: relative;
  z-index: 2;
}

.kategori-img-overlap {
  width: 100%;
  height: 100%;
  object-fit: contain;
  position: relative;
  z-index: 2;
  margin-top: 0;
  transition: transform 0.3s cubic-bezier(.4,2,.6,1), box-shadow 0.3s cubic-bezier(.4,2,.6,1), border-radius 0.3s cubic-bezier(.4,2,.6,1), outline 0.3s cubic-bezier(.4,2,.6,1);
}

.kategori-content:hover .kategori-img-overlap {
  transform: scale(1.08) rotate(-2deg);
  box-shadow: 0 8px 32px 0 var(--shadow-medium), 0 1.5px 8px 0 var(--shadow-light);
  border-radius: 50%;
  outline: 6px solid var(--shadow-light);
}

.kategori-nama {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--text-primary);
  text-align: center;
}

.kategori-desc {
  font-size: 1.15rem;
  color: var(--text-secondary);
  margin-top: 0;
  margin-bottom: 0;
  line-height: 1.7;
  text-align: center;
  max-width: 600px;
}

/* Neurosis - Warna Biru-Ungu Pastel untuk Depresi/Kecemasan */
.bg-depresi {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 50%, #fce4ec 100%);
}

/* Psikotik - Warna Ungu-Violet Pastel untuk Halusinasi/Delusi */
.bg-anxiety {
  background: linear-gradient(135deg, #f3e5f5 0%, #e8eaf6 50%, #e1f5fe 100%);
}

/* PTSD - Warna Orange-Kuning Pastel untuk Trauma */
.bg-ptsd {
  background: linear-gradient(135deg, #fff3e0 0%, #fff8e1 50%, #fffde7 100%);
}

/* OCD - Warna Hijau-Biru Pastel untuk Obsesi/Kompulsi */
.bg-ocd {
  background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 50%, #f9fbe7 100%);
}

/* Schizophrenia - Warna Merah-Pink Pastel untuk Gangguan Realita */
.bg-schizophrenia {
  background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 50%, #e8eaf6 100%);
}

/* Anxiety - Warna Kuning-Orange Pastel untuk Kecemasan */
.bg-anxiety-disorder {
  background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 50%, #fce4ec 100%);
}

.ruang-artikel-title-container {
  text-align: center;
  padding: 60px 0 40px 0;
  background-color: var(--bg-tertiary);
}

.ruang-artikel-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.journal-page-layout {
  display: flex;
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px 80px 24px;
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

.question-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.question-title {
  font-weight: 700;
  font-size: 1.18rem;
  margin-bottom: 8px;
  color: var(--text-primary);
}

.question-desc {
  font-size: 1.01rem;
  color: var(--text-secondary);
  margin-bottom: 12px;
  white-space: pre-line;
}

.baca-selengkapnya-btn {
  color: var(--accent-blue);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.baca-selengkapnya-btn:hover {
  color: var(--accent-blue);
  text-decoration: underline;
}

.baca-selengkapnya-btn.disabled {
  color: var(--text-muted);
  cursor: not-allowed;
}

.question-footer {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 4px;
}

.question-tag {
  background: var(--bg-tertiary);
  color: var(--accent-purple);
  font-size: 0.92rem;
  padding: 4px 14px;
  border-radius: 8px;
  font-weight: 600;
  margin-bottom: 18px;
  display: inline-block;
}

.forum-featured-links {
  flex: 0 0 200px;
  background-color: var(--card-bg);
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 12px var(--shadow-light);
  margin-top: 80px;
  height: fit-content;
}

.forum-featured-links h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 10px;
  color: var(--text-primary);
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
  color: var(--accent-blue);
  text-decoration: none;
  font-size: 0.95rem;
  line-height: 1.5;
  transition: color 0.2s;
  cursor: pointer;
}

.forum-featured-links a:hover {
  color: var(--accent-blue);
  text-decoration: underline;
}

.journal-empty {
  text-align: center;
  padding: 40px;
  color: var(--text-secondary);
  font-size: 1.1rem;
}

.dropdown-categories {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--input-bg);
  color: var(--text-primary);
  font-size: 0.95rem;
  margin-bottom: 16px;
}

@media (max-width: 900px) {
  .journal-page-layout {
    flex-direction: column;
    gap: 20px;
  }
  
  .forum-sidebar, .forum-featured-links {
    order: 2;
    margin-top: 20px;
  }
  
  .forum-content {
    order: 1;
    margin-top: 20px;
  }
  
  .edukasi-title {
    font-size: 2rem;
  }
  
  .kategori-content {
    min-height: 200px;
    padding: 20px 16px 16px 16px;
  }
  
  .kategori-img-wrapper {
    width: 200px;
    height: 200px;
  }
  
  .kategori-bg-title {
    font-size: 1.5rem;
  }
  
  .kategori-desc {
    font-size: 1rem;
  }
}
</style>
