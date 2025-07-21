<script setup>
import Header from './components/header.vue';
import Footer from './components/footer.vue';
</script>

<template>
  <div id="app-container">
    <Header />
    <main>
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<style>
/* Global styles in App.vue or a dedicated css file */
#app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex-grow: 1;
}
</style>
