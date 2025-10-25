<template>
  <div id="app">
    <header class="app-header">
      <div class="container">
        <h1 class="app-title">{{ appName }}</h1>
        <nav class="app-nav">
          <router-link to="/products" class="nav-link">Products</router-link>
          <router-link to="/checkout" class="nav-link cart-link">
            Checkout
            <span v-if="cartItemCount > 0" class="cart-badge">{{ cartItemCount }}</span>
          </router-link>
        </nav>
      </div>
    </header>

    <main class="app-main">
      <div class="container">
        <router-view />
      </div>
    </main>

    <footer class="app-footer">
      <div class="container">
        <p>&copy; 2025 {{ appName }}. All rights reserved.</p>
      </div>
    </footer>

    <!-- Global Toast Notifications -->
    <ToastNotification />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCart } from './composables/useCart'
import ToastNotification from './components/common/ToastNotification.vue'

const appName = ref(import.meta.env.VITE_APP_NAME || 'E-Commerce Platform')
const { cartItemCount } = useCart()
</script>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  width: 100%;
}

.app-header {
  background-color: #2c3e50;
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.app-header .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.app-title {
  margin: 0;
  font-size: 1.5rem;
}

.app-nav {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover {
  color: #42b883;
}

.nav-link.router-link-active {
  color: #42b883;
  border-bottom: 2px solid #42b883;
}

.cart-link {
  position: relative;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -12px;
  background-color: #e74c3c;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}

.app-main {
  flex: 1;
  padding: 2rem 0;
}

.app-footer {
  background-color: #f5f5f5;
  padding: 1.5rem 0;
  margin-top: auto;
  text-align: center;
  color: #666;
}

.app-footer p {
  margin: 0;
}
</style>
