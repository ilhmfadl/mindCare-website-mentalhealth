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
          Kenali, Pahami, dan Rawat kesehatan mental Anda.
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
          <span>mindcareindonesia@gmail.com</span>
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
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d255347.4931110609!2d101.3211187425133!3d0.5116560965391942!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5ac43c441b8c1%3A0x92f63f73a5a8a655!2sPekanbaru%2C%20Pekanbaru%20City%2C%20Riau!5e0!3m2!1sen!2sid!4v1628516584282!5m2!1sen!2sid"
            width="100%"
            height="150"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
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
  background: var(--bg-tertiary);
  padding: 50px 0 20px 0; /* Increased top padding */
  font-family: 'Poppins', sans-serif;
  border-top: 1px solid var(--border-color);
}
.footer-main {
  display: flex;
  justify-content: space-between; /* Changed from space-around */
  align-items: flex-start;
  flex-wrap: wrap;
  max-width: 1200px; /* Added max-width for large screens */
  margin: 0 auto; /* Center the container */
  padding: 0 40px;
}
.footer-section {
  flex: 1 1 200px; /* Adjusted flex-basis */
  margin: 0 15px 30px; /* Consistent margin */
  min-width: 180px;
}
.logo-section {
  max-width: 240px;
}
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  font-size: 20px;
  margin-bottom: 15px; /* Increased margin */
}
.desc {
  font-size: 14px; /* Slightly larger font */
  color: var(--text-secondary); /* Softer color */
  line-height: 1.6;
}
h4 {
  font-size: 16px; /* Slightly smaller */
  font-weight: 700; /* Bolder */
  margin-bottom: 20px; /* More space */
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-primary);
}
.footer-section p {
    line-height: 1.7;
    margin-bottom: 8px;
    font-size: 14px;
    color: var(--text-secondary);
}
.contact-item {
  display: flex;
  align-items: center;
  gap: 10px; /* Increased gap */
  font-size: 14px;
  margin-bottom: 12px; /* Increased margin */
  color: var(--text-secondary);
}
.icon {
  display: flex;
  align-items: center;
  color: var(--text-primary);
}
.maps-placeholder {
  margin-top: 10px;
  background: var(--bg-secondary);
  border-radius: 8px;
  overflow: hidden;
  width: 100%;
  max-width: 220px;
  height: 150px;
}
.footer-divider {
  margin: 30px auto; /* Centered */
  border: none;
  border-top: 1px solid var(--border-color); /* Softer color */
  width: 90%;
}
.footer-bottom {
  text-align: center;
  padding-bottom: 20px;
}
.socials {
  margin-bottom: 15px; /* Increased margin */
}
.socials i {
  font-size: 18px; /* Slightly larger */
  margin: 0 10px;
  color: var(--text-primary);
  cursor: pointer;
  transition: color 0.3s, transform 0.3s;
}
.socials i:hover {
    color: var(--accent-purple); /* Example brand color */
    transform: scale(1.1);
}
.copyright {
  font-size: 12px; /* Slightly smaller */
  color: var(--text-muted);
  text-transform: capitalize;
}
.logo-img {
  width: 10rem;
  object-fit: contain;
}
@media (max-width: 900px) {
  .footer {
    padding: 16px 0 8px 0;
  }
  .footer-main {
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: center;
    text-align: left;
    gap: 8px;
    padding: 0 2px;
  }
  .footer-section {
    margin: 0 2px 0 2px;
    min-width: 120px;
    max-width: 160px;
    flex: 1 1 120px;
    font-size: 0.85rem;
    background: none;
    border-radius: 0;
    padding: 6px 2px 6px 2px;
    box-shadow: none;
  }
  .logo-img {
    width: 3.5rem;
    max-width: 100%;
    margin-bottom: 4px;
  }
  .footer-bottom {
    padding-bottom: 6px;
    font-size: 0.85rem;
  }
  .socials i {
    font-size: 1rem;
    margin: 0 4px;
  }
  .maps-placeholder {
    max-width: 100vw;
    height: 70px;
    border-radius: 6px;
  }
  .desc, .footer-section p, .contact-item {
    font-size: 0.85rem;
  }
  h4 {
    font-size: 0.95rem;
    margin-bottom: 6px;
  }
}
</style>