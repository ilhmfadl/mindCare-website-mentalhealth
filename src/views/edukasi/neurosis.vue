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
.edukasi-detail-bg {
  background: #faf7f3;
  min-height: 100vh;
}
/* HEADER FULL BLEED BACKGROUND */
.edukasi-detail-header-bg {
  width: 100vw;
  min-width: 100vw;
  background: linear-gradient(135deg, #cfd5f7 0%, #e0c3fc 100%);
  margin-left: 50%;
  transform: translateX(-50%);
  box-sizing: border-box;
  padding: 0;
  min-height: 420px;
}
.edukasi-detail-header-center {
  max-width: 1200px;
  margin: 96px auto 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 64px 48px 64px 48px;
  box-sizing: border-box;
  min-height: 340px;
  gap: 32px;
  border-radius: 0;
  background: transparent;
}
.edukasi-detail-header-content {
  flex: 1 1 0;
  max-width: 520px;
  padding: 0;
  z-index: 2;
  text-align: left;
}
.edukasi-detail-header-content h1 {
  font-size: 3rem;
  color: #4B4B9F;
  margin-bottom: 16px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
.edukasi-detail-header-content p {
  font-size: 1.35rem;
  color: #4a4a6a;
  margin-bottom: 0;
  line-height: 1.7;
}
.edukasi-detail-header-img {
  flex: 0 0 220px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  height: 220px;
  margin: 0;
  z-index: 2;
}
.edukasi-detail-header-img img {
  width: 340px;
  height: 340px;
  object-fit: contain;
  position: static;
}
.edukasi-detail-content {
  max-width: 1100px;
  margin: 0 auto;
  background: none;
  border-radius: 0;
  box-shadow: none;
  padding: 40px 32px 32px 32px;
  position: relative;
  z-index: 3;
}
.edukasi-detail-content h2 {
  color: #4B4B9F;
  font-size: 1.6rem;
  margin-bottom: 8px;
  font-weight: 700;
}
.edukasi-detail-content hr {
  border: none;
  border-top: 2px solid #e0e0e0;
  margin-bottom: 24px;
}
.edukasi-detail-desc p {
  font-size: 1.08rem;
  color: #333;
  margin-bottom: 18px;
  line-height: 1.7;
}


@media (max-width: 900px) {
  .edukasi-detail-header-center {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 220px;
    height: auto;
    padding: 36px 12px 24px 12px;
    margin-top: 48px;
    gap: 16px;
    border-radius: 0;
  }
  .edukasi-detail-header-content {
    padding: 0 8px 18px 8px;
    max-width: 100%;
    text-align: center;
  }
  .edukasi-detail-header-img {
    margin: 0;
    height: 200px;
    justify-content: center;
  }
  .edukasi-detail-header-img img {
    width: 180px;
    height: 180px;
  }
  .edukasi-detail-content {
    padding: 16px 6px 12px 6px;
    max-width: 100%;
  }
  .article-cards {
    flex-direction: column;
    gap: 14px;
    align-items: center;
    justify-content: center;
  }
  .article-card {
    width: 100%;
    max-width: 340px;
    min-width: 0;
    min-height: 120px;
    padding: 12px 8px 10px 8px;
  }
}
</style>

  