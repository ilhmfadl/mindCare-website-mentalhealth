<template>
  <footer class="footer">
    <div class="footer-main">
      <!-- Logo & Description -->
      <div class="footer-section logo-section">
        <div class="logo">
          <!-- Ganti SVG Logo dengan gambar -->
          <img src="/src/assets/LogoMindCare.png" alt="Logo" class="logo-img" />
        </div>
        <p class="desc">
          Lorem ipsum dolor sit amet consectetur. Convallis est urna.
        </p>
      </div>
      <!-- Services -->
      <div class="footer-section">
        <h4>Services</h4>
        <p>Mental Counseling</p>
      </div>
      <!-- Contact -->
      <div class="footer-section">
        <h4>Contact</h4>
        <div class="contact-item">
          <span class="icon">
            <!-- Phone SVG -->
            <svg width="18" height="18" fill="none" stroke="black" stroke-width="2"><path d="M2 2l14 14M16 2L2 16"/></svg>
          </span>
          <span>+62 xxx xxxx xxxx</span>
        </div>
        <div class="contact-item">
          <span class="icon">
            <!-- Email SVG -->
            <svg width="18" height="18" fill="none" stroke="black" stroke-width="2"><rect x="2" y="4" width="14" height="10" rx="2"/><path d="M2 4l7 6 7-6"/></svg>
          </span>
          <span>mindcare@domain.com</span>
        </div>
        <div class="contact-item">
          <span class="icon">
            <!-- Location SVG -->
            <svg width="18" height="18" fill="none" stroke="black" stroke-width="2"><circle cx="9" cy="7" r="3"/><path d="M9 10v5"/></svg>
          </span>
          <span>Pekanbaru, Riau</span>
        </div>
      </div>
      <!-- Links -->
      <div class="footer-section">
        <h4>Links</h4>
        <p>Privacy Policy</p>
        <p>Term Of Use</p>
      </div>
      <!-- Maps -->
      <div class="footer-section">
        <h4>Maps</h4>
        <div class="maps-placeholder">
          <!-- Checkerboard SVG -->
          <svg width="80" height="60" viewBox="0 0 80 60">
            <rect x="0" y="0" width="20" height="20" fill="black"/>
            <rect x="20" y="20" width="20" height="20" fill="black"/>
            <rect x="40" y="0" width="20" height="20" fill="black"/>
            <rect x="60" y="20" width="20" height="20" fill="black"/>
            <rect x="0" y="40" width="20" height="20" fill="black"/>
            <rect x="20" y="0" width="20" height="20" fill="black"/>
            <rect x="40" y="40" width="20" height="20" fill="black"/>
            <rect x="60" y="0" width="20" height="20" fill="black"/>
          </svg>
        </div>
      </div>
    </div>
    <hr class="footer-divider" />
    <div class="footer-bottom">
      <div class="socials">
        <i class="fab fa-instagram"></i>
        <i class="fab fa-facebook-f"></i>
        <i class="fab fa-youtube"></i>
        <i class="fab fa-twitter"></i>
        <i class="fab fa-linkedin-in"></i>
      </div>
      <div class="copyright">
        copyright 2023 @mindcare all right reserved
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: #faf7f3;
  padding: 40px 0 0 0;
  font-family: 'Poppins', sans-serif;
}
.footer-main {
  display: flex;
  justify-content: space-around;
  align-items: flex-start;
  flex-wrap: wrap;
  padding: 0 40px;
}
.footer-section {
  flex: 1 1 180px;
  margin: 0 20px;
  min-width: 160px;
}
.logo-section {
  max-width: 220px;
}
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  font-size: 20px;
  margin-bottom: 10px;
}
.brand {
  font-size: 20px;
  font-weight: 600;
}
.desc {
  font-size: 13px;
  color: #222;
  margin-top: 10px;
}
h4 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
}
.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  margin-bottom: 6px;
}
.icon {
  display: flex;
  align-items: center;
}
.maps-placeholder {
  margin-top: 10px;
}
.footer-divider {
  margin: 30px 0 20px 0;
  border: none;
  border-top: 2px solid #222;
  width: 90%;
  margin-left: auto;
  margin-right: auto;
}
.footer-bottom {
  text-align: center;
  padding-bottom: 20px;
}
.socials {
  margin-bottom: 10px;
}
.socials i {
  font-size: 16px;
  margin: 0 7px;
  color: #222;
  cursor: pointer;
}
.copyright {
  font-size: 13px;
  color: #222;
}
.logo-img {
  width: 10rem;
  object-fit: contain;
  /* Atur sesuai kebutuhan */
}
@media (max-width: 900px) {
  .footer-main {
    flex-direction: column;
    align-items: flex-start;
    gap: 30px;
  }
  .footer-section {
    margin: 0 0 20px 0;
  }
}
</style>