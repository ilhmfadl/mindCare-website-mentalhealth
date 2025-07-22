<template>
  <div class="edukasi-detail-bg">
    <div class="edukasi-detail-header-bg-eating">
      <div class="edukasi-detail-header-center">
        <div class="edukasi-detail-header-content">
          <h1>Eating Disorders</h1>
          <p>Masalah serius pada pola makan, seperti anoreksia (takut gemuk, makan sangat sedikit), bulimia (makan banyak lalu memuntahkan), atau binge eating (makan berlebihan).</p>
        </div>
        <div class="edukasi-detail-header-img">
          <img src="/src/assets/gangguan makan.png" alt="Ilustrasi Eating Disorders" />
        </div>
      </div>
    </div>
    <div class="edukasi-detail-content">
      <h2>Eating Disorders</h2>
      <hr />
      <div class="edukasi-detail-desc">
        <p>
          <b>Apa itu Eating Disorders?</b><br>
          Eating disorders adalah gangguan mental serius yang memengaruhi pola makan, citra tubuh, dan emosi terkait makanan. Contohnya: anoreksia nervosa, bulimia nervosa, dan binge eating disorder.
        </p>
        <p>
          <b>Gejala Eating Disorders:</b><br>
          - Takut berat badan naik, sangat membatasi makan (anoreksia).<br>
          - Makan berlebihan lalu memuntahkan (bulimia).<br>
          - Makan dalam jumlah sangat banyak dalam waktu singkat (binge eating).<br>
          - Terobsesi dengan berat badan, bentuk tubuh, atau kalori.<br>
          - Perubahan berat badan drastis, masalah kesehatan fisik.
        </p>
        <p>
          <b>Penyebab Eating Disorders:</b><br>
          Faktor genetik, tekanan sosial, pengalaman traumatis, masalah citra tubuh, atau stres berat dapat meningkatkan risiko eating disorders.
        </p>
        <p>
          <b>Kapan Harus Mencari Bantuan?</b><br>
          Jika pola makan atau pikiran tentang makanan/tubuh mulai mengganggu kesehatan fisik, emosi, atau kehidupan sehari-hari, segera konsultasikan ke profesional kesehatan mental.
        </p>
      </div>
      <h3 style="margin-top:48px; color:#4B4B9F;">Artikel Terkait :</h3>
      <section class="articles-section">
        <div class="articles-container">
          <div class="article-cards">
            <div class="article-card" v-for="article in relatedArticles" :key="article.title">
              <h4>{{ article.title }}</h4>
              <p>{{ article.summary }}</p>
              <button class="baca-selengkapnya-btn" type="button">Baca Selengkapnya</button>
              <div class="author" v-if="article.author">
                <span>{{ article.author }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EatingDetail',
  data() {
    return {
      relatedArticles: [
        { title: 'Mengenal Anoreksia, Bulimia, dan Binge Eating', summary: 'Penjelasan perbedaan dan ciri khas tiga jenis eating disorders utama.', author: 'Dr. Lestari' },
        { title: 'Dampak Eating Disorders pada Kesehatan Fisik', summary: 'Risiko kesehatan jangka panjang akibat gangguan makan.', author: 'Dr. Yoga' },
        { title: 'Cara Mendukung Teman dengan Eating Disorders', summary: 'Tips membantu orang terdekat yang mengalami gangguan makan.', author: 'Dr. Siska' },
      ]
    }
  },
}
</script>

<style scoped>
.edukasi-detail-bg {
  background: #faf7f3;
  min-height: 100vh;
}
.edukasi-detail-header-bg-eating {
  width: 100vw;
  min-width: 100vw;
  background: linear-gradient(135deg, #f7ecd5 0%, #f7d5d5 100%);
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
  color: #b98a6a;
  margin-bottom: 16px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
.edukasi-detail-header-content p {
  font-size: 1.35rem;
  color: #7a6a5a;
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
  color: #b98a6a;
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
.articles-section {
  margin-top: 56px;
  margin-bottom: 32px;
}
.articles-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 16px;
}
.article-cards {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  justify-content: center;
}
.article-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(106,76,155,0.10), 0 1.5px 6px rgba(0,0,0,0.06);
  padding: 18px 12px 14px 12px;
  width: 30%;
  min-width: 220px;
  max-width: 320px;
  min-height: 140px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 12px;
  transition: box-shadow 0.25s, transform 0.25s;
  border: 1.5px solid #f3f0fa;
  font-size: 0.97rem;
}
.article-card:hover {
  box-shadow: 0 8px 32px rgba(106,76,155,0.18), 0 2px 8px rgba(0,0,0,0.10);
  transform: translateY(-6px) scale(1.03);
}
.article-card h4 {
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 8px;
}
.article-card p {
  font-size: 0.97rem;
  margin-bottom: 8px;
}
.article-card .author {
  font-size: 0.98rem;
  color: #b98a6a;
  font-weight: 600;
  margin-top: auto;
  text-align: left;
}
.baca-selengkapnya-btn {
  background: none;
  color: #b98a6a;
  border: none;
  font-weight: 600;
  font-size: 1.01rem;
  cursor: pointer;
  padding: 0;
  margin-bottom: 8px;
  text-decoration: underline;
  transition: color 0.2s;
}
.baca-selengkapnya-btn:hover {
  color: #222;
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
