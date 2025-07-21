<template>
  <header class="header">
    <div class="logo">
      <img src="/src/assets/LogoMindCare.png" alt="Logo" class="logo-img" />
    </div>
    <nav class="nav">
      <ul>
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/tes-diri">Tes Diri</router-link></li>
        <li><router-link to="/pojok-edukasi">Pojok Edukasi</router-link></li>
        <li><router-link to="/forum-diskusi">Forum Diskusi</router-link></li>
        <li><router-link to="/profile">Profile</router-link></li>
      </ul>
    </nav>
    <button class="btn-contact" type="button">Contact Us</button>
  </header>
</template>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 3vw;
  border-bottom: 1px solid #E5E7EB;
  flex-wrap: wrap;
  width: 100%;
  box-sizing: border-box;
  background: transparent;
  min-width: 0;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 0;
}

.logo-img {
  width: 10rem;
  object-fit: contain;
}

.logo-icon {
  width: 2rem;
  height: 2rem;
  stroke: #4B5563;
  stroke-width: 1.5;
}

.logo-text {
  font-weight: 700;
  font-size: 1.25rem;
  color: #111827;
  user-select: text;
  white-space: nowrap;
}

.nav ul {
  list-style: none;
  display: flex;
  gap: 2rem;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
}

.nav ul li a {
  color: #374151;
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.nav ul li a:hover {
  color: #EC744A;
}

.nav ul li .router-link-active {
  font-weight: bold;
  color: #EC744A;
}

.btn-contact {
  padding: 0.6rem 1.5rem;
  background-color: #EC744A;
  color: white;
  font-weight: 600;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
  white-space: nowrap;
  user-select: none;
  font-size: 1rem;
}

@media (max-width: 480px) {
  .header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem 2vw;
  }
  .nav ul {
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>