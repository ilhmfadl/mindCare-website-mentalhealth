<script setup>
import Header from './components/header.vue';
import Footer from './components/footer.vue';
import ChatPopup from './components/ChatPopup.vue';
import { useRoute } from 'vue-router';
import { computed, ref, onMounted } from 'vue';

const route = useRoute();
const isAdminRoute = computed(() => route.path.startsWith('/admin'));
const isTestRoute = computed(() => route.path === '/tes-diri');
const showChat = computed(() => !isAdminRoute.value && !isTestRoute.value);

const chatPopup = ref(null);

// Make chat accessible globally
onMounted(() => {
  window.openChat = () => {
    if (chatPopup.value) {
      chatPopup.value.openChat();
    }
  };
  
  window.closeChat = () => {
    if (chatPopup.value) {
      chatPopup.value.closeChat();
    }
  };
});
</script>

<template>
  <div id="app-container">
    <Header v-if="!isAdminRoute" />
    <main>
      <router-view />
    </main>
    <Footer v-if="!isAdminRoute" />
    <ChatPopup v-if="showChat" ref="chatPopup" />
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
