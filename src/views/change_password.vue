<template>
  <ConfirmationModal
    :visible="showSaveConfirm"
    title="Konfirmasi Ubah Kata Sandi"
    message="Apakah Anda yakin ingin mengubah kata sandi?"
    @confirm="confirmSave"
    @cancel="showSaveConfirm = false"
  />
  <div class="change-password-bg">
    <div class="change-password-container">
      <h2>Ubah Kata Sandi</h2>
      <form class="change-password-form" @submit.prevent="onSubmit">
        <div class="form-group">
          <label for="oldPassword">Kata Sandi Lama</label>
          <input id="oldPassword" type="password" v-model="oldPassword" required />
        </div>
        <div class="form-group">
          <label for="newPassword">Kata Sandi Baru</label>
          <input id="newPassword" type="password" v-model="newPassword" required />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Konfirmasi Kata Sandi Baru</label>
          <input id="confirmPassword" type="password" v-model="confirmPassword" required />
        </div>
        <div v-if="error" class="error-msg">{{ error }}</div>
        <div v-if="success" class="success-msg">Kata sandi berhasil diubah!</div>
        <div class="btn-group">
          <button type="submit" class="save-btn">Simpan</button>
          <button type="button" class="cancel-btn" @click="onCancel">Batal</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ConfirmationModal from '../components/ConfirmationModal.vue';
export default {
  name: 'ChangePassword',
  components: { ConfirmationModal },
  data() {
    return {
      oldPassword: '',
      newPassword: '',
      confirmPassword: '',
      error: '',
      success: false,
      showSaveConfirm: false
    }
  },
  methods: {
    onSubmit() {
      this.error = '';
      this.success = false;
      if (!this.oldPassword || !this.newPassword || !this.confirmPassword) {
        this.error = 'Semua field wajib diisi.';
        return;
      }
      if (this.newPassword.length < 6) {
        this.error = 'Kata sandi baru minimal 6 karakter.';
        return;
      }
      if (this.newPassword !== this.confirmPassword) {
        this.error = 'Konfirmasi kata sandi tidak cocok.';
        return;
      }
      this.showSaveConfirm = true;
    },
    confirmSave() {
      this.success = true;
      this.oldPassword = '';
      this.newPassword = '';
      this.confirmPassword = '';
      this.showSaveConfirm = false;
      setTimeout(() => {
        this.$router.push({ name: 'Profile' });
      }, 1500);
    },
    onCancel() {
      this.$router.back();
    }
  }
}
</script>

<style scoped>
.change-password-bg {
  min-height: 100vh;
  width: 100vw;
  background: #fcf8f3;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding-top: 0;
}
.change-password-container {
  max-width: 1100px;
  margin: 100px auto 48px auto;
  background: #fcf8f3;
  border-radius: 22px;
  padding: 80px 120px 60px 120px;
  box-shadow: 0 8px 32px rgba(106,76,155,0.10), 0 1.5px 6px rgba(0,0,0,0.06);
  border: 1.5px solid #f3f0fa;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.change-password-container h2 {
  color: #6A4C9B;
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 24px;
  letter-spacing: 1px;
}
.change-password-form {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.form-group {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.form-group label {
  width: 100%;
  font-size: 1.04rem;
  font-weight: 600;
  color: #6A4C9B;
}
.form-group input[type="password"] {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid #d1c4e9;
  border-radius: 8px;
  font-size: 1.04rem;
  background: #faf8ff;
  color: #222;
  outline: none;
  transition: border 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 2px rgba(106,76,155,0.04);
  box-sizing: border-box;
}
.form-group input[type="password"]:focus {
  border-color: #6A4C9B;
  box-shadow: 0 0 0 2px #e9e3f7;
}
.btn-group {
  display: flex;
  gap: 14px;
  margin-top: 10px;
  justify-content: center;
}
.save-btn {
  background: #d1c4e9;
  color: #563d7c;
  font-weight: 700;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  padding: 11px 30px;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, color 0.2s;
  box-shadow: 0 1px 4px rgba(106,76,155,0.06);
  letter-spacing: 0.5px;
}
.save-btn:hover {
  background: #b39ddb;
  color: #4b2e83;
  box-shadow: 0 2px 8px rgba(106,76,155,0.13);
}
.cancel-btn {
  background: #f3f0fa;
  color: #888;
  font-weight: 700;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  padding: 11px 30px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.cancel-btn:hover {
  background: #e9e3f7;
  color: #6A4C9B;
}
.error-msg {
  color: #e21313;
  background: #fdecea;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 0.98rem;
  margin-bottom: -8px;
  text-align: center;
}
.success-msg {
  color: #16a34a;
  background: #eafaf1;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 0.98rem;
  margin-bottom: -8px;
  text-align: center;
}
</style> 