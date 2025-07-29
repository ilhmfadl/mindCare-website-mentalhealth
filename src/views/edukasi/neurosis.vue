<template>
  <div class="edukasi-detail-bg">
    <div class="edukasi-detail-header-bg">
      <div class="edukasi-detail-header-center">
        <div class="edukasi-detail-header-content">
          <h1>Gejala Neurosis</h1>
          <p>Neurosis adalah gangguan mental ringan yang memengaruhi pikiran, emosi, dan perilaku, namun tidak sampai membuat seseorang kehilangan kontak dengan realita. Gejala ini sering muncul dalam bentuk kecemasan, fobia, atau depresi ringan.</p>
        </div>
        <div class="edukasi-detail-header-img">
          <img src="/src/assets/Depresi.png" alt="Ilustrasi Neurosis" />
        </div>
      </div>
    </div>
    <div class="edukasi-detail-content">
      <h2>Gejala Neurosis</h2>
      <hr />
      <div class="edukasi-detail-desc">
        <p>
          <b>Apa itu Neurosis?</b><br>
          Neurosis adalah istilah untuk berbagai gangguan mental ringan yang menyebabkan stres, kecemasan, atau perubahan suasana hati, namun tidak sampai menyebabkan delusi atau halusinasi. Orang dengan neurosis masih mampu membedakan kenyataan dan fantasi.
        </p>
        <p>
          <b>Ciri-ciri Gejala Neurosis:</b><br>
          - Kecemasan berlebihan tanpa sebab yang jelas<br>
          - Fobia (takut berlebihan pada objek/situasi tertentu)<br>
          - Depresi ringan atau perasaan murung berkepanjangan<br>
          - Mudah marah, gelisah, atau tegang<br>
          - Gangguan tidur (insomnia atau mimpi buruk)<br>
          - Gangguan makan (nafsu makan menurun/meningkat)<br>
          - Obsesif-kompulsif ringan (pikiran atau perilaku berulang yang mengganggu)<br>
          - Keluhan fisik tanpa sebab medis jelas (psikosomatis)
        </p>
        <p>
          <b>Contoh Gangguan Neurosis:</b><br>
          - Gangguan Kecemasan Umum (GAD)<br>
          - Fobia sosial atau spesifik<br>
          - Gangguan obsesif-kompulsif ringan (OCD ringan)<br>
          - Depresi ringan<br>
          - Gangguan psikosomatis
        </p>
        <p>
          <b>Kapan Harus Mencari Bantuan?</b><br>
          Jika gejala-gejala di atas mengganggu aktivitas sehari-hari atau hubungan sosial, sebaiknya konsultasikan ke profesional kesehatan mental. Penanganan dini dapat mencegah gejala berkembang menjadi lebih berat.
        </p>
      </div>
      <h3 style="margin-top:48px; color:#4B4B9F;">
        <router-link to="/edukasi?category=neurosis" style="color: #4B4B9F; text-decoration: none; cursor: pointer; transition: color 0.3s;" 
                     onmouseover="this.style.color='#8b5cf6'" 
                     onmouseout="this.style.color='#4B4B9F'">
          Artikel terkait : cari artikel tentang neurosis
        </router-link>
      </h3>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NeurosisDetail',
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

.edukasi-detail-header-bg {
  background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
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

[data-theme="dark"] .edukasi-detail-header-bg {
  background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
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

  
  