<template>
  <div class="auth2-bg">
    <div class="auth2-container">
      <div class="auth2-left">
        <h2>Daftar</h2>
        <p class="auth2-desc">Buat akun baru untuk mengakses semua fitur MindCare.</p>
        <form @submit.prevent="handleRegister">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="username" required placeholder="Username" />
          </div>
          <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" v-model="name" required placeholder="Nama Lengkap" />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="email" required placeholder="Email" />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="password" required placeholder="Password" />
          </div>
          <div class="form-group">
            <label for="confirmPassword">Konfirmasi Password</label>
            <input type="password" id="confirmPassword" v-model="confirmPassword" required placeholder="Konfirmasi Password" />
          </div>
          <div v-if="error" class="error">{{ error }}</div>
          <button type="submit">Sign up</button>
        </form>
        <p class="auth2-link">Sudah punya akun? <router-link to="/login">Masuk</router-link></p>
      </div>
      <div class="auth2-right">
        <div class="auth2-img-wrapper">
          <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="Ilustrasi" class="auth2-illustration" />
          <div class="auth2-img-overlay"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: 'Register',
  data() {
    return {
      username: '',
      name: '',
      email: '',
      password: '',
      confirmPassword: '',
      error: ''
    }
  },
  methods: {
    async handleRegister() {
      if (this.password !== this.confirmPassword) {
        this.error = 'Password dan konfirmasi password tidak sama.';
        return;
      }
      try {
        const formData = new FormData();
        formData.append('username', this.username);
        formData.append('email', this.email);
        formData.append('password', this.password);
        formData.append('fullName', this.name);

        const response = await axios.post('https://mindcareindependent.com/api/register.php', formData);
        if (response.data.success) {
          this.$router.push('/login');
        } else {
          this.error = response.data.message;
        }
      } catch (err) {
        this.error = 'Terjadi kesalahan koneksi ke server.';
      }
    }
  }
}
</script>

<style scoped>
.auth2-bg {
  min-height: 100vh;
  width: 100vw;
  background: var(--bg-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
}
.auth2-container {
  display: flex;
  width: 1100px;
  min-height: 540px;
  background: none;
  border-radius: 18px;
  box-shadow: none;
  overflow: hidden;
}
.auth2-left {
  flex: 1.1;
  background: none;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 56px 80px 48px 80px;
}
h2 {
  font-size: 2.1rem;
  font-weight: 800;
  color: #4B4B9F;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}
.auth2-desc {
  color: var(--text-muted);
  font-size: 1.08rem;
  margin-bottom: 32px;
}
.form-group {
  margin-bottom: 22px;
}
label {
  display: block;
  margin-bottom: 7px;
  color: #4B4B9F;
  font-weight: 600;
  font-size: 1.01rem;
}
input {
  width: 100%;
  padding: 13px 14px;
  border: 1.5px solid #cfd5f7;
  border-radius: 8px;
  font-size: 1.09rem;
  background: var(--input-bg);
  color: var(--text-primary);
  transition: border 0.2s, box-shadow 0.2s;
  outline: none;
}
input:focus {
  border: 1.5px solid #4B4B9F;
  box-shadow: 0 2px 8px rgba(75,75,159,0.08);
  background: var(--card-bg);
}
button {
  width: 100%;
  padding: 14px;
  background: linear-gradient(90deg, #4B4B9F 0%, var(--button-primary) 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.13rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
  box-shadow: 0 2px 12px rgba(75,75,159,0.08);
  letter-spacing: 0.5px;
}
button:hover {
  background: linear-gradient(90deg, #37377a 0%, var(--button-hover) 100%);
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 6px 18px rgba(75,75,159,0.13);
}
.error {
  color: var(--error-color);
  margin-bottom: 10px;
  text-align: center;
  font-size: 1.01rem;
}
.auth2-link {
  text-align: left;
  margin-top: 24px;
  color: #4B4B9F;
  font-size: 1.01rem;
}
.auth2-link a, .auth2-link .router-link {
  color: var(--button-primary);
  text-decoration: underline;
  font-weight: 600;
  transition: color 0.18s;
}
.auth2-link a:hover, .auth2-link .router-link:hover {
  color: var(--button-hover);
}
.auth2-right {
  flex: 1.2;
  background: none;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 0;
}
.auth2-img-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 340px;
  min-width: 260px;
  max-height: 540px;
  display: flex;
}
.auth2-illustration {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 18px;
  z-index: 1;
}
.auth2-img-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  border-radius: 18px;
  z-index: 2;
  background: linear-gradient(120deg, rgba(75,75,159,0.38) 0%, rgba(236,116,74,0.28) 100%);
}
@media (max-width: 1200px) {
  .auth2-container {
    width: 98vw;
  }
  .auth2-left {
    padding: 40px 6vw 32px 6vw;
  }
}
@media (max-width: 900px) {
  .auth2-container {
    flex-direction: column;
    width: 98vw;
    min-height: unset;
    box-shadow: none;
  }
  .auth2-right {
    order: 1;
    min-height: 180px;
    max-height: 220px;
    padding: 0;
  }
  .auth2-img-wrapper {
    min-height: 180px;
    max-height: 220px;
  }
  .auth2-illustration, .auth2-img-overlay {
    min-height: 180px;
    max-height: 220px;
    border-radius: 0 0 18px 18px;
  }
  .auth2-left {
    order: 2;
    padding: 32px 8vw 32px 8vw;
  }
}
@media (max-width: 600px) {
  .auth2-left {
    padding: 18px 4vw 18px 4vw;
  }
  h2 {
    font-size: 1.3rem;
  }
  button {
    font-size: 1.05rem;
    padding: 12px;
  }
  label, input, p {
    font-size: 1rem;
  }
  .auth2-img-wrapper {
    min-height: 120px;
    max-height: 140px;
  }
  .auth2-illustration, .auth2-img-overlay {
    min-height: 120px;
    max-height: 140px;
    border-radius: 0 0 12px 12px;
  }
}
</style> 