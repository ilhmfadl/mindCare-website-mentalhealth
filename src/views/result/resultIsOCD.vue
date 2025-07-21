<template>
  <div class="result-page">
    <section class="result-hero ocd-bg">
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
          <img src="/src/assets/OCD.png" alt="Mental health illustration for OCD" />
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
  name: 'ResultIsOCD',
  props: {
    score: {
      type: [Number, String],
      required: true
    }
  },
  computed: {
    diagnosis() {
      const s = Number(this.score);
      // NOTE: Score ranges for OCD might differ.
      // This is placeholder logic.
      if (s <= 14) {
        return {
          name: "Tidak Ada Indikasi OCD",
          percentage: 20,
          recommendation: "Perilaku Anda terorganisir dengan baik tanpa adanya obsesi atau kompulsi yang mengganggu. Pertahankan fleksibilitas.",
          description: "Adalah normal untuk menyukai keteraturan atau memiliki rutinitas. Ini tidak sama dengan OCD, yang melibatkan pikiran dan tindakan yang tidak diinginkan dan sulit dikendalikan.",
          articles: [
            { title: "Kebiasaan Baik vs. Kompulsi", summary: "Memahami perbedaannya...", author: "Dr. Maya" },
            { title: "Manfaat Fleksibilitas Kognitif", summary: "Beradaptasi dengan perubahan...", author: "Dr. Nino" },
            { title: "Mengelola Pikiran Mengganggu", summary: "Teknik untuk pikiran yang tidak diinginkan...", author: "Dr. Olivia" },
          ]
        };
      }
      if (s <= 24) {
        return {
          name: "Kecenderungan Obsesif-Kompulsif",
          percentage: 50,
          recommendation: "Anda menunjukkan beberapa pikiran obsesif atau perilaku kompulsif. Mempelajari teknik relaksasi dan mindfulness bisa membantu.",
          description: "Anda mungkin mengalami pikiran mengganggu yang berulang (obsesi) atau merasa perlu melakukan tindakan tertentu (kompulsi) untuk mengurangi kecemasan. Ini bisa mengganggu jika tidak dikelola.",
          articles: [
            { title: "Pengantar Terapi ERP", summary: "Exposure and Response Prevention...", author: "Dr. Nino" },
            { title: "Mindfulness untuk Pikiran Obsesif", summary: "Mengamati pikiran tanpa terhanyut...", author: "Dr. Maya" },
            { title: "Menantang Pikiran Tidak Rasional", summary: "Teknik dari Terapi Kognitif Perilaku...", author: "Dr. Olivia" },
          ]
        };
      }
      return {
        name: "Indikasi Kuat OCD",
        percentage: 85,
        recommendation: "Gejala Anda sangat konsisten dengan OCD. Terapi, terutama Exposure and Response Prevention (ERP), sangat efektif dan disarankan untuk konsultasi.",
        description: "Obsessive-Compulsive Disorder (OCD) adalah gangguan yang ditandai oleh obsesi (pikiran yang tidak diinginkan dan intrusif) dan kompulsi (perilaku berulang) yang signifikan mengganggu kehidupan sehari-hari.",
        articles: [
            { title: "Memahami OCD: Lebih dari Sekadar Rapi", summary: "Membongkar mitos tentang OCD...", author: "Dr. Maya" },
            { title: "Peran Obat dalam Penanganan OCD", summary: "Opsi farmakoterapi yang ada...", author: "Dr. Nino" },
            { title: "Mendukung Seseorang dengan OCD", summary: "Cara membantu orang terkasih...", author: "Dr. Olivia" },
        ]
      };
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
/* Styles can be customized for OCD */
.result-page {
  background-color: #fdfcfa;
  font-family: 'Inter', sans-serif;
}
/* New gradient for OCD */
.ocd-bg {
  background: linear-gradient(120deg, #1abc9c 0%, #16a085 100%); /* Teal gradient */
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
  background: #2ecc71; /* Emerald color for OCD */
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
