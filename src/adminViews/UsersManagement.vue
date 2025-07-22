<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <UserTable />
    </main>
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
import UserTable from './components/UserTable.vue';
export default {
  name: 'UsersManagement',
  components: { AdminHeader, UserTable }
};
</script>

<style scoped>
.admin-page-bg {
  min-height: 100vh;
  background: #faf7f3;
}
main {
  flex: 1;
}
</style> 