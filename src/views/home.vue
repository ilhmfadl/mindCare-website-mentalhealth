<template>
  <div class="home-page-container">
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
            <button class="btn-register" type="button">DAFTAR</button>
            <button class="btn-login" type="button">MASUK</button>
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
          <article class="card orange-gradient" aria-labelledby="schizophrenia-title">
            <img src="/src/assets/logocard1.png" alt="Logo Skizofrenia" class="card-logo-img" />
            <h3 id="schizophrenia-title">Skizofrenia</h3>
            <p>
              Gangguan mental serius yang menyebabkan kesulitan membedakan antara kenyataan dan halusinasi/delusi (pikiran tidak nyata).
            </p>
          </article>

          <article class="card purple-gradient featured-card" aria-labelledby="depression-title">
            <img src="/src/assets/logocard2.png" alt="Logo Depresi" class="card-logo-img" />
            <h3 id="depression-title">Depresi</h3>
            <p>
              Gangguan suasana hati yang membuat seseorang merasa sedih, putus asa, kehilangan semangat, dan sulit menikmati hal-hal yang biasa disukai.
            </p>
          </article>

          <article class="card red-gradient" aria-labelledby="anxiety-title">
            <img src="/src/assets/logocard3.png" alt="Logo Anxiety" class="card-logo-img" />
            <h3 id="anxiety-title">Anxiety</h3>
            <p>
              Kondisi saat seseorang merasa khawatir atau takut berlebihan, bahkan tanpa alasan yang jelas.
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

    <!-- DEBUG SECTION -->
    <section class="debug-section">
        <div class="home-container">
            <h3 style="text-align: center; margin-bottom: 20px;">Developer Shortcuts</h3>
            <div class="debug-buttons">
                <button @click="goToResult('ResultIsDepresi', 30)" class="debug-button">View Depresi Result (Score 30)</button>
                <button @click="goToResult('ResultIsAnxiety', 20)" class="debug-button">View Anxiety Result (Score 20)</button>
                <button @click="goToResult('ResultIsSkizofrenia', 25)" class="debug-button">View Skizofrenia Result (Score 25)</button>
                <button @click="goToResult('ResultIsBipolar', 22)" class="debug-button">View Bipolar Result (Score 22)</button>
                <button @click="goToResult('ResultIsGangguanMakan', 18)" class="debug-button">View G. Makan Result (Score 18)</button>
                <button @click="goToResult('ResultIsOCD', 28)" class="debug-button">View OCD Result (Score 28)</button>
                <button @click="goToResult('ResultIsPTSD', 26)" class="debug-button">View PTSD Result (Score 26)</button>
                <button @click="goToResult('ResultIsGangguanKepribadian', 15)" class="debug-button">View G. Kepribadian Result (Score 15)</button>
            </div>
        </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "MindCareLanding",
  methods: {
    goToResult(routeName, score) {
      this.$router.push({ name: routeName, params: { score: score } });
    },
    goToTesDiri() {
      this.$router.push({ name: 'TesDiri' });
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
  gap: 16px;
  margin-top: 2rem;
}
.btn-register, .btn-login, .btn-start {
  padding: 12px 32px;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  font-size: 1rem;
  transition: all 0.3s ease;
}
.btn-register {
  background-color: #f97316;
  color: white;
}
.btn-register:hover { background-color: #ea580c; }
.btn-login {
  background-color: transparent;
  border: 2px solid #f97316;
  color: #f97316;
}
.btn-login:hover {
  background-color: #f97316;
  color: white;
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
}
.btn-start {
  margin-top: 2rem;
  background-color: #EC744A;
  color: white;
}
.btn-start:hover { background-color: #ea580c; }

.debug-section {
    padding: 40px 0;
    background-color: #f0f0f0;
}
.debug-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}
.debug-button {
    padding: 10px 20px;
    border: 1px solid #ccc;
    background-color: #fff;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
}
.debug-button:hover {
    background-color: #e9e9e9;
    border-color: #aaa;
}


/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 900px) {
  .hero-content-wrapper {
    flex-direction: column-reverse;
    text-align: center;
  }
  .hero-left {
    align-items: center;
  }
  .hero-desc {
    max-width: 100%;
  }

  .test-content-wrapper {
    flex-direction: column;
    text-align: center;
  }
  .test-illustration { order: 1; }
  .test-content { order: 2; align-items: center; }
  .test-yourself-section .section-title { text-align: center; }
}

@media (max-width: 768px) {
    .section-title { font-size: 1.8rem; }
    .hero-left h1 { font-size: 2.5rem; }
}
</style>
