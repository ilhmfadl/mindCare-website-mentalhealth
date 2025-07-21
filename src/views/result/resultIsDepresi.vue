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
          <img src="/src/assets/Depresi.png" alt="Mental health illustration" />
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
              <!-- <img src="/src/assets/author-avatar.png" alt="Author" /> -->
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
  name: 'ResultPage',
  props: {
    score: {
      type: [Number, String],
      required: true
    }
  },
  computed: {
    diagnosis() {
      const s = Number(this.score);
      if (s <= 14) {
        return {
          name: "Kesehatan Mental Stabil",
          percentage: 20,
          recommendation: "Kondisi Anda sangat baik. Terus pertahankan gaya hidup sehat dan kelola stres dengan baik.",
          description: "Kesehatan mental yang stabil berarti Anda mampu mengelola emosi, pikiran, dan perilaku sehari-hari dengan baik. Anda memiliki ketahanan yang baik terhadap stres dan mampu menjaga hubungan yang sehat.",
          articles: [
            { title: "Tips Menjaga Kestabilan Emosi", summary: "Kumpulan cara sederhana...", author: "Dr. Anisa" },
            { title: "Pentingnya Mindfulness", summary: "Latih fokus dan ketenangan...", author: "Dr. Budi" },
            { title: "Manfaat Tidur Cukup", summary: "Jangan sepelekan istirahat...", author: "Dr. Cantika" },
          ]
        };
      }
      if (s <= 24) {
        return {
          name: "Stres Ringan",
          percentage: 50,
          recommendation: "Anda mengalami gejala stres ringan. Cobalah untuk beristirahat, lakukan hobi yang Anda sukai, dan berbicara dengan orang terdekat.",
          description: "Stres ringan adalah bagian normal dari kehidupan, namun jika dibiarkan dapat menumpuk. Gejalanya bisa berupa mudah marah, lelah, atau sulit tidur. Penting untuk menemukan cara relaksasi yang efektif.",
          articles: [
            { title: "Teknik Relaksasi Pernapasan", summary: "Cara cepat atasi stres...", author: "Dr. Budi" },
            { title: "Mengelola Stres di Pekerjaan", summary: "Strategi untuk tetap tenang...", author: "Dr. Anisa" },
            { title: "Journaling untuk Kesehatan Mental", summary: "Tuangkan perasaan Anda...", author: "Dr. Cantika" },
          ]
        };
      }
      if (s <= 36) {
        return {
          name: "Depresi Sedang",
          percentage: 75,
          recommendation: "Cobalah berbicara dengan tenaga profesional dan hindari menyendiri terlalu lama. Lakukan kegiatan yang Anda sukai.",
          description: "Depresi sedang ditandai dengan perasaan sedih yang terus-menerus, kehilangan minat pada banyak hal, dan perubahan energi yang signifikan. Kondisi ini dapat mengganggu aktivitas sehari-hari dan memerlukan perhatian profesional.",
          articles: [
            { title: "Mengenal Terapi Kognitif Perilaku", summary: "Salah satu terapi efektif...", author: "Dr. Anisa" },
            { title: "Pentingnya Dukungan Sosial", summary: "Jangan hadapi sendirian...", author: "Dr. Budi" },
            { title: "Hubungan Pola Makan dan Depresi", summary: "Nutrisi untuk otak Anda...", author: "Dr. Cantika" },
          ]
        };
      }
      return {
        name: "Depresi Berat",
        percentage: 95,
        recommendation: "Gejala yang Anda alami signifikan. Harap segera mencari bantuan dari psikolog atau psikiater untuk evaluasi dan penanganan lebih lanjut.",
        description: "Depresi berat melibatkan gejala yang parah dan persisten, sangat mengganggu fungsi sehari-hari. Pikiran untuk menyakiti diri sendiri bisa muncul. Bantuan profesional segera sangat penting dan krusial.",
        articles: [
            { title: "Kapan Harus ke Psikiater?", summary: "Mengenali tanda-tanda darurat...", author: "Dr. Budi" },
            { title: "Memahami Peran Obat-obatan", summary: "Bagaimana antidepresan bekerja...", author: "Dr. Anisa" },
            { title: "Membangun Rencana Keamanan Diri", summary: "Langkah-langkah penting...", author: "Dr. Cantika" },
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
  margin-top: 16px; /* Added margin-top */
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