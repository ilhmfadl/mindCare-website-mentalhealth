<template>
  <div class="edukasi-detail-bg">
    <div class="edukasi-detail-header-bg-ptsd">
      <div class="edukasi-detail-header-center">
        <div class="edukasi-detail-header-content">
          <h1>Post-Traumatic Stress Disorder (PTSD)</h1>
          <p>Gangguan setelah mengalami peristiwa traumatis, seperti kecelakaan, bencana, atau kekerasan, yang menyebabkan kilas balik, mimpi buruk, dan kecemasan berat.</p>
        </div>
        <div class="edukasi-detail-header-img">
          <img src="/src/assets/PTSD.png" alt="Ilustrasi PTSD" />
        </div>
      </div>
    </div>
    <div class="edukasi-detail-content">
      <h2>Post-Traumatic Stress Disorder (PTSD)</h2>
      <hr />
      <div class="edukasi-detail-desc">
        <p>
          <b>Apa itu PTSD?</b><br>
          PTSD adalah gangguan mental yang muncul setelah seseorang mengalami atau menyaksikan peristiwa traumatis, seperti kecelakaan, bencana alam, kekerasan, atau kehilangan orang tercinta.
        </p>
        <p>
          <b>Gejala PTSD:</b><br>
          - Kilas balik (flashback) atau mimpi buruk tentang peristiwa traumatis.<br>
          - Menghindari hal-hal yang mengingatkan pada trauma.<br>
          - Mudah terkejut, cemas berlebihan, sulit tidur.<br>
          - Perubahan suasana hati, merasa terasing, atau mudah marah.
        </p>
        <p>
          <b>Penyebab PTSD:</b><br>
          Mengalami atau menyaksikan peristiwa yang sangat menakutkan, mengancam jiwa, atau menyakitkan secara emosional. Tidak semua orang yang mengalami trauma akan mengalami PTSD.
        </p>
        <p>
          <b>Kapan Harus Mencari Bantuan?</b><br>
          Jika gejala PTSD berlangsung lebih dari sebulan dan mengganggu kehidupan sehari-hari, sebaiknya konsultasikan ke profesional kesehatan mental.
        </p>
      </div>
      <h3 style="margin-top:48px; color:#4B4B9F;">
        <router-link to="/edukasi?category=ptsd" style="color: #4B4B9F; text-decoration: none; cursor: pointer; transition: color 0.3s;" 
                     onmouseover="this.style.color='#7a6ab9'" 
                     onmouseout="this.style.color='#4B4B9F'">
          Artikel terkait : cari artikel tentang ptsd
        </router-link>
      </h3>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PTSDDetail',
  data() {
    return {
    }
  },
}
</script>

<style scoped>
/* Light mode styles */
.edukasi-detail-bg {
  background-color: #fdfcfa;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

.edukasi-detail-header-bg-ptsd {
  background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
  padding: 80px 0;
  color: white;
}

.edukasi-detail-header-center {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  gap: 40px;
}

.edukasi-detail-header-content h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 16px;
  color: white;
}

.edukasi-detail-header-content p {
  font-size: 1.1rem;
  line-height: 1.6;
  opacity: 0.9;
  color: white;
}

.edukasi-detail-header-img img {
  max-width: 300px;
  height: auto;
}

.edukasi-detail-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 80px 24px;
  background-color: #fdfcfa;
}

.edukasi-detail-content h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 24px;
}

.edukasi-detail-content h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #4B4B9F;
  margin-bottom: 16px;
}

.edukasi-detail-desc {
  color: #555;
}

.edukasi-detail-desc p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #555;
  margin-bottom: 16px;
}

.edukasi-detail-desc b {
  color: #333;
  font-weight: 600;
}

/* Dark mode styles */
[data-theme="dark"] .edukasi-detail-bg {
  background-color: var(--bg-primary);
}

[data-theme="dark"] .edukasi-detail-header-bg-ptsd {
  background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
}

[data-theme="dark"] .edukasi-detail-header-content h1 {
  color: white;
}

[data-theme="dark"] .edukasi-detail-header-content p {
  color: white;
}

[data-theme="dark"] .edukasi-detail-content {
  background-color: var(--bg-primary);
}

[data-theme="dark"] .edukasi-detail-content h2 {
  color: var(--text-primary);
}

[data-theme="dark"] .edukasi-detail-content h3 {
  color: var(--accent-purple);
}

[data-theme="dark"] .edukasi-detail-desc {
  color: var(--text-secondary);
}

[data-theme="dark"] .edukasi-detail-desc p {
  color: var(--text-secondary);
}

[data-theme="dark"] .edukasi-detail-desc b {
  color: var(--text-primary);
}

@media (max-width: 768px) {
  .edukasi-detail-header-center {
    flex-direction: column;
    text-align: center;
    gap: 24px;
  }
  
  .edukasi-detail-header-content h1 {
    font-size: 2rem;
  }
  
  .edukasi-detail-header-content p {
    font-size: 1rem;
  }
  
  .edukasi-detail-content {
    padding: 40px 16px;
  }
  
  .edukasi-detail-content h2 {
    font-size: 1.5rem;
  }
  
  .edukasi-detail-content h3 {
    font-size: 1.25rem;
  }
  
  .edukasi-detail-desc p {
    font-size: 1rem;
  }
}
</style>

