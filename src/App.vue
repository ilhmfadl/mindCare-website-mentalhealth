<script setup>
import Header from './components/header.vue';
import Footer from './components/footer.vue';
import { useRoute } from 'vue-router';
import { computed } from 'vue';

const route = useRoute();
const isAdminRoute = computed(() => route.path.startsWith('/admin'));
</script>

<template>
  <div id="app-container">
    <Header v-if="!isAdminRoute" />
    <main>
      <router-view />
    </main>
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<style>
#app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex-grow: 1;
}
</style>
