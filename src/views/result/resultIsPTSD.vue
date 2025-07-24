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
  name: 'ResultIsPTSD',
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
      // NOTE: Score ranges for PTSD might differ.
      // This is placeholder logic.
      if (s <= 14) {
        return {
          name: "Tidak Ada Indikasi PTSD",
          percentage: 20,
          recommendation: "Anda tampaknya memproses pengalaman sulit dengan baik. Pertahankan mekanisme koping yang sehat.",
          description: "Adalah normal untuk merasa terpengaruh oleh peristiwa traumatis, tetapi kebanyakan orang pulih seiring waktu tanpa mengembangkan PTSD.",
          articles: [
            { title: "Membangun Resiliensi Diri", summary: "Kemampuan untuk bangkit kembali...", author: "Dr. Putra" },
            { title: "Pentingnya Dukungan Sosial", summary: "Peran teman dan keluarga dalam pemulihan...", author: "Dr. Queen" },
            { title: "Journaling untuk Memproses Emosi", summary: "Menulis sebagai alat terapi...", author: "Dr. Rama" },
          ]
        };
      }
      if (s <= 24) {
        return {
          name: "Kecenderungan Stres Pasca-Trauma",
          percentage: 50,
          recommendation: "Anda mungkin mengalami beberapa gejala sisa dari peristiwa traumatis. Penting untuk mempraktikkan perawatan diri dan mempertimbangkan dukungan profesional.",
          description: "Gejala seperti mimpi buruk, kewaspadaan berlebih, atau menghindari pengingat trauma mungkin muncul. Ini adalah respons stres yang umum tetapi memerlukan perhatian.",
          articles: [
            { title: "Teknik Grounding saat Cemas", summary: "Menstabilkan diri di saat-saat sulit...", author: "Dr. Queen" },
            { title: "Memahami Respon Fight-Flight-Freeze", summary: "Reaksi alami tubuh terhadap trauma...", author: "Dr. Putra" },
            { title: "Kapan Gejala Stres Menjadi Masalah?", summary: "Mengenali batas antara normal dan gangguan...", author: "Dr. Rama" },
          ]
        };
      }
      return {
        name: "Indikasi Kuat PTSD",
        percentage: 85,
        recommendation: "Gejala Anda sangat konsisten dengan PTSD. Terapi yang berfokus pada trauma, seperti EMDR atau TF-CBT, sangat direkomendasikan.",
        description: "Post-Traumatic Stress Disorder (PTSD) dapat berkembang setelah mengalami atau menyaksikan peristiwa traumatis. Gejalanya meliputi ingatan yang mengganggu, penghindaran, perubahan mood negatif, dan gairah yang meningkat.",
        articles: [
            { title: "Apa itu Terapi EMDR?", summary: "Eye Movement Desensitization and Reprocessing...", author: "Dr. Putra" },
            { title: "Trauma-Focused CBT (TF-CBT)", summary: "Terapi efektif untuk anak dan dewasa...", author: "Dr. Queen" },
            { title: "Hidup dengan PTSD Jangka Panjang", summary: "Manajemen gejala dan pemulihan...", author: "Dr. Rama" },
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
/* Styles can be customized for PTSD */
.result-page {
  background-color: #fdfcfa;
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

/* Tambahkan style info-block dan info-container agar konsisten dengan neurosis dan psikotik */
.ptsd-info-container {
  margin-top: 32px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(44,62,80,0.08);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: #3498db;
  margin-bottom: 24px;
  text-align: center;
}
.info-block {
  margin-bottom: 24px;
}
.info-block h4 {
  color: #2c3e50;
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
