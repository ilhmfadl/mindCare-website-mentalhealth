<template>
  <header class="main-header">
    <div class="header-container">
      <div class="logo">
        <img src="/src/assets/LogoMindCare.png" alt="Logo" class="logo-img" />
      </div>
      <nav class="main-nav">
        <router-link to="/">Home</router-link>
        <router-link to="/tes-diri">Tes Diri</router-link>
        <router-link
          v-if="testState.testCompleted"
          :to="{ name: testState.resultType, params: { score: testState.score } }"
        >
          Hasil Tes
        </router-link>
        <router-link to="/edukasi">Pojok Edukasi</router-link>
        <router-link to="/forum">Forum Diskusi</router-link>
        <router-link to="/profile">Profile</router-link>
      </nav>
      <div class="header-right">
        <button class="contact-button">Contact Us</button>
      </div>
    </div>
  </header>
</template>

<script>
import { testState } from '../store/testState';

export default {
  name: 'Header',
  setup() {
    return {
      testState
    };
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
  
  background-color: #faf7f3; /* Solid background */
  padding: 1rem 0;
  color: #333; /* Text color for light background */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  display: flex;
  justify-content: center;
}

/* home-container agar sejajar dengan konten utama */
.home-container {
  max-width: 1200px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  width: 100%;
  box-sizing: border-box;
}
.header-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  gap: 32px;
}
@media (max-width: 900px) {
  .header-container {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    padding: 0 1rem;
  }
  .main-header {
    padding: 0.5rem 0.5rem;
  }
  .main-nav {
    justify-content: center;
    gap: 1.2rem;
    flex-wrap: wrap;
  }
  .contact-button {
    width: 100%;
    margin-top: 8px;
  }
  .logo {
    display: flex;
    justify-content: center;
    margin-bottom: 4px;
  }
}

.logo-img {
  height: 40px; /* Adjust as needed */
  width: auto;
}

.main-nav {
  flex-grow: 1; /* Allows the nav to take up available space */
  display: flex;
  justify-content: center; /* Centers the links within the nav element */
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

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-toggle {
  cursor: pointer;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  background-color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-radius: 8px;
  padding: 10px 0;
  min-width: 180px;
  z-index: 1001;
  display: flex;
  flex-direction: column;
}

.dropdown-menu a {
  color: #333;
  padding: 10px 20px;
  text-decoration: none;
  display: block;
  text-align: left;
  border-bottom: none;
}

.dropdown-menu a:hover {
  background-color: #f5f5f5;
}

.contact-button {
  background-color: #6A4C9B;
  color: white;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.contact-button:hover {
  background-color: #563d7c;
}
</style>