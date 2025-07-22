<template>
  <div class="user-table-outer">
    <div class="user-table-container">
      <div class="table-title-row">
        <div class="table-title">Daftar Pengguna</div>
        <input
          v-model="search"
          class="user-search-input"
          type="text"
          placeholder="Cari nama, email, atau ID..."
        />
      </div>
      <div class="table-wrapper">
        <table class="user-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>ID</th>
              <th>Email</th>
              <th>Phone number</th>
              <th>Date added</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="user-info">
                <img :src="user.avatar" class="avatar" alt="avatar" />
                <div>
                  <div class="user-name">{{ user.name }}</div>
                  <div class="user-role">{{ user.role }}</div>
                </div>
              </td>
              <td>{{ user.id }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone }}</td>
              <td>
                <div>{{ user.date }}</div>
                <div class="date-time">{{ user.time }}</div>
              </td>
              <td>
                <button class="delete-btn" @click="showDeleteModal(user)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <ConfirmationModal
        :visible="modalVisible"
        title="Konfirmasi Hapus"
        :message="modalMessage"
        @confirm="deleteUserConfirmed"
        @cancel="modalVisible = false"
      />
    </div>
  </div>
</template>

<script>
import ConfirmationModal from '../../components/ConfirmationModal.vue';
export default {
  name: 'UserTable',
  components: { ConfirmationModal },
  data() {
    return {
      users: [
        {
          id: '87364523',
          name: 'Brooklyn Simmons',
          role: 'Dermatologists',
          email: 'brooklyn@mail.com',
          phone: '(603) 555-0123',
          date: '21/12/2022',
          time: '10:40 PM',
          avatar: 'https://randomuser.me/api/portraits/men/32.jpg'
        },
        {
          id: '93874563',
          name: 'Kristin Watson',
          role: 'Infectious disease',
          email: 'kristinw@mail.com',
          phone: '(219) 555-0114',
          date: '22/12/2022',
          time: '05:20 PM',
          avatar: 'https://randomuser.me/api/portraits/women/44.jpg'
        },
        {
          id: '23847569',
          name: 'Jacob Jones',
          role: 'Ophthalmologists',
          email: 'jacobj@mail.com',
          phone: '(319) 555-0115',
          date: '23/12/2022',
          time: '12:40 PM',
          avatar: 'https://randomuser.me/api/portraits/men/45.jpg'
        },
        {
          id: '39485632',
          name: 'Cody Fisher',
          role: 'Cardiologists',
          email: 'codyf@mail.com',
          phone: '(229) 555-0109',
          date: '24/12/2022',
          time: '03:00 PM',
          avatar: 'https://randomuser.me/api/portraits/men/46.jpg'
        }
      ],
      search: '',
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
        u.name.toLowerCase().includes(s) ||
        u.email.toLowerCase().includes(s) ||
        u.id.toLowerCase().includes(s)
      );
    }
  },
  methods: {
    showDeleteModal(user) {
      this.userToDelete = user;
      this.modalMessage = `Yakin ingin menghapus user \"${user.name}\"? Tindakan ini tidak dapat dibatalkan.`;
      this.modalVisible = true;
    },
    deleteUserConfirmed() {
      if (this.userToDelete) {
        this.users = this.users.filter(u => u.id !== this.userToDelete.id);
      }
      this.modalVisible = false;
      this.userToDelete = null;
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
  /* background: #fff; */
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
.table-wrapper {
  overflow-x: auto;
}
.user-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  /* background: #fff; */
  background: #faf7f3;
  font-family: inherit;
}
.user-table th, .user-table td {
  padding: 14px 10px;
  text-align: left;
  font-size: 1rem;
}
.user-table thead th {
  color: #a3a3a3;
  font-weight: 600;
  background: #f7f3fa;
  border-bottom: 2px solid #ede7f6;
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
.avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
  background: #f3f3f3;
  border: 1.5px solid #ede7f6;
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
.date-time {
  font-size: 0.92rem;
  color: #b5b5b5;
}
.delete-btn {
  background: #f87171;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 6px 16px;
  font-size: 0.98rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 4px rgba(249,113,113,0.08);
}
.delete-btn:hover {
  background: #ef4444;
  box-shadow: 0 2px 8px rgba(249,113,113,0.13);
}
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
  .table-title {
    font-size: 1.08rem;
    margin-bottom: 10px;
  }
  .user-search-input {
    min-width: 120px;
    max-width: 100%;
    font-size: 0.92rem;
    padding: 7px 10px;
  }
  .user-table th, .user-table td {
    padding: 8px 4px;
    font-size: 0.92rem;
  }
  .avatar {
    width: 32px;
    height: 32px;
  }
}
</style> 