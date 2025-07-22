<template>
  <div>
    <AdminHeader />
    <main>
      <h1>Kelola Forum Diskusi</h1>
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