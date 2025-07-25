<template>
  <div class="home-page-container">
    <div style="display: flex; gap: 12px; margin: 24px 0;">
      <button @click="$router.push({ name: 'ResultIsNeurosis', params: { score: 10 } })">Lihat Neurosis</button>
      <button @click="$router.push({ name: 'ResultIsPsychotic', params: { score: 10 } })">Lihat Psychotic</button>
      <button @click="$router.push({ name: 'ResultIsPTSD', params: { score: 10 } })">Lihat PTSD</button>
    </div>
    <section class="hero-section">
      <div class="home-container hero-content-wrapper">
        <div class="hero-left">
          <h1>
            Selamat Datang Di <br />
            <span class="highlight">MindCare</span>
          </h1>
          <p class="hero-text">
            Tempat mengenal, memahami, dan<br />
            merawat kesehatan mentalmu.
          </p>
          <div class="btn-group">
            <button v-if="!isLoggedIn" class="btn-register" type="button" @click="goToRegister">DAFTAR</button>
            <button v-if="!isLoggedIn" class="btn-login" type="button" @click="goToLogin">MASUK</button>
          </div>
          <p class="hero-desc">
            Apakah kamu sedang merasa stres, cemas, lelah secara mental, atau
            butuh teman bicara? MindCare hadir untuk membantumu mengelola
            perasaan dan menenangkan dirimu kembali.
          </p>
        </div>
        <div class="hero-right" aria-label="Landing page illustration">
          <img 
            src="/src/assets/LogoLandingPage.png"
            alt="Landing page MindCare"
            class="hero-landing-img"
            onerror="this.style.display='none'"
          />
        </div>
      </div>
    </section>

    <section class="problems-section">
      <div class="home-container">
        <h2 class="section-title">
          Apa saja masalah mental yang sering <br /> terjadi pada manusia?
        </h2>
        <div class="cards">
          <article class="card orange-gradient" aria-labelledby="neurosis-title">
            <img src="/src/assets/logocard1.png" alt="Logo Neurosis" class="card-logo-img" />
            <h3 id="neurosis-title">Gejala Neurosis</h3>
            <p>
              Gangguan mental ringan seperti kecemasan, fobia, atau depresi ringan yang memengaruhi pikiran dan perilaku, namun tidak sampai kehilangan kontak dengan realita.
            </p>
          </article>

          <article class="card purple-gradient featured-card" aria-labelledby="psikotik-title">
            <img src="/src/assets/logocard2.png" alt="Logo Psikotik" class="card-logo-img" />
            <h3 id="psikotik-title">Gejala Psikotik</h3>
            <p>
              Gangguan mental berat seperti halusinasi, delusi, atau skizofrenia yang menyebabkan seseorang kehilangan kontak dengan realita.
            </p>
          </article>

          <article class="card red-gradient" aria-labelledby="ptsd-title">
            <img src="/src/assets/logocard3.png" alt="Logo PTSD" class="card-logo-img" />
            <h3 id="ptsd-title">PTSD</h3>
            <p>
              Gangguan stres pascatrauma yang muncul setelah mengalami peristiwa traumatis, ditandai dengan kilas balik, mimpi buruk, dan kecemasan berat.
            </p>
          </article>
        </div>
      </div>
    </section>

    <section class="test-yourself-section">
      <div class="home-container test-content-wrapper">
        <div class="test-illustration" aria-label="Logo MindCare di test yourself">
          <img 
            src="/src/assets/LogoMulaiTesDiri.png"
            alt="Logo MindCare"
            class="test-logo-img"
            onerror="this.style.display='none'"
          />
          <div class="test-categories-row">
            <div class="test-category">
              <img src="/src/assets/logocard1.png" alt="Neurosis" class="test-category-img" />
              <div class="test-category-label">Neurosis</div>
            </div>
            <div class="test-category">
              <img src="/src/assets/logocard2.png" alt="Psikotik" class="test-category-img" />
              <div class="test-category-label">Psikotik</div>
            </div>
            <div class="test-category">
              <img src="/src/assets/logocard3.png" alt="PTSD" class="test-category-img" />
              <div class="test-category-label">PTSD</div>
            </div>
          </div>
        </div>
        <div class="test-content">
          <p class="test-question">Ingin mengetahui kondisi mental anda saat ini?</p>
          <h2 class="section-title">Mulai Tes Diri</h2>
          <p class="test-description">
            MindCare hadir untuk membantu anda memahami kondisi kesehatan mental Anda secara cepat dan mudah. Dengan mengisi kuisioner yang telah kami sediakan, Anda akan mendapatkan gambaran umum mengenai kesejahteraan psikologis Anda. Proses ini bersifat pribadi, aman, dan tidak membutuhkan waktu lama. Mulailah dengan hasil memeriksa kesehatan mental yang lebih baik bersama MindCare.
          </p>
          <button class="btn-start" type="button" @click="goToTesDiri">MULAI</button>
        </div>
      </div>
    </section>
    <section v-if="isLoggedIn && hasTestResult" class="tesdiri-result-section">
      <div class="home-container">
        <h2 class="section-title">Hasil Tes Diri Terakhir Anda</h2>
        <div class="tesdiri-result-card">
          <p><strong>Hasil:</strong> {{ lastTestResult.hasil }}</p>
          <p><strong>Tanggal Tes:</strong> {{ formatTanggal(lastTestResult.tanggal) }}</p>
          <button class="btn-detail" @click="goToDetailHasil">Lihat Detail Hasil Tes</button>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import { testState } from '../store/testState';
export default {
  name: "MindCareLanding",
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem('user');
    },
    hasTestResult() {
      return !!testState.hasilTesTerakhir;
    },
    lastTestResult() {
      return testState.hasilTesTerakhir || {};
    }
  },
  methods: {
    goToTesDiri() {
      this.$router.push({ name: 'TesDiri' });
    },
    goToRegister() {
      this.$router.push({ name: 'Register' });
    },
    goToLogin() {
      this.$router.push({ name: 'Login' });
    },
    goToDetailHasil() {
      const hasil = (this.lastTestResult.hasil || '').toLowerCase();
      if (hasil.includes('neurosis')) {
        this.$router.push({ name: 'ResultIsNeurosis' });
      } else if (hasil.includes('psikotik')) {
        this.$router.push({ name: 'ResultIsPsychotic' });
      } else if (hasil.includes('ptsd')) {
        this.$router.push({ name: 'ResultIsPTSD' });
      } else if (hasil.includes('normal')) {
        this.$router.push({ name: 'ResultIsNormal' });
      } else {
        this.$router.push({ name: 'TesDiri' });
      }
    },
    formatTanggal(tgl) {
      if (!tgl) return '-';
      const d = new Date(tgl);
      return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    },
    async fetchLastTestResult() {
      const user = localStorage.getItem('user');
      if (!user) {
        localStorage.removeItem('lastTestResult');
        testState.hasilTesTerakhir = null;
        return;
      }
      try {
        const parsed = JSON.parse(user);
        const formData = new FormData();
        formData.append('user_id', parsed.id);
        const res = await axios.post('https://mindcareindependent.com/api/get_last_tesdiri.php', formData);
        if (res.data.success && res.data.data) {
          localStorage.setItem('lastTestResult', JSON.stringify(res.data.data));
          testState.hasilTesTerakhir = res.data.data;
        } else {
          localStorage.removeItem('lastTestResult');
          testState.hasilTesTerakhir = null;
        }
      } catch (e) {
        localStorage.removeItem('lastTestResult');
        testState.hasilTesTerakhir = null;
      }
    }
  },
  async created() {
    if (this.isLoggedIn) {
      await this.fetchLastTestResult();
    }
  }
};
</script>

<style scoped>
/* GENERAL LAYOUT */
.home-page-container {
  width: 100%;
  overflow-x: hidden;
  font-family: 'Inter', sans-serif;
  background: #faf7f3;
  color: #374151;
}

/* A reusable container to center content and set max-width */
.home-container {
  width: 100%;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 24px;
  padding-right: 24px;
}

/* SECTION STYLING */
.hero-section, .problems-section, .test-yourself-section {
  padding-top: 80px;
  padding-bottom: 80px;
}

.section-title {
  text-align: center;
  font-size: 2.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 50px;
}

/* HERO SECTION */
.hero-content-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 48px;
  flex-wrap: wrap;
}
.hero-left {
  flex: 1 1 500px;
}
.hero-right {
  flex: 1 1 400px;
  display: flex;
  justify-content: center;
}
.hero-landing-img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}
.hero-left h1 {
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.2;
  color: #111827;
}
.highlight {
  color: #EC744A;
}
.hero-text {
  color: #6b7280;
  font-weight: 500;
  font-size: 1.25rem;
  line-height: 1.5;
  margin-top: 1rem;
}
.hero-desc {
  color: #6b7280;
  font-size: 1rem;
  line-height: 1.6;
  margin-top: 1.5rem;
  max-width: 90%;
}
.btn-group {
  display: flex;
  gap: 20px;
  margin-top: 2rem;
  justify-content: flex-start;
}
.btn-register, .btn-login, .btn-start {
  padding: 14px 36px;
  border-radius: 50px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  font-size: 1.08rem;
  transition: all 0.25s cubic-bezier(.4,0,.2,1);
  box-shadow: 0 2px 12px rgba(236,116,74,0.08), 0 1.5px 6px rgba(0,0,0,0.06);
  outline: none;
  letter-spacing: 0.5px;
}
.btn-register {
  background: linear-gradient(90deg, #f97316 0%, #EC744A 100%);
  color: #fff;
  border: none;
  box-shadow: 0 4px 16px rgba(249,115,22,0.13);
}
.btn-register:hover {
  background: linear-gradient(90deg, #ea580c 0%, #d97706 100%);
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 8px 24px rgba(249,115,22,0.18);
}
.btn-login {
  background: #fff;
  border: 2px solid #f97316;
  color: #f97316;
  box-shadow: 0 2px 12px rgba(249,115,22,0.07);
}
.btn-login:hover {
  background: #f97316;
  color: #fff;
  border-color: #ea580c;
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 8px 24px rgba(249,115,22,0.18);
}

/* PROBLEMS SECTION */
.problems-section {
  background-color: #faf7f3;
}
.cards {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
}
.card {
  flex: 1 1 300px;
  max-width: 340px;
  padding: 32px 24px;
  border-radius: 20px;
  color: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  z-index: 1;
}
.featured-card {
  transform: scale(1.05);
  z-index: 2;
}
.card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}
.card-icon {
  width: 50px;
  height: 50px;
  margin-bottom: 16px;
}
.card h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 1rem 0 0.5rem 0;
}
.card p {
  font-size: 0.95rem;
  line-height: 1.6;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.9);
}
.orange-gradient { background: linear-gradient(135deg, #fbbf24, #f97316); }
.purple-gradient { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
.red-gradient { background: linear-gradient(135deg, #f87171, #ef4444); }

.card-logo-img {
  width: 100px;
  height: 100px;
  object-fit: contain;
  margin-bottom: 8px;
  border-radius: 12px;
  background: transparent;
  box-shadow: none;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

/* TEST-YOURSELF SECTION */
.test-content-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 48px;
  flex-wrap: wrap;
}
.test-illustration {
  flex: 1 1 400px;
  order: 2; /* Image on the right */
}
.test-content {
  flex: 1 1 500px;
  order: 1; /* Text on the left */
}
.test-logo-img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}
.test-question {
  font-weight: 500;
  font-size: 1.25rem;
  color: #6b7280;
}
.test-yourself-section .section-title {
    text-align: left;
    margin-bottom: 1rem;
}
.test-description {
  font-weight: 400;
  font-size: 1rem;
  line-height: 1.6;
  color: #4b5563;
  margin-top: 1.5rem;
  text-align: justify;
  padding-right: 20px;
}
.btn-start {
  margin-top: 2rem;
  background-color: #EC744A;
  color: white;
  /* default: tidak center di desktop */
}
.btn-start:hover { background-color: #ea580c; }

.test-categories-row {
  display: none;
}

/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 900px) {
  .home-container {
    padding-left: 4px;
    padding-right: 4px;
    max-width: 100vw;
  }
  .hero-section, .problems-section, .test-yourself-section {
    padding-top: 18px;
    padding-bottom: 18px;
  }
  .hero-content-wrapper {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }
  .test-content-wrapper {
    flex-direction: row;
    align-items: center;
    gap: 22px;
    text-align: left;
  }
  .hero-left h1 {
    font-size: 1.08rem;
    margin-bottom: 4px;
    margin-top: 100px;
  }
  .section-title {
    font-size: 0.92rem;
    margin-bottom: 10px;
  }
  .hero-text, .hero-desc, .test-question, .test-description {
    font-size: 0.88rem;
  }
  .test-description {
    padding-right: 2vw;
  }
  .btn-group {
    flex-direction: column;
    gap: 4px;
    margin-top: 0.5rem;
    margin-right: 9px;
  }
  .btn-register, .btn-login, .btn-start {
    padding: 7px 12px;
    font-size: 0.89rem;
  }
  .cards {
    flex-direction: column;
    gap: 8px;
    align-items: center;
  }
  .card {
    max-width: 99vw;
    padding: 10px 4px;
    font-size: 0.87rem;
  }
  .card-logo-img {
    width: 38px;
    height: 38px;
    margin-bottom: 2px;
  }
  .test-logo-img {
    display: none;
  }
  .test-categories-row {
    display: flex;
    flex-direction: row;
    gap: 12px;
    justify-content: center;
    align-items: flex-end;
    width: 100%;
    margin-bottom: 8px;
  }
  .test-category {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 60px;
  }
  .test-category-img {
    width: 54px;
    height: 54px;
    object-fit: contain;
    border-radius: 8px;
    margin-bottom: 4px;
  }
  .test-category-label {
    font-size: 0.82rem;
    color: #6b7280;
    text-align: center;
    margin-top: 2px;
  }
  .problems-section {
    padding-top: 10px;
    padding-bottom: 10px;
  }
  .section-title {
    font-size: 0.8rem;
    margin-bottom: 6px;
  }
  .cards {
    gap: 4px;
  }
  .card {
    max-width: 99vw;
    padding: 6px 2px;
    font-size: 0.8rem;
    border-radius: 12px;
  }
  .card-logo-img {
    width: 28px;
    height: 28px;
    margin-bottom: 1px;
  }
  .card {
    transform: scale(0.92);
    font-size: 0.75rem;
    padding: 4px 1px;
    border-radius: 8px;
  }
  .test-illustration { order: 1; }
  .test-content { order: 2; align-items: center; }
  .test-yourself-section .section-title { text-align: center; }
  .btn-group {
    flex-direction: column;
    gap: 14px;
    align-items: center;
    justify-content: center;
  }
  .btn-register, .btn-login {
    width: 100%;
    min-width: 180px;
    padding: 14px 0;
    font-size: 1.08rem;
  }
  .btn-start {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
}

@media (max-width: 768px) {
    .section-title { font-size: 1.8rem; }
    .hero-left h1 { font-size: 2.5rem; }
}
.tesdiri-result-section {
  background: #f3f7fa;
  padding: 40px 0 0 0;
}
.tesdiri-result-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(106,76,155,0.08);
  padding: 28px 24px;
  max-width: 420px;
  margin: 0 auto 32px auto;
  text-align: center;
}
.btn-detail {
  background: #EC744A;
  color: white;
  padding: 10px 32px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  font-size: 1rem;
  margin-top: 18px;
  transition: all 0.3s ease;
}
.btn-detail:hover {
  background: #d9633a;
}
</style>
