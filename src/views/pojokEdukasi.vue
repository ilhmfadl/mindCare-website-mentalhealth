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
          <div v-for="journal in filteredJournals" :key="journal.title" class="forum-question-card">
            <div class="question-body">
              <div class="question-title">{{ journal.title }}</div>
              <div class="question-desc">{{ journal.summary }}</div>
              <button class="baca-selengkapnya-btn" type="button">Baca Selengkapnya</button>
            </div>
            <div class="question-footer">
              <span class="question-tag">{{ journal.category }}</span>
            </div>
          </div>
          <div v-if="filteredJournals.length === 0" class="journal-empty">Tidak ada jurnal ditemukan.</div>
        </div>
      </main>
      <!-- Featured Links -->
      <aside class="forum-featured-links">
        <h4>Featured links</h4>
        <ul>
          <li><a href="#">Cari info yang lebih akurat melalui jurnal!</a></li>
          <li><a href="#" @click="goToForumDiskusi">Tanyakan kepada orang yang memiliki kendala yang sama dengan anda!</a></li>
        </ul>
      </aside>
    </div>
  </div>
</template>

<script>
import DepresiImg from '../assets/Depresi.png'
import AnxietyImg from '../assets/anxiety.png'
import PTSDImg from '../assets/PTSD.png'
export default {
  name: 'PojokEdukasi',
  mounted() {
    // Handle anchor scroll to ruang artikel
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
      }, 300); // Increased timeout for better reliability
    } else {
      console.log('📍 No ruang-artikel anchor found');
    }
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
      journals: [
        { title: 'Jurnal Tentang PTSD', summary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Consequat aliquet maecenas ut sit nulla(link) read more', category: 'PTSD' },
        { title: 'Judul Jurnal', summary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Consequat aliquet maecenas ut sit nulla  (link) read more', category: 'Neurosis' },
        { title: 'Judul Jurnal', summary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Consequat aliquet maecenas ut sit nulla (link) read more', category: 'Psikotik' },
      ]
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
  mounted() {
    const saved = sessionStorage.getItem('pojokEdukasiScroll');
    if (saved) {
      window.scrollTo(0, parseInt(saved, 10));
    }
    window.addEventListener('scroll', this.saveScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.saveScroll);
  },
  methods: {
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
    }
  }
}
</script>

<style scoped>
html {
  scroll-behavior: smooth;
}

.edukasi-bg {
  min-height: 100vh;
  background-color: #faf7f3;
  padding: 0;
  position: relative;
  scroll-behavior: smooth;
  /* Subtle pattern overlay */
  background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" fill="%23f7e3f7" fill-opacity="0.12"/><circle cx="20" cy="20" r="2" fill="%23e3f7f7" fill-opacity="0.18"/></svg>');
}
.edukasi-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 0 60px 0;
}
.edukasi-container.edukasi-title-container {
  padding: 32px 0 80px 0;
}
.edukasi-title {
  margin-top: 120px;
  text-align: center;
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 2px;
  color: #4e4e4e;
  letter-spacing: 1px;
  background: none;
  -webkit-background-clip: unset;
  -webkit-text-fill-color: unset;
  background-clip: unset;
}
.kategori-svg-top, .kategori-svg-bottom {
  width: 100%;
  position: absolute;
  left: 0;
  z-index: 1;
  line-height: 0;
}
.kategori-svg-top {
  top: 0;
  transform: translateY(-100%);
}
.kategori-svg-bottom {
  bottom: 0;
  transform: translateY(100%);
}
.kategori-section {
  position: relative;
  min-height: 400px;
  padding: 60px 0 56px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  width: 100vw;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 0;
}
.kategori-section::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background: linear-gradient(120deg, rgba(255,255,255,0.12) 0%, rgba(236,116,74,0.10) 100%);
  opacity: 0.7;
}
.kategori-section--right {
  transform: translateX(-50%);
}
.kategori-section--top {
  transform: translateX(-50%);
  clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
}

.kategori-section--bottom {
  transform: translateX(-50%) !important;
  clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
}
.kategori-section--bottom .kategori-content {
  transform: none;
}
.kategori-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 260px;
  background: rgba(255,255,255,0.85); /* default fallback */
  border-radius: 32px;
  box-shadow: 0 2px 16px 0 rgba(44,62,80,0.07);
  padding: 32px 24px 24px 24px;
  transition: box-shadow 0.3s, transform 0.3s, background 0.3s;
  border: none;
}
/* Default hover effect untuk kategori yang tidak memiliki style khusus */
.kategori-content:hover {
  box-shadow: 0 8px 32px 0 rgba(44,62,80,0.18), 0 1.5px 8px 0 rgba(44,62,80,0.10);
  transform: scale(1.03);
  background: rgba(255,255,255,0.95);
}

.kategori-bg-title {
  position: static;
  font-size: 2rem;
  font-weight: 800;
  color: #555;
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
.kategori-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 260px;
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
  box-shadow: 0 8px 32px 0 rgba(44,62,80,0.18), 0 1.5px 8px 0 rgba(44,62,80,0.10);
  border-radius: 50%;
  outline: 6px solid rgba(44,62,80,0.10);
}
.kategori-nama {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #222;
  text-align: center;
}
.kategori-desc {
  font-size: 1.15rem;
  color: #444;
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
  background: linear-gradient(135deg, #ffd4a3 0%, #ffe0b3 50%, #fff2d4 100%);
}


.kategori-wave-top-sharp {
  width: 100vw;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1;
  line-height: 0;
  pointer-events: none;
}
/* Background untuk kategori konten menyesuaikan dengan warna section yang pastel */
.kategori-section.bg-depresi .kategori-content {
  background: linear-gradient(135deg, rgba(212, 227, 255, 0.9) 0%, rgba(232, 212, 255, 0.9) 100%);
  border: none;
  box-shadow: 0 4px 20px rgba(212, 227, 255, 0.10);
}

.kategori-section.bg-anxiety .kategori-content {
  background: linear-gradient(135deg, rgba(232, 212, 240, 0.9) 0%, rgba(240, 212, 255, 0.9) 100%);
  border: none;
  box-shadow: 0 4px 20px rgba(232, 212, 240, 0.10);
}

.kategori-section.bg-ptsd .kategori-content {
  background: linear-gradient(135deg, rgba(255, 212, 163, 0.9) 0%, rgba(255, 224, 179, 0.9) 50%, rgba(255, 242, 212, 0.9) 100%);
  border: none;
  box-shadow: 0 4px 20px rgba(255, 212, 163, 0.10);
}

/* Hover effects untuk setiap kategori yang pastel */
.kategori-section.bg-depresi .kategori-content:hover {
  background: linear-gradient(135deg, rgba(212, 227, 255, 0.95) 0%, rgba(232, 212, 255, 0.95) 100%);
  border: none;
  box-shadow: 0 8px 30px rgba(212, 227, 255, 0.15);
  transform: scale(1.02);
}

.kategori-section.bg-anxiety .kategori-content:hover {
  background: linear-gradient(135deg, rgba(232, 212, 240, 0.95) 0%, rgba(240, 212, 255, 0.95) 100%);
  border: none;
  box-shadow: 0 8px 30px rgba(232, 212, 240, 0.15);
  transform: scale(1.02);
}

.kategori-section.bg-ptsd .kategori-content:hover {
  background: linear-gradient(135deg, rgba(255, 212, 163, 0.95) 0%, rgba(255, 224, 179, 0.95) 50%, rgba(255, 242, 212, 0.95) 100%);
  border: none;
  box-shadow: 0 8px 30px rgba(255, 212, 163, 0.15);
  transform: scale(1.02);
}

/* Fallback untuk kategori lain */
.kategori-section:nth-child(1) .kategori-content { border-color: #ec744a; }
.kategori-section:nth-child(2) .kategori-content { border-color: #a78bfa; }
.kategori-section:nth-child(3) .kategori-content { border-color: #4db6ac; }
.kategori-section:nth-child(4) .kategori-content { border-color: #fbbf24; }
.kategori-section:nth-child(5) .kategori-content { border-color: #ef4444; }
.kategori-section:nth-child(6) .kategori-content { border-color: #8b5cf6; }
.kategori-section:nth-child(7) .kategori-content { border-color: #f87171; }
.kategori-section:nth-child(8) .kategori-content { border-color: #81c784; }
@media (max-width: 900px) {
  .edukasi-container {
    padding: 16px 0 24px 0;
    max-width: 100%;
  }
  .edukasi-title {
    font-size: 1.3rem;
    margin-top: 60px;
    margin-bottom: 8px;
  }
  .kategori-section {
    padding: 18px 0 12px 0;
    min-height: 220px;
  }
  .kategori-content {
    padding: 16px 8px 12px 8px;
    border-radius: 16px;
    min-height: 120px;
    font-size: 0.95rem;
  }
  
  /* Mempertahankan warna background di mobile yang pastel */
  .kategori-section.bg-depresi .kategori-content {
    background: linear-gradient(135deg, rgba(212, 227, 255, 0.9) 0%, rgba(232, 212, 255, 0.9) 100%);
    border: none;
    box-shadow: 0 2px 10px rgba(212, 227, 255, 0.10);
  }
  
  .kategori-section.bg-anxiety .kategori-content {
    background: linear-gradient(135deg, rgba(232, 212, 240, 0.9) 0%, rgba(240, 212, 255, 0.9) 100%);
    border: none;
    box-shadow: 0 2px 10px rgba(232, 212, 240, 0.10);
  }
  
  .kategori-section.bg-ptsd .kategori-content {
    background: linear-gradient(135deg, rgba(255, 212, 163, 0.9) 0%, rgba(255, 224, 179, 0.9) 50%, rgba(255, 242, 212, 0.9) 100%);
    border: none;
    box-shadow: 0 2px 10px rgba(255, 212, 163, 0.10);
  }
  .kategori-bg-title {
    font-size: 1.1rem;
    margin-top: 16px;
    margin-bottom: 6px;
  }
  .kategori-img-wrapper {
    width: 90px;
    height: 90px;
    margin: 10px auto 8px auto;
  }
  .kategori-desc {
    font-size: 0.95rem;
    max-width: 98vw;
  }
  .edukasi-map-section {
    padding: 12px 4px 12px 4px;
    border-radius: 12px;
    margin: 24px 4px 0 4px;
    max-width: 100vw;
  }
  .map-title {
    font-size: 1.05rem;
    margin-bottom: 10px;
  }
  .map-container {
    border-radius: 8px;
  }
}


.journal-page-layout {
  display: flex;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 40px 40px 40px;
  gap: 32px;
  position: relative;
  z-index: 2;
}
.ruang-artikel-title-container {
  padding: 32px 0 80px 0;
  text-align: center;
  scroll-margin-top: 20px; /* Add scroll margin for better positioning */
}
.ruang-artikel-title {
  margin-top: 120px;
  text-align: center;
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 2px;
  color: #4e4e4e;
  letter-spacing: 1px;
  background: none;
  -webkit-background-clip: unset;
  -webkit-text-fill-color: unset;
  background-clip: unset;
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
  margin-top: 40px;
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
  margin-top: 40px;
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
  padding: 18px 56px 18px 24px;
  border-radius: 18px;
  border: 2px solid #b6a7d6;
  background: #fff;
  font-size: 1.13rem;
  color: #6a4c9b;
  outline: none;
  box-shadow: 0 2px 8px rgba(160, 130, 255, 0.08);
  font-weight: 500;
  transition: border 0.2s, box-shadow 0.2s;
}
.forum-search-box input:focus {
  border: 2px solid #8b5cf6;
  box-shadow: 0 4px 16px rgba(160, 130, 255, 0.13);
}
.search-icon {
  position: absolute;
  right: 22px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  font-size: 1.7rem;
  color: #8b5cf6;
  display: flex;
  align-items: center;
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
.journal-empty {
  text-align: center;
  color: #b0a9d6;
  font-size: 1.01rem;
  margin-top: 18px;
}
.forum-featured-links {
  flex: 0 0 220px;
  background: #f7f5f9;
  border-radius: 18px;
  padding: 28px 18px;
  box-shadow: 0 2px 8px rgba(160, 130, 255, 0.07);
  margin-top: 40px;
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
.baca-selengkapnya-btn {
  background: none;
  color: #8b5cf6;
  border: none;
  font-weight: 600;
  font-size: 1.01rem;
  cursor: pointer;
  padding: 0;
  margin-bottom: 8px;
  text-decoration: underline;
  transition: color 0.2s;
}
.baca-selengkapnya-btn:hover {
  color: #222;
}
@media (max-width: 1100px) {
  .journal-page-layout {
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
  .journal-page-layout {
    padding: 0 4px 24px 4px;
  }
  .ruang-artikel-title {
    font-size: 1.8rem;
    margin-top: 80px;
    padding: 0 16px;
  }
  .ruang-artikel-title-container {
    padding: 24px 0 40px 0;
  }
  .forum-sidebar, .forum-featured-links {
    padding: 16px 8px;
  }
  .forum-question-card {
    padding: 14px 8px 12px 8px;
  }
  .forum-search-row {
    flex-direction: column;
    gap: 10px;
  }
  .forum-featured-links {
    margin-top: 12px;
  }
  .sidebar-categories ul {
    display: none !important;
  }
  .dropdown-categories {
    display: block !important;
    width: 100%;
    padding: 7px 10px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 0.95rem;
    margin-bottom: 8px;
    background: #faf7f3;
    color: #6a4c9b;
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
</style>
