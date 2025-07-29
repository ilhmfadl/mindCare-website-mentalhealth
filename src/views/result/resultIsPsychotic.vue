<template>
  <div class="result-page">
    <section class="result-hero psychotic-bg">
      <div class="result-container">
        <div class="result-content">
          <div class="title-block">
            <span class="title-line"></span>
            <h2>HASIL TES KAMU</h2>
          </div>
          <p class="subtitle">Gangguan Kesehatan Mental yang paling dominan Kamu alami saat ini adalah:</p>
          <div v-if="diagnosis" :class="diagnosisCardClass">
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
          <img src="/src/assets/anxiety.png" alt="Psychotic illustration" />
        </div>
      </div>
    </section>

    <section class="details-section">
      <div class="details-container">
        <div class="details-text">
          <h3>Apa yang sedang kamu alami?</h3>
          <h4>{{ diagnosis.name }}</h4>
          <p>{{ diagnosis.description }}</p>
          <div class="psychotic-info-container">
            <h2 class="info-title">Penjelasan</h2>
            <div class="info-block">
              <h4>Pengertian</h4>
              <p>Psikotik merupakan salah satu gangguan jiwa yang mengarah pada kumpulan gejala yang memengaruhi pikiran, perasaan dan perilaku dimana selama episode psikotik mereka akan sulit untuk membedakan antara yang nyata dan tidak nyata. Penderita gangguan psikotik menggambarkan pengalaman mendengar suara yang selaras dengan keyakinan yang mereka pegang tentang diri mereka sendiri. Orang yang mengalami gangguan psikotik biasanya akan mengalami delusi (keyakinan yang salah misalnya merasa orang lain yang tidak dikenalnya mengirimkan pesan yang khusus, merasa ingin disakiti oleh orang lain), halusinasi (mendengar suara suara aneh yang tidak didengar oleh orang lain yang menyuruhnya melakukan sesuatu).</p>
            </div>
            <div class="info-block">
              <h4>Penyebab</h4>
              <p>Penyebab seseorang mengalami gangguan psikotik tidak dapat dijelaskan secara pasti hal tersebut disebabkan karena psikosis tampaknya merupakan hasil dari kombinasi kompleks dari risiko genetik, perbedaan perkembangan otak dan paparan stres atau trauma. Psikotik juga mungkin disebabkan oleh gejala penyakit mental, seperti skizofrenia, gangguan bipolar, atau depresi berat.</p>
            </div>
            <div class="info-block">
              <h4>Tanda dan Gejala</h4>
              <ul>
                <li>Kecurigaan, ide-ide paranoid, atau kegelisahan dengan orang lain</li>
                <li>Kesulitan berpikir jernih dan logis</li>
                <li>Menarik diri secara sosial dan menghabiskan lebih banyak waktu sendirian</li>
                <li>Gagasan yang tidak biasa atau terlalu intens, perasaan aneh dan terkadang tidak berperasaan</li>
                <li>Penurunan perawatan diri atau kebersihan pribadi</li>
                <li>Gangguan tidur, termasuk sulit tidur dan berkurangnya waktu tidur</li>
                <li>Kesulitan membedakan fantasi dan kenyataan</li>
                <li>Kesulitan berkomunikasi dengan orang lain</li>
                <li>Penurunan nilai atau kinerja pekerjaan secara tiba-tiba</li>
              </ul>
              <p>Bersamaan dengan gejala-gejala diatas, seseorang dengan psikotik juga dapat mengalami perubahan perilaku yang lebih umum yang meliputi:</p>
              <ul>
                <li>Gangguan emosional</li>
                <li>Kecemasan</li>
                <li>Kurang motivasi</li>
                <li>Tidak dapat beraktivitas normal</li>
              </ul>
            </div>
            <div class="info-block">
              <h4>Cara Mengatasi/Mengobati</h4>
              <ul>
                <li>Pemberian obat Antipsikotik</li>
                <li>Terapi Psikososial</li>
                <li>Konseling dan Dukungan Keluarga</li>
                <li>Rawat Inap (jika perlu)</li>
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
  name: 'ResultIsPsychotic',
  props: {
    score: {
      type: [Number, String],
      required: false
    },
    severityProp: { type: String, default: '' }
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
          name: 'Gejala Psikotik Ringan',
          percentage: 25,
          recommendation: 'Anda mengalami gejala psikotik ringan. Segera konsultasikan dengan psikiater untuk penanganan lebih lanjut.',
          description: 'Psikotik ringan dapat berupa kecurigaan ringan, kesulitan berpikir jernih, atau perubahan perilaku yang tidak signifikan.',
          articles: this.articles
        };
      } else if (this.severity === 'Sedang') {
        return {
          name: 'Gejala Psikotik Sedang',
          percentage: 55,
          recommendation: 'Gejala yang Anda alami cukup serius. Segera konsultasikan dengan psikiater untuk penanganan medis.',
          description: 'Psikotik sedang ditandai dengan halusinasi, delusi, atau gangguan berpikir yang mulai mengganggu aktivitas harian.',
          articles: this.articles
        };
      } else if (this.severity === 'Berat') {
        return {
          name: 'Gejala Psikotik Berat',
          percentage: 95,
          recommendation: 'Gejala yang Anda alami sangat serius. Segera konsultasikan dengan psikiater untuk penanganan intensif.',
          description: 'Psikotik berat meliputi halusinasi berat, delusi yang kuat, atau gangguan berpikir yang sangat mengganggu kehidupan sehari-hari.',
          articles: this.articles
        };
      } else {
        return {
          name: 'Gejala Psikotik Berat',
          percentage: 95,
          recommendation: 'Gejala yang Anda alami sangat serius. Segera konsultasikan dengan psikiater untuk penanganan intensif.',
          description: 'Psikotik berat meliputi halusinasi berat, delusi yang kuat, atau gangguan berpikir yang sangat mengganggu kehidupan sehari-hari.',
          articles: this.articles
        };
      }
    },
    diagnosisCardClass() {
      // Gunakan severity dari diagnosis
      const name = (this.diagnosis.name || '').toLowerCase();
      if (name.includes('ringan')) return 'diagnosis-card ringan';
      if (name.includes('sedang')) return 'diagnosis-card sedang';
      if (name.includes('berat')) return 'diagnosis-card berat';
      return 'diagnosis-card normal';
    }
  },
  components: {
    ConfirmationModal,
  },
  mounted() {
    console.log('ResultIsPsychotic mounted');
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
    
    // Fetch articles for psychotic category
    this.fetchArticles('psikotik');
    
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
.result-page {
  background-color: var(--bg-primary);
  font-family: 'Inter', sans-serif;
}

.result-hero {
  background: linear-gradient(120deg, #8b5cf6 0%, #a78bfa 100%);
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
  background: var(--accent-purple);
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

.psychotic-info-container {
  margin-top: 32px;
  background: var(--card-bg);
  border-radius: 18px;
  box-shadow: 0 4px 24px var(--shadow-heavy);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--accent-purple);
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