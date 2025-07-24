<template>
  <div class="result-page">
    <section class="result-hero neurosis-bg">
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
  name: 'ResultIsPsychotic',
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
          name: "Tidak Ada Gejala Psikotik",
          percentage: 20,
          recommendation: "Kondisi mental Anda stabil. Terus pertahankan gaya hidup sehat dan kelola stres dengan baik.",
          description: "Anda tidak menunjukkan gejala psikotik. Tetap jaga kesehatan mental dan hindari faktor risiko seperti stres berat atau penggunaan zat terlarang.",
          articles: [
            { title: "Menjaga Kesehatan Mental Sehari-hari", summary: "Tips sederhana untuk menjaga kestabilan emosi dan pikiran.", author: "Dr. Anisa" },
            { title: "Pentingnya Dukungan Sosial", summary: "Bagaimana lingkungan mendukung kesehatan mental.", author: "Dr. Budi" },
            { title: "Manfaat Relaksasi", summary: "Teknik relaksasi untuk mencegah stres berlebih.", author: "Dr. Cantika" },
          ]
        };
      }
      if (s <= 24) {
        return {
          name: "Gejala Psikotik Ringan",
          percentage: 50,
          recommendation: "Anda mengalami gejala psikotik ringan. Segera konsultasikan dengan profesional jika gejala memburuk.",
          description: "Psikosis ringan dapat berupa kebingungan, kesulitan membedakan kenyataan, atau perubahan perilaku yang tidak biasa. Gejala ini perlu dipantau dan dikelola dengan baik.",
          articles: [
            { title: "Mengenal Psikosis", summary: "Apa itu psikosis dan bagaimana gejalanya?", author: "Dr. Budi" },
            { title: "Tanda Awal Gangguan Psikotik", summary: "Kenali gejala sejak dini.", author: "Dr. Anisa" },
            { title: "Pentingnya Dukungan Keluarga", summary: "Peran keluarga dalam pemulihan.", author: "Dr. Cantika" },
          ]
        };
      }
      if (s <= 36) {
        return {
          name: "Gejala Psikotik Sedang",
          percentage: 75,
          recommendation: "Segera konsultasikan dengan psikolog atau psikiater. Hindari isolasi dan tetap terhubung dengan orang terdekat.",
          description: "Psikosis sedang ditandai dengan halusinasi ringan, delusi, atau perubahan perilaku yang mulai mengganggu aktivitas harian. Bantuan profesional sangat dianjurkan.",
          articles: [
            { title: "Terapi untuk Psikosis", summary: "Pilihan terapi yang efektif untuk psikosis.", author: "Dr. Anisa" },
            { title: "Mengelola Halusinasi", summary: "Strategi menghadapi pengalaman tidak nyata.", author: "Dr. Budi" },
            { title: "Obat-obatan Antipsikotik", summary: "Bagaimana obat membantu pemulihan.", author: "Dr. Cantika" },
          ]
        };
      }
      return {
        name: "Gejala Psikotik Berat",
        percentage: 95,
        recommendation: "Gejala yang Anda alami cukup berat. Segera konsultasikan dengan psikiater untuk penanganan lebih lanjut dan hindari bahaya bagi diri sendiri maupun orang lain.",
        description: "Psikosis berat meliputi halusinasi intens, delusi berat, dan perilaku yang sangat tidak sesuai dengan kenyataan. Penanganan medis segera sangat penting.",
        articles: [
            { title: "Penanganan Darurat Psikosis", summary: "Langkah-langkah penting saat krisis psikotik.", author: "Dr. Budi" },
            { title: "Peran Keluarga dalam Pemulihan", summary: "Bagaimana keluarga dapat membantu proses pemulihan.", author: "Dr. Anisa" },
            { title: "Mengenal Rehabilitasi Psikososial", summary: "Dukungan jangka panjang untuk pasien psikotik.", author: "Dr. Cantika" },
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
/* New gradient for Anxiety */


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
  background: #34D399; /* Green/Teal color for Anxiety */
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
  flex-direction: row;
  align-items: flex-start;
  gap: 60px;
}
.details-text { flex: 1.5; }
.details-illustration {
  flex: 1;
  max-width: 300px;
  margin-top: 80px;
  display: flex;
  justify-content: center;
}
.details-illustration img { width: 100%; max-width: 300px; }
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

/* Terapkan style info-block dan info-container dari neurosis-info-container agar konsisten */
.psychotic-info-container {
  margin-top: 32px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(106,76,155,0.08);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: #4facfe;
  margin-bottom: 24px;
  text-align: center;
}
.info-block {
  margin-bottom: 24px;
}
.info-block h4 {
  color: #00b4d8;
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
@media (max-width: 900px) {
  /* Removed psikotik-info-container styles */
  .info-title {
    font-size: 1.2rem;
  }
}
</style> 