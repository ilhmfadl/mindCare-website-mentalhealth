import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'

// Initialize dark mode from localStorage
function initializeDarkMode() {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.removeAttribute('data-theme');
  }
}

// Call initialization
initializeDarkMode();

createApp(App).use(router).mount('#app')
