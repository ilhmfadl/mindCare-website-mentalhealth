<template>
  <header class="main-header">
    <div class="header-container">
      <div class="logo">
        <router-link to="/">
          <img src="/src/assets/LogoMindCare.png" alt="Logo" class="logo-img" />
        </router-link>
      </div>
      <nav class="main-nav">
        <router-link to="/">Home</router-link>
        <router-link to="/tes-diri">Tes Diri</router-link>
        <router-link
          v-if="testState.hasilTesTerakhir"
          :to="getHasilTesRoute"
        >
          Hasil Tes
        </router-link>
        <router-link to="/edukasi">Pojok Edukasi</router-link>
        <router-link to="/forum">Forum Diskusi</router-link>
        <router-link v-if="isLoggedIn" to="/profile">Profile</router-link>
      </nav>
    </div>
  </header>
</template>

<script>
import { testState } from '../store/testState';
import { isLoggedIn } from '../store/authState';

export default {
  name: 'Header',
  setup() {
    return {
      testState,
      isLoggedIn
    };
  },
  computed: {
    getHasilTesRoute() {
      const hasil = (testState.hasilTesTerakhir?.hasil || '').toLowerCase();
      if (hasil.includes('neurosis')) {
        return { name: 'ResultIsNeurosis' };
      } else if (hasil.includes('psikotik') || hasil.includes('zat')) {
        return { name: 'ResultIsPsychotic' };
      } else if (hasil.includes('ptsd')) {
        return { name: 'ResultIsPTSD' };
      } else if (hasil.includes('normal')) {
        return { name: 'ResultIsNormal' };
      } else {
        return { name: 'TesDiri' };
      }
    }
  }
}
</script>

<style scoped>
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  background-color: #faf7f3;
  padding: 1rem 0;
  color: #333;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: center;
}
.header-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  max-width: 1200px;
}
.logo-img {
  height: 40px;
  width: auto;
}
.main-nav {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  gap: 2.5rem;
}
.main-nav a {
  color: #555;
  text-decoration: none;
  font-weight: 500;
  padding-bottom: 5px;
  transition: color 0.3s, border-color 0.3s;
  border-bottom: 2px solid transparent;
}
.main-nav a:hover {
  color: #6A4C9B;
}
.main-nav a.router-link-exact-active {
  color: #6A4C9B;
  font-weight: 600;
  border-bottom-color: #6A4C9B;
}
@media (max-width: 900px) {
  .main-header {
    padding: 8px 0 4px 0;
  }
  .header-container {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 6px;
    padding: 0 2px;
  }
  .logo-img {
    height: 18px;
    margin-bottom: 2px;
  }
  .main-nav {
    gap: 0.3rem;
    font-size: 0.78rem;
    flex-wrap: wrap;
    justify-content: center;
    padding: 0 1px;
    overflow-x: auto;
    white-space: nowrap;
    max-width: 100vw;
  }
  .main-nav a {
    font-size: 0.78rem;
    padding-bottom: 1px;
    padding-left: 2px;
    padding-right: 2px;
    margin-bottom: 1px;
    display: inline-block;
  }
}
</style>