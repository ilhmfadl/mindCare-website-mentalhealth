<template>
  <div class="edukasi-bg">
    <div class="edukasi-container edukasi-title-container">
      <h1 class="edukasi-title">Jenis Gangguan Mental</h1>
    </div>
    <!-- Kategori-section keluar dari .edukasi-container agar full-bleed -->
    <div v-for="(item, idx) in topics" :key="item.title" :class="['kategori-section', item.bgClass, { 
      'kategori-section--top': idx === 0, 
      'kategori-section--right': idx % 2 === 1, 
      'kategori-section--bottom': idx === topics.length - 1,
      'kategori-section--title-up': ['PTSD','OCD','Anxiety','Schizophrenia'].includes(item.title)
    }]">
      <router-link v-if="idx === 0" :to="'/edukasi/Depresi'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 1" :to="'/edukasi/anxiety'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 2" :to="'/edukasi/skizofrenia'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 3" :to="'/edukasi/bipolar'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 4" :to="'/edukasi/personality'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 5" :to="'/edukasi/ocd'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 6" :to="'/edukasi/eating'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <router-link v-else-if="idx === 7" :to="'/edukasi/ptsd'" style="display:block;text-decoration:none;">
        <div class="kategori-content" style="cursor:pointer;">
          <div class="kategori-bg-title">{{ item.title }}</div>
          <div class="kategori-img-wrapper">
            <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
          </div>
          <div class="kategori-desc">{{ item.desc }}</div>
        </div>
      </router-link>
      <div v-else class="kategori-content">
        <div class="kategori-bg-title">{{ item.title }}</div>
        <div class="kategori-img-wrapper">
          <img :src="item.img" :alt="'Ilustrasi ' + item.title" class="kategori-img-overlap" />
        </div>
        <div class="kategori-desc">{{ item.desc }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PojokEdukasi',
  data() {
    return {
      topics: [
        {
          title: 'Depression',
          desc: 'Gangguan suasana hati yang membuat seseorang merasa sangat sedih, putus asa, kehilangan minat, dan sulit menikmati aktivitas sehari-hari.',
          img: '/src/assets/Depresi.png',
          bgClass: 'bg-depresi',
          colorClass: 'color-depresi',
        },
        {
          title: 'Anxiety',
          desc: 'Kondisi saat seseorang merasa cemas atau takut berlebihan secara terus-menerus, bahkan tanpa alasan yang jelas, sering disertai gejala fisik seperti jantung berdebar atau sulit tidur.',
          img: '/src/assets/anxiety.png',
          bgClass: 'bg-anxiety',
          colorClass: 'color-anxiety',
        },
        {
          title: 'Schizophrenia',
          desc: 'Gangguan mental serius yang menyebabkan penderitanya sulit membedakan antara kenyataan dan halusinasi/delusi (pikiran tidak nyata).',
          img: '/src/assets/skizifernia.png',
          bgClass: 'bg-skizofrenia',
          colorClass: 'color-skizofrenia',
        },
        {
          title: 'Bipolar Disorder',
          desc: 'Gangguan yang ditandai perubahan suasana hati ekstrem, dari episode mania (sangat bersemangat) ke depresi (sangat sedih) secara bergantian.',
          img: '/src/assets/bipolar.png',
          bgClass: 'bg-bipolar',
          colorClass: 'color-bipolar',
        },
        {
          title: 'Personality Disorders',
          desc: 'Pola pikir, perasaan, dan perilaku yang tidak fleksibel dan menyulitkan seseorang dalam berinteraksi sosial atau menjalani kehidupan sehari-hari.',
          img: '/src/assets/gangguan kepribadian.png',
          bgClass: 'bg-ocd',
          colorClass: 'color-ocd',
        },
        {
          title: 'OCD',
          desc: 'Gangguan yang ditandai pikiran obsesif (mengganggu) dan perilaku kompulsif (berulang) untuk mengurangi kecemasan, seperti mencuci tangan berulang kali.',
          img: '/src/assets/OCD.png',
          bgClass: 'bg-ptsd',
          colorClass: 'color-ptsd',
        },
        {
          title: 'Eating Disorders',
          desc: 'Masalah serius pada pola makan, seperti anoreksia (takut gemuk, makan sangat sedikit), bulimia (makan banyak lalu memuntahkan), atau binge eating (makan berlebihan).',
          img: '/src/assets/gangguan makan.png',
          bgClass: 'bg-eating',
          colorClass: 'color-eating',
        },
        {
          title: 'PTSD',
          desc: 'Gangguan setelah mengalami peristiwa traumatis, seperti kecelakaan, bencana, atau kekerasan, yang menyebabkan kilas balik, mimpi buruk, dan kecemasan berat.',
          img: '/src/assets/PTSD.png',
          bgClass: 'bg-fobia',
          colorClass: 'color-fobia',
        },
      ]
    }
  }
}
</script>

<style scoped>
.edukasi-bg {
  min-height: 100vh;
  background-color: #f5f5f5;
  padding: 0;
  position: relative;
  /* Subtle pattern overlay */
  background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" fill="%23f7e3f7" fill-opacity="0.12"/><circle cx="20" cy="20" r="2" fill="%23e3f7f7" fill-opacity="0.18"/></svg>');
}
.edukasi-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 0 60px 0;
}
.edukasi-container.edukasi-title-container {
  padding: 32px 0 80px 0;
}
.edukasi-title {
  margin-top: 120px;
  text-align: center;
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 2px;
  color: #4e4e4e;
  letter-spacing: 1px;
  background: none;
  -webkit-background-clip: unset;
  -webkit-text-fill-color: unset;
  background-clip: unset;
}
.kategori-svg-top, .kategori-svg-bottom {
  width: 100%;
  position: absolute;
  left: 0;
  z-index: 1;
  line-height: 0;
}
.kategori-svg-top {
  top: 0;
  transform: translateY(-100%);
}
.kategori-svg-bottom {
  bottom: 0;
  transform: translateY(100%);
}
.kategori-section {
  position: relative;
  min-height: 400px;
  padding: 60px 0 56px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  width: 100vw;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 0;
}
.kategori-section::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background: linear-gradient(120deg, rgba(255,255,255,0.12) 0%, rgba(236,116,74,0.10) 100%);
  opacity: 0.7;
}
.kategori-section--right {
  transform: translateX(-50%);
}
.kategori-section--top {
  transform: translateX(-50%);
  clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
}
.kategori-section--top .kategori-content {
  /* transform: none !important; */
}
.kategori-section--bottom {
  transform: translateX(-50%) !important;
  clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
}
.kategori-section--bottom .kategori-content {
  transform: none;
}
.kategori-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 260px;
  background: rgba(255,255,255,0.85); /* default fallback */
  border-radius: 32px;
  box-shadow: 0 2px 16px 0 rgba(44,62,80,0.07);
  padding: 32px 24px 24px 24px;
  transition: box-shadow 0.3s, transform 0.3s;
  border: none;
}
.kategori-content:hover {
  box-shadow: 0 8px 32px 0 rgba(44,62,80,0.18), 0 1.5px 8px 0 rgba(44,62,80,0.10);
  transform: scale(1.03);
  background: rgba(255,255,255,0.95);
}
.kategori-section--right .kategori-content {
  /* transform: none; */
}
.kategori-bg-title {
  position: static;
  font-size: 2rem;
  font-weight: 800;
  color: #555;
  opacity: 0.95;
  background: none;
  -webkit-background-clip: unset;
  -webkit-text-fill-color: unset;
  background-clip: unset;
  text-shadow: none;
  margin-top: 32px;
  margin-bottom: 10px;
  text-align: center;
  letter-spacing: 1px;
  user-select: none;
  pointer-events: none;
}
.kategori-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  min-height: 260px;
}
.kategori-img-wrapper {
  width: 260px;
  height: 260px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  margin: 20px auto 12px auto;
  position: relative;
  z-index: 2;
}
.kategori-img-overlap {
  width: 100%;
  height: 100%;
  object-fit: contain;
  position: relative;
  z-index: 2;
  margin-top: 0;
  transition: transform 0.3s cubic-bezier(.4,2,.6,1), box-shadow 0.3s cubic-bezier(.4,2,.6,1), border-radius 0.3s cubic-bezier(.4,2,.6,1), outline 0.3s cubic-bezier(.4,2,.6,1);
}
.kategori-content:hover .kategori-img-overlap {
  transform: scale(1.08) rotate(-2deg);
  box-shadow: 0 8px 32px 0 rgba(44,62,80,0.18), 0 1.5px 8px 0 rgba(44,62,80,0.10);
  border-radius: 50%;
  outline: 6px solid rgba(44,62,80,0.10);
}
.kategori-nama {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #222;
  text-align: center;
}
.kategori-desc {
  font-size: 1.15rem;
  color: #444;
  margin-top: 0;
  margin-bottom: 0;
  line-height: 1.7;
  text-align: center;
  max-width: 600px;
}
.bg-depresi {
  background: linear-gradient(135deg, #cfd5f7 0%, #e0c3fc 100%);
}
.bg-anxiety {
  background: linear-gradient(135deg, #f7f3c7 0%, #f7cac9 100%);
}
.bg-skizofrenia {
  background: linear-gradient(135deg, #e0e7ef 0%, #c9e4f7 100%);
}
.bg-bipolar {
  background: linear-gradient(135deg, #f7e3d5 0%, #f7d5e6 100%);
}
.bg-ocd {
  background: linear-gradient(135deg, #e6f7f3 0%, #c3f7e0 100%);
}
.bg-ptsd {
  background: linear-gradient(135deg, #f7d5e6 0%, #f7e3d5 100%);
}
.bg-eating {
  background: linear-gradient(135deg, #f7ecd5 0%, #f7d5d5 100%);
}
.bg-fobia {
  background: linear-gradient(135deg, #e7d5f7 0%, #d5e7f7 100%);
}
.kategori-wave-top-sharp {
  width: 100vw;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1;
  line-height: 0;
  pointer-events: none;
}
.kategori-section:nth-child(1) .kategori-content { border-color: #ec744a; }
.kategori-section:nth-child(2) .kategori-content { border-color: #a78bfa; }
.kategori-section:nth-child(3) .kategori-content { border-color: #4db6ac; }
.kategori-section:nth-child(4) .kategori-content { border-color: #fbbf24; }
.kategori-section:nth-child(5) .kategori-content { border-color: #ef4444; }
.kategori-section:nth-child(6) .kategori-content { border-color: #8b5cf6; }
.kategori-section:nth-child(7) .kategori-content { border-color: #f87171; }
.kategori-section:nth-child(8) .kategori-content { border-color: #81c784; }
@media (max-width: 900px) {
  .edukasi-container { padding: 24px 0 32px 0; }
  .kategori-section { padding: 32px 0 24px 0; min-height: 320px; }
  .kategori-bg-title { font-size: 2.5rem; }
  .kategori-img-wrapper { width: 140px; height: 140px; margin-top: 24px; }
  .kategori-img-overlap { width: 100%; height: 100%; }
  .kategori-nama { font-size: 1.2rem; }
  .kategori-desc { font-size: 1rem; }
}
/* Border color mapping by bgClass */
.bg-depresi .kategori-content { border-color: #cfd5f7; }
.bg-anxiety .kategori-content { border-color: #f7f3c7; }
.bg-skizofrenia .kategori-content { border-color: #c9e4f7; }
.bg-bipolar .kategori-content { border-color: #f7d5e6; }
.bg-ocd .kategori-content { border-color: #c3f7e0; }
.bg-ptsd .kategori-content { border-color: #f7d5e6; }
.bg-eating .kategori-content { border-color: #f7ecd5; }
.bg-fobia .kategori-content { border-color: #e7d5f7; }

/* Background color mapping by bgClass */
.bg-depresi .kategori-content { background: rgba(207,213,247,0.85); }
.bg-anxiety .kategori-content { background: rgba(247,243,199,0.85); }
.bg-skizofrenia .kategori-content { background: rgba(201,228,247,0.85); }
.bg-bipolar .kategori-content { background: rgba(247,213,230,0.85); }
.bg-ocd .kategori-content { background: rgba(195,247,224,0.85); }
.bg-ptsd .kategori-content { background: rgba(247,213,230,0.85); }
.bg-eating .kategori-content { background: rgba(247,236,213,0.85); }
.bg-fobia .kategori-content { background: rgba(231,213,247,0.85); }

</style>
