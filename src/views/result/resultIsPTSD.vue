<template>
  <div class="result-page">
    <section class="result-hero ptsd-bg">
      <div class="result-container">
        <div class="result-content">
          <div class="title-block">
            <span class="title-line"></span>
            <h2>HASIL TES KAMU</h2>
          </div>
          <p class="subtitle">Gangguan Kesehatan Mental yang paling dominan Kamu alami saat ini adalah:</p>
          <div class="diagnosis-card">
            <h3>{{ diagnosis.name }}</h3>
            <div class="progress-bar-container">
              <div class="progress-bar" :style="{ width: diagnosis.percentage + '%' }"></div>
            </div>
            <div class="progress-labels">
              <span>Normal</span>
              <span>Sedang</span>
              <span>Tinggi</span>
            </div>
            <p class="recommendation"><strong>Saran:</strong> {{ diagnosis.recommendation }}</p>
          </div>
        </div>
        <div class="result-illustration">
          <img src="/src/assets/PTSD.png" alt="Mental health illustration for PTSD" />
        </div>
      </div>
    </section>

    <section class="details-section">
      <div class="details-container">
        <div class="details-text">
          <h3>Apa yang sedang kamu alami?</h3>
          <h4>{{ diagnosis.name }}</h4>
          <p>{{ diagnosis.description }}</p>
          <div class="ptsd-info-container">
            <h2 class="info-title">Penjelasan</h2>
            <div class="info-block">
              <h4>Pengertian</h4>
              <p>Gangguan stres pasca-trauma (PTSD) adalah masalah kesehatan mental yang mungkin dirasakan seseorang setelah mengalami peristiwa traumatis. PSTD pertama kali dikenali oleh veteran perang selama Perang Dunia I. Saat itu, PSTD disebut sebagai "kejutan peluru" atau "shock shell". Gangguan stres pasca trauma (PTSD) adalah gangguan kejiwaan umum yang terjadi setelah seseorang mengalami peristiwa traumatis. PTSD dapat menyebabkan gangguan kronis, menyebabkan penyakit kejiwaan penyerta, dan meningkatkan risiko bunuh diri. (Mann, 2024), kemudian menurut peneliti lain menyebutkan bawa Post traumatic stress disorder suatu sindrom yang dialami oleh yang mengalami kejadian yang traumatis dan individu tersebut tidak mampu menghilangkan ingatan akan kejadian tersebut dari pikirannya.  Post-Traumatic Stress Disorder (PTSD) sering disalahpahami dan salah diagnosis karena gejalanya tumpang tindih dengan gangguan stres akut. Namun, kondisi ini memiliki gejala spesifik yang membedakan dari gangguan jiwa lain, antara lain mimpi buruk dan kilas balik (Sareen, 2014).</p>
            </div>
            <div class="info-block">
              <h4>Penyebab</h4>
              <p>Post-traumatic Stress Disorder (PTSD) disebabkan oleh kejadian traumatis di masa lalu. Banyak yang beranggapan bahwa kondisi ini banyak menyerang orang dewasa. Namun, PTSD ternyata juga bisa menyerang remaja. Bahkan, para ahli mengatakan bahwa remaja merupakan kelompok yang rentan mengalami kondisi mental ini. Gangguan mental pascatrauma pada remaja dapat berasal dari berbagai kejadian traumatis. Kejadian yang sering terjadi di lingkungan remaja contohnya perundungan (bullying), pelecehan seksual, dan kekerasan. Walaupun kebanyakan remaja sering murung dan moody, remaja sangat rentan mengalami masalah kesehatan mental yang signifikan dan perlu diatasi segera.</p>
            </div>
            <div class="info-block">
              <h4>Tanda dan Gejala</h4>
              <ul>
                <li>Mengingat Kejadian Traumatis</li>
                <li>Mimpi buruk atau mimpi berulang tentang kejadian traumatis</li>
                <li>Pikiran atau gambaran mengganggu yang muncul tiba-tiba dan tidak diinginkan</li>
                <li>Distress emosional atau fisik saat dihadapkan dengan hal yang mengingatkan pada trauma</li>
                <li>Menghindari tempat, orang, atau situasi yang mengingatkan pada kejadian</li>
                <li>Menarik diri dari aktivitas sosial atau orang lain</li>
                <li>Gangguan pada cara berpikir dan perasaan terhadap diri sendiri atau dunia.</li>
                <li>Perasaan putus asa, sedih, atau tidak berharga</li>
                <li>Amnesia terhadap bagian penting dari kejadian traumatis</li>
                <li>Perasaan terasing dari orang lain</li>
                <li>Pikiran negatif berlebihan tentang diri sendiri atau orang lain</li>
                <li>Peningkatan kewaspadaan yang berlebihan</li>
                <li>Sulit tidur atau sering terbangun di malam hari</li>
                <li>Mudah terkejut atau panik</li>
                <li>Iritabilitas dan ledakan emosi (marah berlebihan)</li>
                <li>Perilaku merusak diri sendiri (contohnya: menyakiti diri, alkohol, narkoba)</li>
                <li>Kesulitan berkonsentrasi</li>
              </ul>
            </div>
            <div class="info-block">
              <h4>Perawatan untuk Mengatasi PTSD</h4>
              <ul>
                <li>Intervensi promosi dan pencegahan kesehatan mental.</li>
                <li>Cognitive Behavioral Therapy (CBT)</li>
                <li>Eye Movement Desensitization and Reprocessing (EMDR)</li>
                <li>Terapi Pemaparan Naratif (NET)</li>
                <li>Terapi Bermain</li>
                <li>Teknik kesadaran penuh, seperti meditasi dan teknik pernapasan</li>
                <li>Terapi kelompok</li>
                <li>Terapi Keluarga</li>
                <li>Farmakoterapi</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="details-illustration">
          <img src="/src/assets/Hasiltest.png" alt="Doctor illustration" />
        </div>
      </div>
      <div class="re-test-container">
        <button @click="showConfirmationModal = true" class="re-test-button">Tes Ulang</button>
      </div>
    </section>

    <section class="articles-section">
      <div class="articles-container">
        <h3>Baca artikel selengkapnya mengenai {{ diagnosis.name }}</h3>
        <p class="article-subtitle">Artikel Terkait {{ diagnosis.name }}</p>
        <div v-if="loadingArticles" class="articles-loading">
          <p>Memuat artikel...</p>
        </div>
        <div v-else-if="articlesError" class="articles-error">
          <p>{{ articlesError }}</p>
        </div>
        <div v-else-if="diagnosis.articles.length === 0" class="articles-empty">
          <p>Belum ada artikel tersedia untuk kategori ini.</p>
        </div>
        <div v-else class="article-cards">
          <div class="article-card" v-for="article in diagnosis.articles" :key="article.title">
            <h4>{{ article.title }}</h4>
            <p>{{ article.summary }}</p>
            <div class="article-footer">
              <div class="author" v-if="article.author">
                <span>{{ article.author }}</span>
              </div>
              <a v-if="article.link" :href="article.link" target="_blank" class="read-more-link">Baca Selengkapnya</a>
            </div>
          </div>
        </div>
      </div>
    </section>
      <ConfirmationModal
    :isVisible="showConfirmationModal"
    title="Konfirmasi Tes Ulang"
    message="Anda akan melakukan tes ulang dan hasil tes Anda sebelumnya akan dihapus. Apakah Anda yakin?"
    @confirm="handleConfirm"
    @close="handleCancel"
  />
  </div>
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
import { testState, resetTestState } from '../../store/testState';
import axios from 'axios';

export default {
  name: 'ResultIsPTSD',
  components: {
    ConfirmationModal,
  },
  data() {
    return {
      showConfirmationModal: false,
      articles: [],
      loadingArticles: false,
      articlesError: ''
    };
  },
  computed: {
    hasil() {
      // Prioritaskan testState, fallback ke localStorage, lalu default
      let hasil = testState.hasilTesTerakhir?.hasil;
      
      if (!hasil) {
        try {
          const localStorageData = localStorage.getItem('lastTestResult');
          if (localStorageData) {
            const parsed = JSON.parse(localStorageData);
            hasil = parsed.hasil;
          }
        } catch (e) {
          console.error('Error parsing localStorage lastTestResult:', e);
        }
      }
      
      // Fallback ke default jika masih kosong
      if (!hasil) {
        hasil = 'Gejala PTSD';
      }
      
      return hasil;
    },
    severity() {
      // Prioritaskan router params, lalu testState, fallback ke localStorage, lalu default
      let severity = this.$route.params.severity;
      
      if (!severity) {
        severity = testState.hasilTesTerakhir?.severity;
      }
      
      if (!severity) {
        try {
          const localStorageData = localStorage.getItem('lastTestResult');
          if (localStorageData) {
            const parsed = JSON.parse(localStorageData);
            severity = parsed.severity;
          }
        } catch (e) {
          console.error('Error parsing localStorage lastTestResult:', e);
        }
      }
      
      // Fallback ke default jika masih kosong
      if (!severity) {
        severity = 'Berat';
      }
      
      console.log('Severity (computed):', severity);
      console.log('testState.hasilTesTerakhir:', testState.hasilTesTerakhir);
      console.log('localStorage lastTestResult:', localStorage.getItem('lastTestResult'));
      console.log('Router params severity:', this.$route.params.severity);
      
      return severity;
    },
    diagnosis() {
      console.log('Diagnosis severity:', this.severity);
      if (this.severity === 'Ringan') {
        return {
          name: 'Gejala PTSD Ringan',
          percentage: 25,
          recommendation: 'Anda mengalami gejala PTSD ringan. Cobalah teknik relaksasi dan berbicara dengan orang terdekat.',
          description: 'PTSD ringan dapat berupa kilas balik ringan, mimpi buruk sesekali, atau kecemasan yang tidak terlalu mengganggu.',
          articles: this.articles
        };
      } else if (this.severity === 'Sedang') {
        return {
          name: 'Gejala PTSD Sedang',
          percentage: 55,
          recommendation: 'Gejala yang Anda alami cukup serius. Segera konsultasikan dengan psikolog atau psikiater.',
          description: 'PTSD sedang ditandai dengan kilas balik yang sering, mimpi buruk, dan kecemasan yang mulai mengganggu aktivitas harian.',
          articles: this.articles
        };
      } else if (this.severity === 'Berat') {
        return {
          name: 'Gejala PTSD Berat',
          percentage: 95,
          recommendation: 'Gejala yang Anda alami sangat serius. Segera konsultasikan dengan psikiater untuk penanganan intensif.',
          description: 'PTSD berat meliputi kilas balik yang sangat intens, mimpi buruk yang mengganggu tidur, dan kecemasan yang sangat mengganggu kehidupan sehari-hari.',
          articles: this.articles
        };
      } else {
        return {
          name: 'Gejala PTSD Berat',
          percentage: 95,
          recommendation: 'Gejala yang Anda alami sangat serius. Segera konsultasikan dengan psikiater untuk penanganan intensif.',
          description: 'PTSD berat meliputi kilas balik yang sangat intens, mimpi buruk yang mengganggu tidur, dan kecemasan yang sangat mengganggu kehidupan sehari-hari.',
          articles: this.articles
        };
      }
    }
  },
  mounted() {
    console.log('ResultIsPTSD mounted');
    console.log('Initial testState.hasilTesTerakhir:', testState.hasilTesTerakhir);
    console.log('Initial localStorage lastTestResult:', localStorage.getItem('lastTestResult'));
    
    // Ambil ulang dari localStorage jika testState kosong
    if (!testState.hasilTesTerakhir && localStorage.getItem('lastTestResult')) {
      try {
        const localStorageData = localStorage.getItem('lastTestResult');
        const parsed = JSON.parse(localStorageData);
        testState.hasilTesTerakhir = parsed;
        console.log('Updated testState.hasilTesTerakhir from localStorage:', testState.hasilTesTerakhir);
      } catch (e) {
        console.error('Error parsing lastTestResult in mounted:', e);
      }
    }
    
    // Jika tidak ada data hasil tes sama sekali, redirect ke tes
    if (!localStorage.getItem('lastTestResult')) {
      console.log('No test result found, redirecting to TesDiri');
      this.$router.replace({ name: 'TesDiri' });
      return;
    }
    
    // Fetch articles for PTSD category
    this.fetchArticles('ptsd');
    
    console.log('Final testState.hasilTesTerakhir after mounted:', testState.hasilTesTerakhir);
  },
  methods: {
    async fetchArticles(category) {
      this.loadingArticles = true;
      this.articlesError = '';
      try {
        const response = await axios.get(`https://mindcareindependent.com/api/get_journals_by_category.php?category=${category}`);
        if (response.data.success) {
          this.articles = response.data.data.journals.map(article => ({
            title: article.judul,
            summary: article.kutipan,
            author: 'MindCare',
            link: article.link
          }));
          console.log('Articles loaded for category:', category, this.articles);
        } else {
          this.articlesError = 'Gagal memuat artikel';
          console.error('Failed to load articles:', response.data.message);
        }
      } catch (error) {
        this.articlesError = 'Terjadi kesalahan saat memuat artikel';
        console.error('Error fetching articles:', error);
      } finally {
        this.loadingArticles = false;
      }
    },
    handleConfirm() {
      // Reset test state untuk memulai tes baru
      resetTestState();
      localStorage.removeItem('lastTestResult');
      
      // Redirect ke halaman tes
      this.$router.push({ name: 'TesDiri' });
      this.showConfirmationModal = false;
    },
    handleCancel() {
      this.showConfirmationModal = false;
    },
    goToTest() {
      this.showConfirmationModal = true;
    }
  }
}
</script>

<style scoped>
/* Styles can be customized for PTSD */
.result-page {
  background-color: var(--bg-primary);
  font-family: 'Inter', sans-serif;
}
/* New gradient for PTSD */
.ptsd-bg {
  background: linear-gradient(120deg, #2c3e50 0%, #3498db 100%); /* Dark Blue to Blue gradient */
}
/* ... rest of the styles are identical ... */

/* Hero Section */
.result-hero {
  color: white;
  padding: 80px 24px;
  position: relative;
  overflow: hidden;
}
.result-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 150%;
  height: 150%;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M0,50 C25,100 75,0 100,50" stroke="rgba(255,255,255,0.05)" stroke-width="2" fill="none"/></svg>');
  opacity: 0.5;
  transform: rotate(-15deg);
}

.result-container {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 40px;
  position: relative;
  z-index: 2;
}
.result-content { flex: 1.2; }
.result-illustration {
  flex: 1;
  max-width: 350px;
  align-self: flex-end;
}
.result-illustration img { width: 100%; }

.title-block {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 8px;
}
.title-line {
  width: 5px;
  height: 50px;
  background-color: white;
  border-radius: 5px;
}
.result-content h2 {
  font-size: 2.8rem;
  font-weight: 700;
}
.subtitle {
  font-size: 1rem;
  opacity: 0.9;
  margin-left: 21px;
  margin-bottom: 30px;
}
.diagnosis-card {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 20px;
  padding: 24px 30px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.diagnosis-card h3 {
  font-size: 1.4rem;
  font-weight: 600;
  text-align: center;
  margin: 0 0 20px 0;
}
.progress-bar-container {
  background: rgba(0, 0, 0, 0.25);
  border-radius: 50px;
  height: 12px;
  position: relative;
  overflow: hidden;
}
.progress-bar {
  background: #3498db; /* Blue color for PTSD */
  height: 100%;
  border-radius: 50px;
  transition: width 0.8s ease-in-out;
}
.progress-labels {
  display: flex;
  justify-content: space-between;
  padding: 4px 2px 0;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.85);
}
.recommendation {
  font-size: 0.95rem;
  line-height: 1.6;
  margin-top: 24px;
}

/* Details Section */
.details-section { 
  padding: 80px 24px; 
  background-color: var(--bg-primary);
}
.details-container {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: flex-start;
  gap: 60px;
}
.details-text { flex: 1.5; }
.details-illustration {
  flex: 1;
  max-width: 300px;
}
.details-illustration img { width: 100%; }
.details-text h3 {
  font-size: 1.8rem;
  color: var(--text-primary);
  margin-bottom: 8px;
}
.details-text h4 {
  font-size: 1.2rem;
  color: var(--text-secondary);
  margin-bottom: 24px;
  font-weight: 500;
}
.details-text p {
  color: var(--text-secondary);
  line-height: 1.8;
  white-space: pre-line;
}

.re-test-container {
  max-width: 1100px;
  margin: 40px auto 0;
  text-align: center;
}

.re-test-button {
  background-color: var(--button-primary);
  color: white;
  padding: 12px 32px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.re-test-button:hover {
  background-color: var(--button-hover);
}

/* Articles Section */
.articles-section {
  background-color: var(--bg-tertiary);
  padding: 80px 24px;
}
.articles-container {
  max-width: 1100px;
  margin: 0 auto;
  text-align: center;
}
.articles-container h3 {
  font-size: 1.8rem;
  color: var(--text-primary);
}
.article-subtitle {
  color: var(--text-muted);
  margin-bottom: 40px;
}
.article-cards {
  display: flex;
  gap: 30px;
  justify-content: center;
  flex-wrap: wrap;
  align-items: stretch;
}
.article-card {
  flex: 1;
  min-width: 280px;
  max-width: 320px;
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 20px;
  text-align: left;
  box-shadow: 0 4px 15px var(--shadow-light);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 200px;
}
.article-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px var(--shadow-medium);
}
.article-card h4 {
  font-size: 1.1rem;
  margin: 0 0 8px 0;
  color: var(--text-primary);
}
.article-card p {
  font-size: 0.9rem;
  color: var(--text-light);
  margin-bottom: 20px;
  flex-grow: 1;
}
.article-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid var(--border-color);
}
.author {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: var(--text-muted);
}
.author img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}
.read-more-link {
  font-size: 0.9rem;
  color: var(--accent-blue);
  text-decoration: none;
  font-weight: 600;
}
.read-more-link:hover {
  text-decoration: underline;
}

.articles-loading, .articles-error, .articles-empty {
  padding: 20px;
  color: var(--text-secondary);
  font-size: 1rem;
  text-align: center;
  margin-bottom: 20px;
}

/* Tambahkan style info-block dan info-container agar konsisten dengan neurosis dan psikotik */
.ptsd-info-container {
  margin-top: 32px;
  background: var(--card-bg);
  border-radius: 18px;
  box-shadow: 0 4px 24px var(--shadow-heavy);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--accent-blue);
  margin-bottom: 24px;
  text-align: center;
}
.info-block {
  margin-bottom: 24px;
}
.info-block h4 {
  color: var(--text-primary);
  font-size: 1.1rem;
  margin-bottom: 10px;
  font-weight: 700;
}
.info-block p, .info-block ul {
  color: var(--text-secondary);
  font-size: 1.05rem;
  line-height: 1.7;
  margin-bottom: 8px;
}
.info-block ul {
  padding-left: 22px;
  margin-bottom: 8px;
}
.info-block li {
  margin-bottom: 4px;
  color: var(--text-secondary);
  font-size: 1.05rem;
  line-height: 1.7;
}

@media (max-width: 768px) {
  .result-container, .details-container {
    flex-direction: column;
    text-align: center;
  }
  .result-illustration { display: none; }
  .details-illustration { margin-top: 40px; display: none; }
  .details-text, .info-block p, .info-block li {
    text-align: justify;
  }
}
</style>
