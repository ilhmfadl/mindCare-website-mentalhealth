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
.edukasi-detail-bg {
  background: #faf7f3;
  min-height: 100vh;
}
.edukasi-detail-header-bg-ptsd {
  width: 100vw;
  min-width: 100vw;
  background: linear-gradient(135deg, #e7d5f7 0%, #d5e7f7 100%);
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
  color: #7a6ab9;
  margin-bottom: 16px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
.edukasi-detail-header-content p {
  font-size: 1.35rem;
  color: #5a4a7a;
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
  color: #7a6ab9;
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
