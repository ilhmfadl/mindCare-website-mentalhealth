<template>
  <div class="edukasi-detail-bg">
    <div class="edukasi-detail-header-bg-anxiety">
      <div class="edukasi-detail-header-center">
        <div class="edukasi-detail-header-content">
          <h1>Gejala Psikotik</h1>
          <p>Psikosis adalah gangguan mental berat yang menyebabkan seseorang kehilangan kontak dengan realita, sering kali ditandai dengan halusinasi, delusi, dan perubahan perilaku drastis.</p>
        </div>
        <div class="edukasi-detail-header-img">
          <img src="/src/assets/anxiety.png" alt="Ilustrasi Psikotik" />
        </div>
      </div>
    </div>
    <div class="edukasi-detail-content">
      <h2>Gejala Psikotik</h2>
      <hr />
      <div class="edukasi-detail-desc">
        <p>
          <b>Apa itu Psikosis?</b><br>
          Psikosis adalah kondisi mental serius di mana seseorang mengalami gangguan dalam membedakan kenyataan dan fantasi. Orang dengan gejala psikotik bisa mengalami halusinasi (melihat/mendengar sesuatu yang tidak nyata) atau delusi (mempercayai hal yang tidak benar).
        </p>
        <p>
          <b>Ciri-ciri Gejala Psikotik:</b><br>
          - Halusinasi: mendengar suara, melihat, atau merasakan sesuatu yang tidak nyata<br>
          - Delusi: keyakinan kuat yang tidak sesuai kenyataan (misal: merasa diawasi, merasa punya kekuatan khusus)<br>
          - Pikiran kacau, sulit berbicara dengan jelas<br>
          - Perubahan perilaku drastis, menarik diri dari lingkungan<br>
          - Emosi datar atau tidak sesuai situasi<br>
          - Sulit membedakan mana yang nyata dan tidak
        </p>
        <p>
          <b>Contoh Gangguan Psikotik:</b><br>
          - Skizofrenia<br>
          - Gangguan delusi<br>
          - Gangguan skizoafektif<br>
          - Psikosis akibat penggunaan zat/obat
        </p>
        <p>
          <b>Kapan Harus Mencari Bantuan?</b><br>
          Jika seseorang mengalami gejala-gejala di atas, segera konsultasikan ke profesional kesehatan mental. Penanganan dini sangat penting untuk mencegah komplikasi dan membantu pemulihan.
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
  name: 'PsikotikDetail',
  data() {
    return {
      relatedArticles: [
        { title: 'Mengenal Halusinasi dan Delusi', summary: 'Penjelasan tentang dua gejala utama psikosis dan cara mengenalinya.', author: 'Dr. Nino' },
        { title: 'Peran Keluarga dalam Pemulihan Psikosis', summary: 'Bagaimana dukungan keluarga sangat penting dalam proses pemulihan.', author: 'Dr. Maya' },
        { title: 'Psikosis Akibat Zat: Apa yang Harus Dilakukan?', summary: 'Langkah-langkah jika psikosis dipicu oleh penggunaan obat/zat tertentu.', author: 'Dr. Olivia' },
      ]
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

.edukasi-detail-header-bg-anxiety {
  background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
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

.articles-section {
  background-color: #faf7f3;
  padding: 80px 24px;
}

.articles-container {
  max-width: 1200px;
  margin: 0 auto;
}

.article-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.article-card {
  background: white;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 20px;
  text-align: left;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.article-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.article-card h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
}

.article-card p {
  color: #555;
  line-height: 1.6;
  margin-bottom: 16px;
}

.baca-selengkapnya-btn {
  background-color: #EC744A;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.baca-selengkapnya-btn:hover {
  background-color: #d9633a;
}

.author {
  margin-top: 12px;
  font-size: 0.9rem;
  color: #777;
}

/* Dark mode styles */
[data-theme="dark"] .edukasi-detail-bg {
  background-color: var(--bg-primary);
}

[data-theme="dark"] .edukasi-detail-header-bg-anxiety {
  background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
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

[data-theme="dark"] .articles-section {
  background-color: var(--bg-tertiary);
}

[data-theme="dark"] .article-card {
  background: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 4px 15px var(--shadow-light);
}

[data-theme="dark"] .article-card h4 {
  color: var(--text-primary);
}

[data-theme="dark"] .article-card p {
  color: var(--text-secondary);
}

[data-theme="dark"] .baca-selengkapnya-btn {
  background-color: var(--button-primary);
  color: white;
}

[data-theme="dark"] .author {
  color: var(--text-muted);
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
  
  .articles-section {
    padding: 40px 16px;
  }
  
  .article-cards {
    grid-template-columns: 1fr;
  }
}
</style>

