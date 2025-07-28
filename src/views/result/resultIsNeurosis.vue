<template>
  <div class="result-page">
    <section class="result-hero">
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
          <img src="/src/assets/Depresi.png" alt="Neurosis illustration" />
        </div>
      </div>
    </section>

    <section class="details-section">
      <div class="details-container">
        <div class="details-text">
          <h3>Apa yang sedang kamu alami?</h3>
          <h4>{{ diagnosis.name }}</h4>
          <p>{{ diagnosis.description }}</p>
          <div class="neurosis-info-container">
            <h2 class="info-title">Penjelasan</h2>
            <div class="info-block">
              <h4>Pengertian</h4>
              <p>Psikoneurosis atau disebut juga dengan neurosis adalah istilah umum yang merujuk pada ketidakseimbangan mental yang menyebabkan stres,  neurosis tidak mempengaruhi pemikiran rasional. Konsep neurosis berhubungan dengan bidang psikoanalisis, suatu aliran pemikiran dalam psikologi atau psikiatri. Neurosis adalah gangguan mental ringan yang ditandai dengan ketidakstabilan emosi, kecemasan yang berlebihan, dan respon tidak wajar terhadap stres, namun tidak disertai dengan gangguan realita (tidak mengalami delusi atau halusinasi). Orang yang mengalami neurosis masih bisa menjalani aktivitas sehari-hari, meskipun sering kali merasa tertekan atau terganggu secara emosional.</p>
            </div>
            <div class="info-block">
              <h4>Penyebab Neurosis</h4>
              <p>Neurosis bisa disebabkan oleh kombinasi faktor biologis, psikologis, dan sosial. Berikut beberapa penyebab umumnya:</p>
              <ul>
                <li>Tekanan Psikososial</li>
                <li>Trauma Psikologis</li>
                <li>Faktor Kepribadian seperti Kepribadian yang terlalu perfeksionis, mudah cemas, sensitif, atau cenderung menarik diri.</li>
                <li>Ketidakseimbangan Kimia Otak seperti Gangguan pada neurotransmitter seperti serotonin dan dopamin.</li>
                <li>Faktor Genetik dan Lingkungan</li>
              </ul>
            </div>
            <div class="info-block">
              <h4>Tanda dan Gejala Neurosis</h4>
              <ul>
                <li>Kecemasan berlebihan atau tidak wajar</li>
                <li>Rasa takut yang tidak masuk akal (fobia)</li>
                <li>Pikiran berulang (obsesi)</li>
                <li>Perasaan bersalah terus menerus</li>
                <li>Suasana hati berubah-ubah</li>
                <li>Sulit mengambil keputusan</li>
                <li>Perasaan rendah diri</li>
                <li>Sakit kepala</li>
                <li>Nyeri dada</li>
                <li>Gangguan pencernaan (mual, maag)</li>
                <li>Keringat berlebihan</li>
                <li>Detak jantung cepat</li>
                <li>Gangguan tidur</li>
              </ul>
            </div>
            <div class="info-block">
              <h4>Cara Mengatasi Neurosis</h4>
              <ul>
                <li>Terapi perilaku kognitif (CBT): Membantu pasien mengidentifikasi dan mengubah pola pikir negatif.</li>
                <li>Psikoterapi dinamis: Menggali konflik bawah sadar yang belum terselesaikan.</li>
                <li>Terapi relaksasi dan mindfulness</li>
                <li>Pemberian Obat Antidepresan</li>
                <li>Pemberian obat Anxiolytic</li>
                <li>Konseling keluarga</li>
                <li>Edukasi pasien dan keluarga tentang kondisi mental</li>
                <li>Dukungan dari kelompok sebaya atau komunitas</li>
                <li>Olahraga teratur</li>
                <li>Pola makan sehat</li>
                <li>Tidur cukup</li>
                <li>Menghindari alkohol, rokok, dan kafein berlebih</li>
                <li>Mengelola stres (melalui hobi, meditasi, atau aktivitas positif)</li>
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
  name: 'ResultIsNeurosis',
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
        severity = 'Normal';
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
          name: 'Gejala Neurosis Ringan',
          percentage: 25,
          recommendation: 'Anda mengalami gejala neurosis ringan. Cobalah teknik relaksasi, olahraga, dan berbicara dengan orang terdekat.',
          description: 'Neurosis ringan dapat berupa kecemasan, kekhawatiran berlebih, atau kesulitan tidur. Gejala ini biasanya tidak mengganggu aktivitas sehari-hari secara signifikan.',
          articles: this.articles
        };
      } else if (this.severity === 'Sedang') {
        return {
          name: 'Gejala Neurosis Sedang',
          percentage: 55,
          recommendation: 'Cobalah berbicara dengan tenaga profesional dan hindari menyendiri terlalu lama. Lakukan kegiatan yang menyenangkan.',
          description: 'Neurosis sedang ditandai dengan kecemasan yang lebih sering, mudah marah, atau gangguan tidur yang mulai mengganggu aktivitas harian.',
          articles: this.articles
        };
      } else if (this.severity === 'Berat') {
        return {
          name: 'Gejala Neurosis Berat',
          percentage: 95,
          recommendation: 'Gejala yang Anda alami cukup berat. Segera konsultasikan dengan psikolog atau psikiater untuk penanganan lebih lanjut.',
          description: 'Neurosis berat meliputi kecemasan berlebih, fobia, obsesi, atau kompulsi yang sangat mengganggu kehidupan sehari-hari. Bantuan profesional sangat dianjurkan.',
          articles: this.articles
        };
      } else {
        return {
          name: 'Gejala Neurosis Ringan',
          percentage: 20,
          recommendation: 'Anda mengalami gejala neurosis ringan. Cobalah teknik relaksasi, olahraga, dan berbicara dengan orang terdekat.',
          description: 'Neurosis ringan dapat berupa kecemasan, kekhawatiran berlebih, atau kesulitan tidur. Gejala ini biasanya tidak mengganggu aktivitas sehari-hari secara signifikan.',
          articles: this.articles
        };
      }
    }
  },
  mounted() {
    console.log('ResultIsNeurosis mounted');
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
    
    // Fetch articles for neurosis category
    this.fetchArticles('neurosis');
    
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
  background-color: #fdfcfa;
  font-family: 'Inter', sans-serif;
}

/* Hero Section */
.result-hero {
  background: linear-gradient(120deg, #a18cd1 0%, #fbc2eb 100%); /* Gradient purple to pink */
  color: white;
  padding: 80px 24px;
  position: relative;
  overflow: hidden;
}
.result-hero::before { /* Background curve lines */
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
  margin-left: 21px; /* Align with title */
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
  background: #EF4444; /* Red color */
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
.details-section { padding: 80px 24px; }
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
  color: #333;
  margin-bottom: 8px;
}
.details-text h4 {
  font-size: 1.2rem;
  color: #555;
  margin-bottom: 24px;
  font-weight: 500;
}
.details-text p {
  color: #555;
  line-height: 1.8;
  white-space: pre-line; /* To render placeholder lines */
}

.re-test-container {
  max-width: 1100px;
  margin: 40px auto 0;
  text-align: center;
}

.re-test-button {
  background-color: #EC744A;
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
  background-color: #d9633a;
}

/* Articles Section */
.articles-section {
  background-color: #faf7f3;
  padding: 80px 24px;
}
.articles-container {
  max-width: 1100px;
  margin: 0 auto;
  text-align: center;
}
.articles-container h3 {
  font-size: 1.8rem;
  color: #333;
}
.article-subtitle {
  color: #777;
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
  background: white;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 20px;
  text-align: left;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 200px;
}
.article-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.article-card h4 {
  font-size: 1.1rem;
  margin: 0 0 8px 0;
}
.article-card p {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 20px;
  flex-grow: 1;
}
.article-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid #eee;
}
.author {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: #888;
}
.author img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}
.read-more-link {
  font-size: 0.9rem;
  color: #EC744A;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}
.read-more-link:hover {
  color: #d9633a;
}

.articles-loading, .articles-error, .articles-empty {
  padding: 20px;
  background-color: #f0f0f0;
  border-radius: 10px;
  margin-bottom: 30px;
}
.articles-loading p, .articles-error p, .articles-empty p {
  color: #555;
  font-size: 1rem;
}

/* Tambahkan style agar info-block dan neurosis-info-container tampil menarik dan konsisten */
.neurosis-info-container {
  margin-top: 32px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(106,76,155,0.08);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: #a18cd1;
  margin-bottom: 24px;
  text-align: center;
}
.info-block {
  margin-bottom: 24px;
}
.info-block h4 {
  color: #6A4C9B;
  font-size: 1.1rem;
  margin-bottom: 10px;
  font-weight: 700;
}
.info-block p, .info-block ul {
  color: #333;
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
  color: #333;
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