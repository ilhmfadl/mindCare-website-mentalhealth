<template>
  <header class="main-header">
    <div class="header-container">
      <div class="logo">
        <svg class="logo-icon" width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4C13.1046 4 14 4.89543 14 6V8C14 9.10457 13.1046 10 12 10C10.8954 10 10 9.10457 10 8V6C10 4.89543 10.8954 4 12 4ZM8 10C9.10457 10 10 10.8954 10 12C10 13.1046 9.10457 14 8 14C6.89543 14 6 13.1046 6 12C6 10.8954 6.89543 10 8 10ZM16 10C17.1046 10 18 10.8954 18 12C18 13.1046 17.1046 14 16 14C14.8954 14 14 13.1046 14 12C14 10.8954 14.8954 10 16 10ZM12 14C13.1046 14 14 14.8954 14 16V18C14 19.1046 13.1046 20 12 20C10.8954 20 10 19.1046 10 18V16C10 14.8954 10.8954 14 12 14Z" fill="white"/>
        </svg>
        <span class="logo-text">MindCare</span>
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
  name: 'Navbar'
}
</script>

<style scoped>
.main-header {
  background-color: #6A4C9B;
  padding: 1rem 3rem;
  color: white;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1280px;
  margin: 0 auto;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.5rem;
  font-weight: 700;
}

.main-nav {
  display: flex;
  gap: 2.5rem;
}

.main-nav a {
  color: #e0e0e0;
  text-decoration: none;
  padding-bottom: 5px;
  transition: color 0.3s;
}

.main-nav a:hover {
  color: white;
}

.main-nav a.router-link-exact-active {
  color: white;
  font-weight: 600;
  border-bottom: 2px solid white;
}

.contact-button {
  background-color: #8A6FB9;
  color: white;
  border: none;
  padding: 0.8rem 1.6rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.contact-button:hover {
  background-color: #987cc4;
}
</style> 