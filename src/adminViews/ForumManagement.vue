<template>
  <div class="admin-page-bg">
    <AdminHeader />
    <main>
      <ForumTable />
    </main>
  </div>
</template>

<script>
import AdminHeader from './components/AdminHeader.vue';
import ForumTable from './components/ForumTable.vue';
export default {
  name: 'ForumManagement',
  components: { AdminHeader, ForumTable }
};
</script> 