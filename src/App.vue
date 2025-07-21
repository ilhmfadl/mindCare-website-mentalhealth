<script setup>
import Navbar from './components/Navbar.vue';
</script>

<template>
  <div id="app-container">
    <Navbar />
    <main>
      <router-view />
    </main>
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
  background-color: #F9F9F9;
}
</style>
