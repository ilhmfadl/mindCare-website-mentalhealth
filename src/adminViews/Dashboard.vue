<template>
  <div class="admin-dashboard">
    <AdminHeader />
    <main>
      <h1>Dashboard</h1>
      <!-- Statistik ringkas, dsb -->
    </main>
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
export default {
  name: 'AdminDashboard',
  components: { AdminHeader }
};
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #fcf7f3;
}
main {
  flex: 1;
  padding: 24px;
}
</style> 