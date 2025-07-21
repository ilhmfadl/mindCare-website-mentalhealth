<template>
  <div class="result-page">
    <section class="result-hero bipolar-bg">
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
          <img src="/src/assets/bipolar.png" alt="Mental health illustration for Bipolar Disorder" />
        </div>
      </div>
    </section>

    <section class="details-section">
      <div class="details-container">
        <div class="details-text">
          <h3>Apa yang sedang kamu alami?</h3>
          <h4>{{ diagnosis.name }}</h4>
          <p>{{ diagnosis.description }}</p>
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
        <div class="article-cards">
          <div class="article-card" v-for="article in diagnosis.articles" :key="article.title">
            <h4>{{ article.title }}</h4>
            <p>{{ article.summary }}</p>
            <div class="author" v-if="article.author">
              <span>{{ article.author }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>
    <ConfirmationModal
      :visible="showConfirmationModal"
      title="Konfirmasi Tes Ulang"
      message="Anda akan melakukan tes ulang dan hasil tes Anda sebelumnya akan dihapus. Apakah Anda yakin?"
      @confirm="handleConfirm"
      @cancel="handleCancel"
    />
  </div>
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
import { testState } from '../../store/testState';

export default {
  name: 'ResultIsBipolar',
  props: {
    score: {
      type: [Number, String],
      required: true
    }
  },
  components: {
    ConfirmationModal,
  },
  data() {
    return {
      showConfirmationModal: false,
    };
  },
  computed: {
    diagnosis() {
      const s = Number(this.score);
      // NOTE: Score ranges for Bipolar Disorder might differ.
      // This is placeholder logic.
      if (s <= 14) {
        return {
          name: "Tidak Ada Indikasi Bipolar",
          percentage: 20,
          recommendation: "Suasana hati Anda tampak stabil. Terus kelola stres dan pertahankan pola hidup sehat.",
          description: "Stabilitas suasana hati adalah hal yang normal. Fluktuasi ringan dalam mood adalah bagian dari kehidupan sehari-hari dan tidak mengindikasikan adanya gangguan.",
          articles: [
            { title: "Manajemen Stres untuk Mood Stabil", summary: "Teknik menjaga suasana hati...", author: "Dr. Gina" },
            { title: "Pentingnya Pola Tidur Teratur", summary: "Tidur cukup untuk keseimbangan emosi...", author: "Dr. Hadi" },
            { title: "Nutrisi dan Kesehatan Mental", summary: "Makanan yang mendukung mood baik...", author: "Dr. Ina" },
          ]
        };
      }
      if (s <= 24) {
        return {
          name: "Kecenderungan Siklus Mood",
          percentage: 50,
          recommendation: "Anda menunjukkan perubahan suasana hati yang signifikan. Memantau mood dan membicarakannya dengan profesional bisa sangat membantu.",
          description: "Anda mungkin mengalami periode energi tinggi (mania/hipomania) dan periode depresi yang lebih jelas dari fluktuasi mood biasa. Ini perlu perhatian lebih lanjut.",
          articles: [
            { title: "Mengenal Gejala Hipomania", summary: "Tanda-tanda awal yang perlu diwaspadai...", author: "Dr. Hadi" },
            { title: "Mood Charting: Memantau Suasana Hati", summary: "Alat bantu untuk memahami pola mood...", author: "Dr. Gina" },
            { title: "Kapan Harus ke Profesional?", summary: "Mencari bantuan untuk siklus mood...", author: "Dr. Ina" },
          ]
        };
      }
      return {
        name: "Indikasi Kuat Gangguan Bipolar",
        percentage: 85,
        recommendation: "Gejala Anda sangat konsisten dengan gangguan bipolar. Sangat penting untuk segera mencari diagnosis dan penanganan dari psikiater.",
        description: "Gangguan bipolar ditandai oleh pergeseran suasana hati yang ekstrem, dari puncak mania ke titik terendah depresi. Kondisi ini memerlukan penanganan medis jangka panjang untuk mengelola gejalanya.",
        articles: [
            { title: "Apa itu Gangguan Bipolar Tipe 1 & 2?", summary: "Membedakan dua jenis utama...", author: "Dr. Gina" },
            { title: "Pengobatan untuk Gangguan Bipolar", summary: "Peran mood stabilizer dan terapi...", author: "Dr. Hadi" },
            { title: "Hidup Seimbang dengan Bipolar", summary: "Strategi manajemen jangka panjang...", author: "Dr. Ina" },
        ]
      };
    }
  },
  methods: {
    handleConfirm() {
      // Reset the shared state
      testState.testCompleted = false;
      testState.score = 0;
      testState.resultType = null;
      
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
/* Styles can be customized for Bipolar Disorder */
.result-page {
  background-color: #fdfcfa;
  font-family: 'Inter', sans-serif;
}
/* New gradient for Bipolar Disorder */
.bipolar-bg {
  background: linear-gradient(120deg, #f7b733 0%, #fc4a1a 100%); /* Yellow to Orange gradient */
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
  background: #f1c40f; /* Yellow color for Bipolar */
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
  white-space: pre-line;
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
}
.author {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: #888;
  margin-top: 16px;
}
.author img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

@media (max-width: 768px) {
  .result-container, .details-container {
    flex-direction: column;
    text-align: center;
  }
  .result-illustration { display: none; }
  .details-illustration { margin-top: 40px; }
}
</style>
