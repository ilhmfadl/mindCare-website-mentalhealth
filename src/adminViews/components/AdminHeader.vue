<template>
  <header class="admin-header">
    <div class="header-inner">
      <nav class="admin-nav">
        <router-link to="/admin/users" class="nav-link" active-class="active"><span class="nav-text">Users</span></router-link>
        <router-link to="/admin/questioner" class="nav-link" active-class="active"><span class="nav-text">Pertanyaan</span></router-link>
        <router-link to="/admin/journal" class="nav-link" active-class="active"><span class="nav-text">Jurnal</span></router-link>
        <router-link to="/admin/chat" class="nav-link" active-class="active"><span class="nav-text">Inbox</span></router-link>
      </nav>
      <div class="admin-info">
        <div class="admin-name-role">
          <div class="admin-name">{{ adminName }}</div>
          <div class="admin-role">Admin</div>
        </div>
        <div class="admin-avatar">{{ adminInitials }}</div>
        <button class="logout-btn" @click="showLogoutModal" title="Logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16,17 21,12 16,7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Confirmation Modal untuk Logout -->
    <ConfirmationModal
      :isVisible="showModal"
      title="Konfirmasi Logout"
      message="Apakah Anda yakin ingin keluar dari sistem admin?"
      confirmText="Ya, Logout"
      cancelText="Batal"
      @close="hideLogoutModal"
      @confirm="performLogout"
    />
  </header>
</template>

<script>
import axios from 'axios';
import ConfirmationModal from '../../components/ConfirmationModal.vue';

export default {
  name: 'AdminHeader',
  components: {
    ConfirmationModal
  },
  data() {
    return {
      adminName: 'Admin',
      adminInitials: 'A',
      showModal: false
    }
  },
  mounted() {
    // Ambil data admin dari localStorage
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    if (user && user.role === 'admin') {
      this.adminName = user.fullName || user.username || 'Admin';
      this.adminInitials = (user.fullName || user.username || 'A').slice(0, 2).toUpperCase();
    }
  },
  methods: {
    showLogoutModal() {
      this.showModal = true;
    },
    
    hideLogoutModal() {
      this.showModal = false;
    },
    
    async performLogout() {
      try {
        // Ambil user ID dari localStorage
        const user = JSON.parse(localStorage.getItem('user') || 'null');
        
        if (user && user.id) {
          // Call logout API
          const formData = new FormData();
          formData.append('user_id', user.id);
          
          await axios.post('https://mindcareindependent.com/api/logout.php', formData);
        }
      } catch (error) {
        console.error('Error during logout:', error);
      } finally {
        // Clear localStorage
        localStorage.removeItem('user');
        
        // Redirect ke halaman login
        this.$router.push('/login');
        setTimeout(() => { window.location.reload(); }, 100);
      }
    }
  }
};
</script>

<style scoped>
.admin-header {
  width: 100%;
  background: #6C3483;
  color: #fff;
  padding: 0;
  height: 72px;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  border-bottom: 1.5px solid #ede7f6;
}
.header-inner {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  height: 72px;
}
.admin-nav {
  display: flex;
  gap: 8px;
  flex: 1;
  justify-content: flex-start;
}
.nav-link {
  color: #fff;
  text-decoration: none;
  font-size: 1.08rem;
  font-weight: 500;
  padding: 10px 24px 2px 24px;
  border-radius: 0;
  background: transparent;
  margin: 0 2px;
  position: relative;
  top: 2px;
  box-shadow: none;
  z-index: 1;
}
.nav-text {
  display: inline-block;
  border-bottom: 2px solid transparent;
  transition: border-color 0.25s, color 0.25s;
  padding: 0;
  margin-bottom: 12px;
}
.nav-link.active .nav-text,
.nav-link.router-link-exact-active .nav-text {
  color: #fffbe9;
  border-bottom: 2px solid #fffbe9;
}
.nav-link:hover .nav-text {
  color: #fffbe9;
  border-bottom: 2px solid #d1c4e9;
}
.admin-info {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-left: 24px;
}
.admin-name-role {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  line-height: 1.1;
}
.admin-name {
  font-weight: 600;
  font-size: 1.08rem;
  margin-right: 0;
  white-space: nowrap;
}
.admin-role {
  font-size: 0.92rem;
  color: #e0d7f3;
  margin-left: 0;
  margin-top: 2px;
  font-weight: 400;
}
.admin-avatar {
  width: 36px;
  height: 36px;
  background: #fff;
  color: #6C3483;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
  box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}

.logout-btn {
  background: transparent;
  border: none;
  color: #fff;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}
@media (max-width: 900px) {
  .header-inner {
    padding: 0 8px;
  }
  .admin-nav .nav-link {
    padding: 8px 10px 2px 10px;
    font-size: 0.97rem;
  }
  .admin-avatar { width: 28px; height: 28px; font-size: 1rem; }
}
@media (max-width: 700px) {
  .header-inner {
    flex-direction: column;
    height: auto;
    gap: 8px;
    padding: 8px 2px;
  }
  .admin-nav {
    justify-content: flex-start;
    flex-wrap: wrap;
    gap: 4px;
    width: 100%;
  }
  .admin-info {
    margin-left: 0;
    margin-top: 4px;
  }
}
</style> 