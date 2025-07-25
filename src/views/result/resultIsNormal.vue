<template>
  <div class="result-page">
    <section class="result-hero normal-bg">
      <div class="result-container">
        <div class="result-content">
          <div class="title-block">
            <span class="title-line"></span>
            <h2>HASIL TES KAMU</h2>
          </div>
          <p class="subtitle">Kondisi kesehatan mental kamu saat ini:</p>
          <div class="diagnosis-card">
            <h3>Kondisi Mental Baik (Normal)</h3>
            <div class="progress-bar-container">
              <div class="progress-bar" :style="{ width: '20%' }"></div>
            </div>
            <div class="progress-labels">
              <span>Normal</span>
              <span>Sedang</span>
              <span>Tinggi</span>
            </div>
            <p class="recommendation"><strong>Selamat!</strong> Tidak ditemukan gejala gangguan mental yang signifikan. Tetap jaga kesehatan mental dan fisikmu, serta terus lakukan kebiasaan positif setiap hari.</p>
          </div>
        </div>
        <div class="result-illustration">
          <img src="/src/assets/logoNormal.png" alt="Normal illustration" />
        </div>
      </div>
    </section>

    <section class="details-section">
      <div class="details-container">
        <div class="details-text">
          <h3>Kesehatan Mentalmu Baik</h3>
          <h4>Kamu dalam kondisi mental yang sehat</h4>
          <p>Kamu tidak menunjukkan gejala gangguan mental yang signifikan. Pertahankan gaya hidup sehat, kelola stres dengan baik, dan tetap terhubung dengan orang-orang terdekat. Jika suatu saat merasa butuh bantuan, jangan ragu untuk berkonsultasi dengan profesional.</p>
          <div class="normal-info-container">
            <h2 class="info-title">Tips Menjaga Kesehatan Mental</h2>
            <div class="info-block">
              <ul>
                <li>Jaga pola tidur dan makan yang teratur</li>
                <li>Lakukan aktivitas fisik secara rutin</li>
                <li>Luangkan waktu untuk relaksasi dan hobi</li>
                <li>Jaga komunikasi dengan keluarga dan teman</li>
                <li>Kelola stres dengan teknik pernapasan atau meditasi</li>
                <li>Jangan ragu meminta bantuan jika diperlukan</li>
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
  </div>
  <ConfirmationModal
    :visible="showConfirmationModal"
    title="Konfirmasi Tes Ulang"
    message="Anda akan melakukan tes ulang dan hasil tes Anda sebelumnya akan dihapus. Apakah Anda yakin?"
    @confirm="handleConfirm"
    @cancel="handleCancel"
  />
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
import { testState } from '../../store/testState';

export default {
  name: 'ResultIsNormal',
  components: { ConfirmationModal },
  data() {
    return { showConfirmationModal: false };
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
    }
  },
  mounted() {
    console.log('ResultIsNormal mounted');
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
    
    console.log('Final testState.hasilTesTerakhir after mounted:', testState.hasilTesTerakhir);
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
.normal-bg {
  background: linear-gradient(120deg, #2c3e50 0%, #6dd5ed 100%); /* Dark blue to light blue */
}
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
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  margin: 24px 0;
  transition: border 0.3s, background 0.3s;
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
  background: linear-gradient(90deg, #6dd5ed 0%, #2c3e50 100%);
  height: 16px;
  border-radius: 8px;
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
  background-color: #6dd5ed;
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
  background-color: #2c3e50;
}
.normal-info-container {
  margin-top: 32px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(44,190,144,0.08);
  padding: 28px 24px;
}
.info-title {
  font-size: 2rem;
  font-weight: 700;
  color: #6dd5ed;
  margin-bottom: 24px;
  text-align: center;
}
.info-block {
  margin-bottom: 24px;
}
.info-block h4 {
  color: #6dd5ed;
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
  .info-title {
    font-size: 1.2rem;
  }
}
</style> 