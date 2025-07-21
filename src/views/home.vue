<template>
  <div class="container">
    <section class="hero">
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
          Apakah kamu sedang merasa stres, cemas, lelah secara mental, atau<br />
          butuh teman bicara? MindCare hadir untuk membantumu mengelola<br />
          perasaan dan menenangkan dirimu kembali.
        </p>
        <div class="social-logos" aria-label="Logos of social platforms">
          <span class="social-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#6B7280" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
              <circle cx="12" cy="12" r="10" stroke="none" opacity="0.2"/>
              <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 14.1a4.1 4.1 0 1 1 4.1-4.1 4.1 4.1 0 0 1-4.1 4.1Z" fill="#6B7280"/>
            </svg>
          </span>
          <span class="social-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#6B7280" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
              <rect width="20" height="20" x="2" y="2" rx="2" ry="2" fill-opacity="0.2"/>
              <path d="M7 10h10v4H7z" fill="#6B7280"/>
              <circle cx="12" cy="12" r="3" fill="#6B7280"/>
            </svg>
          </span>
          <span class="social-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#6B7280" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
              <path d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9z" fill-opacity="0.2"/>
              <path d="M16 12a4 4 0 1 1-4-4 4 4 0 0 1 4 4z" fill="#6B7280"/>
            </svg>
          </span>
          <span class="social-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#6B7280" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
              <rect width="20" height="20" x="2" y="2" rx="3" ry="3" fill-opacity="0.2"/>
              <path d="M8 8h8v8H8z" fill="#6B7280"/>
            </svg>
          </span>
        </div>
      </div>
      <div class="hero-right" aria-label="Landing page illustration">
        <img 
          src="/src/assets/LogoLandingPage.png"
          alt="Landing page MindCare"
          class="hero-landing-img"
          onerror="this.style.display='none'"
        />
      </div>
    </section>

    <section class="problems">
      <h2 class="problems-heading" aria-label="Title and subtitle about mental health issues"> 
        Apa saja masalah mental yang sering <br /> terjadi pada manusia?
      </h2>
      <div class="cards">
        <article class="card orange-gradient" aria-labelledby="schizophrenia-title">
          <svg class="card-icon" viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false">
            <circle cx="32" cy="32" r="30" stroke="#fff" stroke-opacity="0.5" stroke-width="2"/>
            <path d="M22 22c4-6 12-6 16 0" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M22 42h20" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M30 12v40" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <h3 id="schizophrenia-title">Skizofrenia</h3>
          <p>
            Gangguan mental serius yang menyebabkan kesulitan membedakan antara kenyataan dan halusinasi/delusi (pikiran tidak nyata).
          </p>
        </article>

        <article class="card purple-gradient featured-card" aria-labelledby="depression-title">
          <svg class="card-icon" viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false">
            <circle cx="32" cy="32" r="30" stroke="#fff" stroke-opacity="0.6" stroke-width="2"/>
            <path d="M32 10a12 12 0 1 1 0 24 12 12 0 0 1 0-24z" stroke="#fff" stroke-width="2"/>
            <path d="M32 22v18" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M38 22a6 6 0 0 1-12 0" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <h3 id="depression-title">Depresi</h3>
          <p>
            Gangguan suasana hati yang membuat seseorang merasa sedih, putus asa, kehilangan semangat, dan sulit menikmati hal-hal yang biasa disukai.
          </p>
        </article>

        <article class="card red-gradient" aria-labelledby="anxiety-title">
          <svg class="card-icon" viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false">
            <circle cx="32" cy="32" r="30" stroke="#fff" stroke-opacity="0.5" stroke-width="2"/>
            <path d="M24 26a10 10 0 0 1 16 0" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M32 36v10" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M22 44h20" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <h3 id="anxiety-title">Anxiety</h3>
          <p>
            Kondisi saat seseorang merasa khawatir atau takut berlebihan, bahkan tanpa alasan yang jelas.
          </p>
        </article>
      </div>
    </section>

    <section class="test-yourself">
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
        <h2 class="test-title">Mulai Tes Diri</h2>
        <p class="test-description">
          MindCare hadir untuk membantu anda memahami kondisi kesehatan mental Anda secara cepat dan mudah. Dengan mengisi kuisioner yang telah kami sediakan, Anda akan mendapatkan gambaran umum mengenai kesejahteraan psikologis Anda. Proses ini bersifat pribadi, aman, dan tidak membutuhkan waktu lama. Mulailah dengan hasil memeriksa kesehatan mental yang lebih baik bersama MindCare.
        </p>
        <button class="btn-start" type="button">MULAI</button>
      </div>
    </section>
  </div>
</template>

<script>

export default {
  name: "MindCareLanding",
};
</script>

<style scoped>
/* Container base */
.container {
  width: 100vw;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
    Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  background: #fcfaf7; /* Ganti warna sesuai keinginan, misal: #fcfaf7 */
  color: #374151;
  user-select: none;
  box-sizing: border-box;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 3vw;
  border-bottom: 1px solid #E5E7EB;
  flex-wrap: wrap;
  width: 100%;
  box-sizing: border-box;
  background: transparent;
  min-width: 0;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 0;
}

.logo-icon {
  width: 2rem;
  height: 2rem;
  stroke: #4B5563;
  stroke-width: 1.5;
}

.logo-text {
  font-weight: 700;
  font-size: 1.25rem;
  color: #111827;
  user-select: text;
  white-space: nowrap;
}

.nav ul {
  list-style: none;
  display: flex;
  gap: 2rem;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
}

.nav ul li a {
  color: #374151;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.btn-contact {
  padding: 0.6rem 1.5rem;
  background-color: #EC744A;
  color: white;
  font-weight: 600;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
  white-space: nowrap;
  user-select: none;
  font-size: 1rem;
}

/* Hero Section */
.hero {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2.5rem;
  gap: 3vw;
  flex-wrap: wrap;
  width: 100%;
  min-width: 0;
}

.hero-left {
  flex: 1 1 350px;
  max-width: 40vw;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-left: 3vw;
  min-width: 260px;
}

.hero-left h1 {
  font-size: 2.5rem;
  font-weight: 800;
  line-height: 1.1;
  color: #111827;
  margin-bottom: 0;
  letter-spacing: 1px;
  word-break: break-word;
}

.highlight {
  color: #EC744A; /* orange-500 */
  user-select: text;
}

.hero-text {
  color: #6b7280;
  font-weight: 600;
  font-size: 1.3rem;
  line-height: 1.4;
}

.hero-desc {
  color: #6b7280;
  font-size: 1.1rem;
  line-height: 1.6;
  user-select: text;
  max-width: 90%;
  margin-top: 0.5rem;
}

.hero-right {
  flex: 1 1 350px;
  max-width: 40vw;
  min-width: 260px;
  margin-right: 5px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  user-select: none;
}

.hero-landing-img {
  margin-left: 100px;
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  user-select: none;
  display: block;
}

/* Problems section */
.problems {
  margin-top: 5rem;
  text-align: center;
  user-select: text;
}

.problems-heading {
  font-size: 1.375rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 3.25rem;
  user-select: text;
}

.cards {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
}

.card {
  flex: 1 1 300px;
  max-width: 320px;
  padding: 2rem 1.5rem;
  border-radius: 20px;
  color: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: 
    transform 0.3s cubic-bezier(.4,2,.6,1), 
    box-shadow 0.3s cubic-bezier(.4,2,.6,1),
    z-index 0.3s;
  cursor: default;
  background: #fff;
  z-index: 1;
}

.featured-card {
  flex: 1.3 1.3 400px;
  max-width: 420px;
  padding: 2.8rem 2.2rem;
  z-index: 2;
  transform: scale(1.08);
  box-shadow: 0 15px 40px rgba(0,0,0,0.18);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

.card-icon {
  width: 50px;
  height: 50px;
  margin-bottom: 12px;
}

.card h3 {
  font-size: 1.2rem;
  font-weight: 700;
  margin: 1rem 0 0.5rem 0;
}

.card p {
  font-size: 0.95rem;
  line-height: 1.5;
  font-weight: 400;
  color: white;
}


/* Gradient backgrounds */
.orange-gradient {
  background: linear-gradient(135deg, #fbbf24, #f97316);
}

.purple-gradient {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
}

.red-gradient {
  background: linear-gradient(135deg, #f87171, #ef4444);
}

/* Test-yourself section */
.test-yourself {
  margin-top: 6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 3vw;
  flex-wrap: wrap;
  width: 100%;
  min-width: 0;
  padding: 0 1rem;
}

.test-illustration {
  flex: 1 1 350px;
  max-width: 40vw;
  min-width: 260px;
  display: flex;
  align-items: center;
  justify-content: flex-start; /* gambar ke kiri */
  user-select: none;
}

.test-illustration img {
  border-radius: 8px;
  max-width: 100%;
  height: auto;
  display: block;
  margin-left: 50px; /* sama seperti hero-landing-img */
}

.test-logo-img {
  max-width: 100%;
  
  width: auto;
  height: auto;
  display: block;
  margin-left: 100px; /* sama seperti .hero-landing-img */
  border-radius: 8px;
  user-select: none;
}
.test-content {
  flex: 1 1 350px;
  max-width: 40vw;
  min-width: 260px;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-left: 3vw; /* sama seperti hero-left */
  margin-right: 100px;
}

.test-title {
  font-weight: 700;
  font-size: 2.5rem; /* sama seperti hero-left h1 */
  color: #111827;
  margin: 0 0 1rem 0;
  user-select: text;
  letter-spacing: 1px;
  word-break: break-word;
}

.test-question {
  font-weight: 600;
  font-size: 1.3rem; /* sama seperti hero-text */
  color: #6b7280;
  margin: 0;
  user-select: text;
}

.test-description {
  font-weight: 400;
  font-size: 1.1rem;
  line-height: 1.6;
  color: #4b5563;
  user-select: text;
  max-width: 90%;
  margin-top: 0.5rem;
}

.btn-start {
  margin-top: 12px;
  width: max-content;
  padding: 10px 28px;
  background-color: #EC744A;
  color: white;
  font-weight: 600;
  font-size: 1rem;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  user-select: none;
  align-self: flex-start;
  transition: background-color 0.3s ease;
}

.btn-start:hover,
.btn-start:focus-visible {
  background-color: #ea580c;
  outline-offset: 4px;
  outline-color: #ea580c;
}
.btn-group {
  display: flex;
  gap: 16px;
  margin-top: 1rem;
}

.btn-register,
.btn-login {
  padding: 10px 28px;
  border-radius: 9999px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  user-select: none;
  font-size: 0.9rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-register {
  background-color: #f97316;
  color: white;
}

.btn-register:hover,
.btn-register:focus-visible {
  background-color: #ea580c;
  outline-offset: 4px;
  outline-color: #ea580c;
}

.btn-login {
  background-color: transparent;
  border: 2px solid #f97316;
  color: #f97316;
}

.btn-login:hover,
.btn-login:focus-visible {
  background-color: #f97316;
  color: white;
  outline-offset: 4px;
  outline-color: #f97316;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .hero-left,
  .hero-right {
    max-width: 100vw;
  }
}

@media (max-width: 900px) {
  .hero {
    flex-direction: column-reverse;
    align-items: center;
    gap: 2rem;
  }
  .hero-left,
  .hero-right {
    margin-left: 0;
    margin-right: 0;
    max-width: 90vw;
    min-width: 0;
  }
  .hero-left {
    align-items: center;
    text-align: center;
  }
  .hero-right {
    justify-content: center;
  }
  .test-yourself {
    flex-direction: column-reverse;
    align-items: center;
    gap: 2rem;
  }
  .test-illustration,
  .test-content {
    margin-left: 0;
    margin-right: 0;
    max-width: 90vw;
    min-width: 0;
  }
  .test-content {
    align-items: center;
    text-align: center;
  }
  .test-illustration {
    justify-content: center;
  }
  .test-illustration img {
    margin-left: 0;
  }
  .test-title {
    font-size: 1.75rem;
  }
  .test-question {
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem 2vw;
  }
  .nav ul {
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
  }
  .container {
    padding: 0;
  }
  .hero-left h1 {
    font-size: 1.5rem;
  }
  .hero-text {
    font-size: 1rem;
  }
  .hero-left,
  .hero-right {
    max-width: 100vw;
    min-width: 0;
  }
  .test-title {
    font-size: 1.2rem;
  }
  .test-question {
    font-size: 1rem;
  }
}
</style>
