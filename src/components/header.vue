<template>
  <header class="main-header">
    <div class="header-container">
      <div class="logo">
        <img src="/src/assets/LogoMindCare.png" alt="Logo" class="logo-img" />
      </div>
      <nav class="main-nav">
        <router-link to="/">Home</router-link>
        <router-link to="/tes-diri">Tes Diri</router-link>
        <router-link to="/edukasi">Pojok Edukasi</router-link>
        <router-link to="/forum">Forum Diskusi</router-link>
        <router-link to="/profile">Profile</router-link>
      </nav>
      <button class="contact-button">Contact Us</button>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Header'
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
  padding: 1rem 3rem;
  color: #333; /* Text color for light background */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  display: flex;
  justify-content: center;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 1200px;
}

.logo-img {
  height: 40px; /* Adjust as needed */
  width: auto;
}

.main-nav {
  display: flex;
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