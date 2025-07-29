<template>
  <button 
    @click="toggleDarkMode" 
    class="dark-mode-toggle"
    :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
  >
    <svg 
      v-if="isDarkMode" 
      class="icon" 
      fill="currentColor" 
      viewBox="0 0 24 24"
    >
      <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/>
    </svg>
    <svg 
      v-else 
      class="icon" 
      fill="currentColor" 
      viewBox="0 0 24 24"
    >
      <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1 -1.5 0V3a.75.75 0 0 1 .75 -0.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1 -9 0ZM18.894 6.166a.75.75 0 0 0 -1.06 -1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591 -1.59ZM21.75 12a.75.75 0 0 1 -0.75.75h-2.25a.75.75 0 0 1 0 -1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06 -1.06l-1.59 -1.591a.75.75 0 1 0 -1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1 -1.5 0v-2.25A.75.75 0 0 1 12 18ZM6.166 17.834a.75.75 0 0 0 -1.06 1.06l1.591 1.59a.75.75 0 0 0 1.06 -1.061l-1.591 -1.59ZM2.25 12a.75.75 0 0 1 .75 -.75H5.25a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1 -.75 -.75ZM6.166 6.166a.75.75 0 0 0 1.06 -1.06L5.636 3.515a.75.75 0 0 0 -1.061 1.06l1.591 1.591Z"/>
    </svg>
    <span>{{ isDarkMode ? 'Light' : 'Dark' }}</span>
  </button>
</template>

<script>
export default {
  name: 'DarkModeToggle',
  data() {
    return {
      isDarkMode: false
    }
  },
  mounted() {
    // Check for saved theme preference or default to light mode
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      this.isDarkMode = true;
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      this.isDarkMode = false;
      document.documentElement.removeAttribute('data-theme');
    }
  },
  methods: {
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode;
      
      if (this.isDarkMode) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
      }
    }
  }
}
</script>

<style scoped>
.dark-mode-toggle {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  background: var(--card-bg);
  border: 2px solid var(--border-color);
  border-radius: 50px;
  padding: 8px 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: var(--text-primary);
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px var(--shadow-light);
  font-weight: 500;
}

.dark-mode-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px var(--shadow-medium);
}

.dark-mode-toggle .icon {
  width: 20px;
  height: 20px;
  transition: transform 0.3s ease;
}

.dark-mode-toggle:hover .icon {
  transform: rotate(15deg);
}

@media (max-width: 768px) {
  .dark-mode-toggle {
    top: 15px;
    right: 15px;
    padding: 6px 10px;
    font-size: 12px;
  }
  
  .dark-mode-toggle .icon {
    width: 18px;
    height: 18px;
  }
}
</style>