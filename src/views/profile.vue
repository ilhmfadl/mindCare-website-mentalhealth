<template>
  <ConfirmationModal
    :visible="showLogoutConfirm"
    title="Konfirmasi Logout"
    message="Apakah Anda yakin ingin logout?"
    @confirm="confirmLogout"
    @cancel="showLogoutConfirm = false"
  />
  <ConfirmationModal
    :visible="showSaveConfirm"
    title="Konfirmasi Simpan Profil"
    message="Apakah Anda yakin ingin menyimpan perubahan profil?"
    @confirm="confirmSave"
    @cancel="showSaveConfirm = false"
  />
  <div class="profile-bg">
    <div class="profile-container">
      <div class="profile-header">
        <div class="avatar-wrapper" @click="onAvatarClick">
          <img class="profile-avatar" :src="form.avatarPreview || form.avatar" alt="Profile Avatar" />
          <div class="avatar-overlay" v-if="isEditing">
            <span>Ubah Foto</span>
          </div>
          <input ref="avatarInput" type="file" accept="image/*" @change="onAvatarChange" style="display:none" />
        </div>
        <h2>Profile</h2>
      </div>
      <form class="profile-form" @submit.prevent="onSave">
        <div class="form-group">
          <label for="username">Username</label>
          <input id="username" type="text" v-model="form.username" :disabled="!isEditing" />
        </div>
        <div class="form-group">
          <label for="fullName">Nama Lengkap</label>
          <input id="fullName" type="text" v-model="form.fullName" :disabled="!isEditing" />
        </div>
        <div class="form-group with-icon">
          <label for="email">Email</label>
          <div class="input-icon-wrapper">
            <input id="email" type="email" v-model="form.email" :disabled="!isEditing" />
          </div>
        </div>
        <div class="form-group">
          <label for="address">Alamat</label>
          <input id="address" type="text" v-model="form.address" :disabled="!isEditing" />
        </div>
        <div class="btn-group-profile">
          <button v-if="!isEditing" type="button" class="edit-btn" @click="onEdit">Edit</button>
          <button v-if="isEditing" type="submit" class="save-btn">Save</button>
          <button v-if="isEditing" type="button" class="cancel-btn" @click="onCancel">Cancel</button>
          <button v-if="!isEditing" type="button" class="logout-btn" @click="showLogoutConfirm = true">Logout</button>
        </div>
      </form>
      <div class="password-section">
        <button type="button" class="change-password-btn" @click="goToChangePassword">Ubah Kata Sandi</button>
      </div>
    </div>
  </div>
</template>

<script>
import ConfirmationModal from '../components/ConfirmationModal.vue';

export default {
  name: 'ProfileView',
  components: { ConfirmationModal },
  data() {
    return {
      isEditing: false,
      showLogoutConfirm: false,
      showSaveConfirm: false,
      original: {
        fullName: 'WINDAH BATUBARA',
        email: 'windah@gmail.com',
        address: 'Harapan Raya',
        job: 'Youtuber',
        avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
        avatarPreview: null,
      },
      form: {
        fullName: 'WINDAH BATUBARA',
        email: 'windah@gmail.com',
        address: 'Harapan Raya',
        job: 'Youtuber',
        avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
        avatarPreview: null,
      }
    }
  },
  methods: {
    onEdit() {
      this.isEditing = true;
    },
    onCancel() {
      this.form = { ...this.original, avatarPreview: null };
      this.isEditing = false;
    },
    onSave() {
      this.showSaveConfirm = true;
    },
    confirmSave() {
      this.original = { ...this.form };
      if (this.form.avatarPreview) {
        this.original.avatar = this.form.avatarPreview;
        this.form.avatar = this.form.avatarPreview;
        this.form.avatarPreview = null;
      }
      this.isEditing = false;
      this.showSaveConfirm = false;
    },
    onLogout() {
      // Tambahkan logika logout sesungguhnya di sini
      this.showLogoutConfirm = false;
    },
    confirmLogout() {
      this.onLogout();
    },
    goToChangePassword() {
      this.$router.push({ name: 'ChangePassword' });
    },
    onAvatarClick() {
      if (this.isEditing) {
        this.$refs.avatarInput.click();
      }
    },
    onAvatarChange(e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          this.form.avatarPreview = ev.target.result;
        };
        reader.readAsDataURL(file);
      }
    }
  }
}
</script>

<style scoped>
.profile-bg {
  min-height: 100vh;
  width: 100vw;
  background: #fcf8f3;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding-top: 0;
}
.profile-container {
  max-width: 1000px !important;
  width: 100%;
  margin: 100px auto 48px auto;
  background: #fcf8f3;
  border-radius: 22px;
  padding: 64px 80px 48px 80px !important;
  box-shadow: 0 8px 32px rgba(106,76,155,0.10), 0 1.5px 6px rgba(0,0,0,0.06);
  border: 1.5px solid #f3f0fa;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: box-shadow 0.2s;
}
.profile-container:hover {
  box-shadow: 0 12px 40px rgba(106,76,155,0.16), 0 2px 8px rgba(0,0,0,0.09);
}
.profile-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  margin-bottom: 28px;
  width: 100%;
}
.profile-header h2 {
  font-size: 2.1rem;
  font-weight: 800;
  margin: 0;
  color: #6A4C9B;
  letter-spacing: 1px;
}
.profile-avatar {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #fff;
  box-shadow: 0 2px 12px rgba(106,76,155,0.13);
  margin-bottom: 2px;
  background: #f3f0fa;
}
.profile-form {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 7px;
}
label {
  font-size: 1.04rem;
  font-weight: 600;
  margin-bottom: 2px;
  color: #6A4C9B;
}
input[type="text"],
input[type="email"] {
  padding: 12px 14px;
  border: 1.5px solid #d1c4e9;
  border-radius: 8px;
  font-size: 1.04rem;
  background: #faf8ff;
  color: #222;
  outline: none;
  transition: border 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 2px rgba(106,76,155,0.04);
}
input[type="text"]:focus,
input[type="email"]:focus {
  border-color: #6A4C9B;
  box-shadow: 0 0 0 2px #e9e3f7;
}
input[disabled] {
  background: #f3f0fa;
  color: #888;
}
.with-icon .input-icon-wrapper {
  display: flex;
  align-items: center;
  position: relative;
}
.input-icon-wrapper input {
  flex: 1;
}
.btn-group-profile {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 18px;
  justify-content: center;
}
.edit-btn, .save-btn, .cancel-btn, .logout-btn {
  background: #e9e3f7;
  color: #6A4C9B;
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
.save-btn {
  background: #d1c4e9;
  color: #563d7c;
}
.cancel-btn {
  background: #f3f0fa;
  color: #888;
}
.logout-btn {
  background: #f8bbd0;
  color: #a31545;
}
.edit-btn:hover {
  background: #d1c4e9;
  color: #563d7c;
  box-shadow: 0 2px 8px rgba(106,76,155,0.10);
}
.save-btn:hover {
  background: #b39ddb;
  color: #4b2e83;
  box-shadow: 0 2px 8px rgba(106,76,155,0.13);
}
.cancel-btn:hover {
  background: #e9e3f7;
  color: #6A4C9B;
}
.logout-btn:hover {
  background: #f48fb1;
  color: #fff;
}
.password-section {
  margin-top: 32px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  width: 100%;
}
.change-password-btn {
  background: #b3e5fc;
  color: #1565c0;
  border: none;
  border-radius: 8px;
  padding: 10px 32px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 4px;
  transition: background 0.2s, box-shadow 0.2s, color 0.2s;
  box-shadow: 0 1px 4px rgba(106,76,155,0.10);
  letter-spacing: 0.5px;
}
.change-password-btn:hover {
  background: #4fc3f7;
  color: #fff;
  box-shadow: 0 2px 8px rgba(106,76,155,0.13);
}
.avatar-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.avatar-wrapper:hover .avatar-overlay {
  opacity: 1;
}
.avatar-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(106,76,155,0.18);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.05rem;
  border-radius: 50%;
  opacity: 0;
  transition: opacity 0.2s;
  pointer-events: none;
  z-index: 2;
}
@media (max-width: 1100px) {
  .profile-container {
    max-width: 98vw !important;
    padding: 24px 4vw 18px 4vw !important;
  }
  .profile-header h2 {
    font-size: 1.3rem;
  }
  .profile-avatar {
    width: 70px;
    height: 70px;
  }
  .btn-group-profile {
    flex-direction: column;
    gap: 10px;
    align-items: stretch;
  }
  .password-section {
    align-items: stretch;
  }
}
@media (max-width: 700px) {
  .profile-bg {
    padding-top: 0;
  }
  .profile-container {
    max-width: 100vw !important;
    padding: 10px 1vw 8px 1vw !important;
    margin: 60px auto 18px auto;
    border-radius: 12px;
  }
  .profile-header {
    gap: 6px;
    margin-bottom: 12px;
  }
  .profile-header h2 {
    font-size: 1.05rem;
  }
  .profile-avatar {
    width: 44px;
    height: 44px;
    border-width: 2px;
    margin-bottom: 1px;
  }
  .profile-form {
    gap: 10px;
    margin-top: 4px;
  }
  .form-group {
    gap: 3px;
  }
  label {
    font-size: 0.85rem;
    margin-bottom: 1px;
  }
  input[type="text"],
  input[type="email"] {
    padding: 7px 8px;
    border-radius: 5px;
    font-size: 0.85rem;
  }
  .btn-group-profile {
    gap: 6px;
    margin-top: 8px;
  }
  .edit-btn, .save-btn, .cancel-btn, .logout-btn {
    font-size: 0.85rem;
    padding: 7px 12px;
    border-radius: 5px;
  }
  .password-section {
    margin-top: 14px;
    gap: 6px;
  }
  .change-password-btn {
    font-size: 0.85rem;
    padding: 7px 12px;
    border-radius: 5px;
  }
  .avatar-overlay {
    font-size: 0.8rem;
  }
}
</style>
