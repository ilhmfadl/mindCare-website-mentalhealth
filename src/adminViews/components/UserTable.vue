<template>
  <div class="user-table-outer">
    <div class="user-table-container">
      <div class="table-title-row">
        <div class="table-title">Daftar Pengguna</div>
        <div class="table-actions">
          <input
            v-model="search"
            class="user-search-input"
            type="text"
            placeholder="Cari nama, email, atau ID..."
          />
          <button class="btn-add" type="button" @click="addFromList">
            <span class="btn-add-icon">✚</span> Tambah User
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Memuat data pengguna...</p>
      </div>

      <!-- Error State -->
      <div v-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchUsers" class="btn-retry">Coba Lagi</button>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="success-message">
        <p>{{ successMessage }}</p>
      </div>

      <!-- User List -->
      <div v-if="!loading && !error" class="table-wrapper">
        <table class="user-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>ID</th>
              <th>Email</th>
              <th>Role</th>
              <th>Date added</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="user-info">
                <div class="avatar-container">
                  <img v-if="user.photo" :src="user.photo" class="avatar" alt="avatar" />
                  <div v-else class="avatar-placeholder">
                    {{ getUserInitials(user.fullName || user.username) }}
                  </div>
                </div>
                <div>
                  <div class="user-name">{{ user.fullName || user.username }}</div>
                  <div class="user-role">{{ user.role || 'User' }}</div>
                </div>
              </td>
              <td>{{ user.id }}</td>
              <td>{{ user.email }}</td>
              <td>
                <span class="role-badge" :class="'role-' + (user.role || 'user').toLowerCase()">
                  {{ user.role || 'User' }}
                </span>
              </td>
              <td>
                <div>{{ user.date }}</div>
                <div class="date-time">{{ user.time }}</div>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-edit" title="Edit" @click="editUser(user)">
                    <span>✏️</span>
                  </button>
                  <button class="btn-delete" title="Hapus" @click="confirmDelete(user)">
                    <span>🗑️</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit Form -->
      <div v-if="mode === 'add' || mode === 'edit'" class="add-user-card-outer">
        <div class="add-user-card">
          <h2 class="form-title">{{ mode === 'add' ? 'Tambah User Baru' : 'Edit User' }}</h2>
          <form @submit.prevent="mode === 'add' ? submitForm() : submitEdit()">
            <div class="form-row">
              <div class="form-group">
                <label for="username">Username</label>
                <input 
                  id="username" 
                  v-model="formData.username" 
                  type="text" 
                  required 
                  placeholder="Masukkan username"
                  :disabled="submitting"
                />
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input 
                  id="email" 
                  v-model="formData.email" 
                  type="email" 
                  required 
                  placeholder="Masukkan email"
                  :disabled="submitting"
                />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="fullName">Nama Lengkap</label>
                <input 
                  id="fullName" 
                  v-model="formData.fullName" 
                  type="text" 
                  placeholder="Masukkan nama lengkap"
                  :disabled="submitting"
                />
              </div>
              <div class="form-group">
                <label for="role">Role</label>
                <select 
                  id="role" 
                  v-model="formData.role" 
                  required 
                  :disabled="submitting"
                  class="form-select"
                >
                  <option value="">Pilih role</option>
                  <option value="User">User</option>
                  <option value="Admin">Admin</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="address">Alamat</label>
              <textarea 
                id="address" 
                v-model="formData.address" 
                rows="3" 
                placeholder="Masukkan alamat"
                :disabled="submitting"
              ></textarea>
            </div>
            <div class="form-group">
              <label for="password">{{ mode === 'add' ? 'Password' : 'Password (kosongkan jika tidak diubah)' }}</label>
              <input 
                id="password" 
                v-model="formData.password" 
                type="password" 
                :required="mode === 'add'"
                :placeholder="mode === 'add' ? 'Masukkan password' : 'Kosongkan jika tidak diubah'"
                :disabled="submitting"
              />
            </div>
            <div class="form-actions">
              <button type="button" class="btn-cancel" @click="cancelForm" :disabled="submitting">Batal</button>
              <button type="submit" class="btn-save" :disabled="submitting">
                {{ submitting ? (mode === 'add' ? 'Menyimpan...' : 'Mengupdate...') : (mode === 'add' ? 'Simpan' : 'Update') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <ConfirmationModal
      :visible="modalVisible"
      title="Konfirmasi Hapus"
      :message="modalMessage"
      @confirm="deleteUserConfirmed"
      @cancel="modalVisible = false"
    />
  </div>
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
import axios from 'axios';

export default {
  name: 'UserTable',
  components: { ConfirmationModal },
  data() {
    return {
      users: [],
      search: '',
      loading: false,
      error: '',
      successMessage: '',
      submitting: false,
      mode: 'list', // 'list', 'add', 'edit'
      
      // Form data
      formData: {
        username: '',
        email: '',
        fullName: '',
        address: '',
        role: '',
        password: ''
      },
      
      // Edit data
      editId: null,
      
      // Delete modal
      modalVisible: false,
      userToDelete: null,
      modalMessage: '',
    };
  },
  computed: {
    filteredUsers() {
      if (!this.search) return this.users;
      const s = this.search.toLowerCase();
      return this.users.filter(u =>
        (u.fullName || u.username).toLowerCase().includes(s) ||
        u.email.toLowerCase().includes(s) ||
        u.id.toString().includes(s)
      );
    }
  },
  async mounted() {
    await this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      this.loading = true;
      this.error = '';
      try {
        const timestamp = new Date().getTime();
        const response = await axios.get(`https://mindcareindependent.com/api/get_all_users.php?t=${timestamp}`);
        
        if (response.data.success) {
          this.users = response.data.users;
        } else {
          this.error = 'Gagal mengambil data pengguna';
        }
      } catch (error) {
        this.error = 'Gagal koneksi ke server';
        console.error('Error fetching users:', error);
      } finally {
        this.loading = false;
      }
    },

    // Add methods
    addFromList() {
      this.resetForm();
      this.mode = 'add';
    },

    async submitForm() {
      if (!this.formData.username.trim() || !this.formData.email.trim() || !this.formData.password.trim() || this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        const response = await axios.post('https://mindcareindependent.com/api/add_user.php', {
          username: this.formData.username.trim(),
          email: this.formData.email.trim(),
          password: this.formData.password,
          fullName: this.formData.fullName.trim(),
          address: this.formData.address.trim(),
          role: this.formData.role || 'User'
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.resetForm();
          this.mode = 'list';
          
          await this.forceRefreshData();
          
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal menambah user';
        }
      } catch (error) {
        console.error('Error adding user:', error);
        this.error = 'Gagal menambah user: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },

    // Edit methods
    editUser(user) {
      this.editId = user.id;
      this.formData = {
        username: user.username,
        email: user.email,
        fullName: user.fullName || '',
        address: user.address || '',
        role: user.role || 'User',
        password: ''
      };
      this.mode = 'edit';
    },

    async submitEdit() {
      if (!this.formData.username.trim() || !this.formData.email.trim() || this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        const updateData = {
          id: this.editId,
          username: this.formData.username.trim(),
          email: this.formData.email.trim(),
          fullName: this.formData.fullName.trim(),
          address: this.formData.address.trim(),
          role: this.formData.role
        };

        // Hanya kirim password jika diisi
        if (this.formData.password.trim()) {
          updateData.password = this.formData.password;
        }

        const response = await axios.put('https://mindcareindependent.com/api/update_user.php', updateData);
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.resetForm();
          this.editId = null;
          this.mode = 'list';
          
          await this.forceRefreshData();
          
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal mengupdate user';
        }
      } catch (error) {
        console.error('Error updating user:', error);
        this.error = 'Gagal mengupdate user: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },

    // Force refresh data
    async forceRefreshData() {
      await this.fetchUsers();
      setTimeout(async () => {
        await this.fetchUsers();
      }, 200);
      setTimeout(async () => {
        await this.fetchUsers();
      }, 500);
    },

    cancelForm() {
      this.resetForm();
      this.editId = null;
      this.mode = 'list';
    },

    resetForm() {
      this.formData = {
        username: '',
        email: '',
        fullName: '',
        address: '',
        role: '',
        password: ''
      };
    },

    // Delete methods
    confirmDelete(user) {
      this.userToDelete = user;
      this.modalMessage = `Yakin ingin menghapus user "${user.fullName || user.username}"? Tindakan ini tidak dapat dibatalkan.`;
      this.modalVisible = true;
    },

    async deleteUserConfirmed() {
      if (!this.userToDelete || this.submitting) return;
      
      this.submitting = true;
      this.error = '';
      try {
        const response = await axios.delete('https://mindcareindependent.com/api/delete_user.php', {
          data: { id: this.userToDelete.id }
        });
        
        if (response.data.success) {
          this.successMessage = response.data.message;
          this.modalVisible = false;
          this.userToDelete = null;
          
          await this.forceRefreshData();
          
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } else {
          this.error = response.data.message || 'Gagal menghapus user';
        }
      } catch (error) {
        console.error('Error deleting user:', error);
        this.error = 'Gagal menghapus user: ' + (error.response?.data?.message || error.message);
      } finally {
        this.submitting = false;
      }
    },

    getUserInitials(name) {
      if (!name) return '';
      const names = name.split(' ');
      if (names.length === 1) {
        return names[0].charAt(0).toUpperCase();
      } else if (names.length > 1) {
        return names[0].charAt(0).toUpperCase() + names[names.length - 1].charAt(0).toUpperCase();
      }
      return '';
    }
  }
};
</script>

<style scoped>
.user-table-outer {
  width: 100%;
  max-width: 1350px;
  margin: 0 auto;
  padding-left: 40px;
  padding-right: 40px;
  box-sizing: border-box;
}

.user-table-container {
  background: #faf7f3;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(108,52,131,0.06);
  padding: 32px 24px 24px 24px;
  margin-top: 32px;
  max-width: 100%;
}

.table-title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
  gap: 18px;
}

.table-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #2d2350;
  letter-spacing: 0.2px;
}

.table-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-search-input {
  min-width: 220px;
  max-width: 320px;
  padding: 10px 16px;
  border-radius: 8px;
  border: 1.5px solid #ede7f6;
  font-size: 1rem;
  background: #faf7f3;
  color: #23263b;
  transition: border 0.2s;
}

.user-search-input:focus {
  border-color: #6C3483;
  outline: none;
}

.btn-add {
  background: #6C3483;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.btn-add:hover {
  background: #51236a;
  transform: translateY(-1px);
}

.btn-add-icon {
  font-size: 1.2rem;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  color: #6C3483;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #6C3483;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error State */
.error-state {
  background: #f8d7da;
  color: #721c24;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #f5c6cb;
  margin-bottom: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-retry {
  background: #dc3545;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-retry:hover {
  background: #c82333;
}

/* Success Message */
.success-message {
  background: #d4edda;
  color: #155724;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #c3e6cb;
  margin-bottom: 16px;
}

.table-wrapper {
  overflow-x: auto;
}

.user-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: #faf7f3;
  font-family: inherit;
}

.user-table th, .user-table td {
  padding: 14px 10px;
  text-align: left;
  font-size: 1rem;
}

.user-table thead th {
  color: #6C3483;
  font-weight: 600;
  background: #f8f5ff;
  border-bottom: 2px solid #e0d5f0;
  position: sticky;
  top: 0;
  z-index: 1;
}

.user-table tbody tr {
  border-bottom: 1px solid #f3f3f3;
  transition: background 0.18s;
}

.user-table tbody tr:hover {
  background: #f7f3fa;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 14px;
}

.avatar-container {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f3f3;
  border: 1.5px solid #ede7f6;
}

.avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  font-size: 1.2rem;
  font-weight: 600;
  color: #6C3483;
  background: #f3f3f3;
  border: 1.5px solid #ede7f6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.user-name {
  font-weight: 600;
  color: #2d2350;
  font-size: 1.04rem;
}

.user-role {
  font-size: 0.95rem;
  color: #b5b5b5;
  margin-top: 2px;
}

.role-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #fff;
  text-transform: capitalize;
}

.role-user {
  background-color: #6C3483;
}

.role-admin {
  background-color: #F44336;
}

.date-time {
  font-size: 0.92rem;
  color: #b5b5b5;
}

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.btn-edit, .btn-delete {
  background: none;
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-edit {
  color: #6C3483;
  background: #f0e8ff;
}

.btn-edit:hover {
  background: #e0d5f0;
  transform: translateY(-1px);
}

.btn-delete {
  color: #dc3545;
  background: #ffe8e8;
}

.btn-delete:hover {
  background: #ffd0d0;
  transform: translateY(-1px);
}

/* Add/Edit Form */
.add-user-card-outer {
  width: 100%;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 40px;
}

.add-user-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(108,52,131,0.1);
  padding: 48px;
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
}

.form-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d2350;
  margin-bottom: 32px;
  text-align: center;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 24px;
}

label {
  font-weight: 600;
  color: #2d2350;
  margin-bottom: 12px;
  font-size: 1.1rem;
  display: block;
}

input, textarea {
  width: 100%;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 16px;
  font-size: 1rem;
  font-family: inherit;
  background: #fff;
  transition: all 0.2s ease;
  outline: none;
  box-sizing: border-box;
}

input:focus, textarea:focus {
  border-color: #6C3483;
  box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
}

input:disabled, textarea:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
}

.form-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  background: #fff;
  transition: all 0.2s ease;
  outline: none;
  cursor: pointer;
}

.form-select:focus {
  border-color: #6C3483;
  box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
}

.form-select:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
  color: #adb5bd;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 32px;
}

.btn-cancel, .btn-save {
  padding: 12px 32px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
}

.btn-cancel {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
}

.btn-cancel:hover:not(:disabled) {
  background: #e9ecef;
  transform: translateY(-1px);
}

.btn-cancel:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-save {
  background: #6C3483;
  color: #fff;
}

.btn-save:hover:not(:disabled) {
  background: #51236a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108,52,131,0.2);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 900px) {
  .user-table-outer {
    padding-left: 8px;
    padding-right: 8px;
  }
  
  .user-table-container {
    padding: 16px 2px 10px 2px;
  }
  
  .table-title-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .table-actions {
    width: 100%;
    flex-direction: column;
    gap: 12px;
  }
  
  .user-search-input {
    min-width: 120px;
    max-width: 100%;
    font-size: 0.92rem;
    padding: 7px 10px;
  }
  
  .btn-add {
    width: 100%;
    justify-content: center;
  }
  
  .user-table th, .user-table td {
    padding: 8px 4px;
    font-size: 0.92rem;
  }
  
  .avatar-container {
    width: 32px;
    height: 32px;
  }
  
  .avatar {
    width: 100%;
    height: 100%;
  }

  .avatar-placeholder {
    font-size: 0.8rem;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }
  
  .add-user-card {
    padding: 24px;
    margin: 16px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn-cancel, .btn-save {
    width: 100%;
  }
  
  .error-state {
    flex-direction: column;
    gap: 12px;
  }
}

@media (max-width: 480px) {
  .table-title {
    font-size: 1.2rem;
  }
  
  .user-table {
    font-size: 0.85rem;
  }
  
  .user-table th, .user-table td {
    padding: 6px 2px;
  }
}
</style> 