<template>
  <div class="product-list-container">
    <div v-if="loading" class="loading">
      <p>Loading products...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button @click="fetchProducts" class="retry-btn">Retry</button>
    </div>

    <div v-else-if="products.length === 0" class="empty">
      <div class="empty-icon">🛍️</div>
      <h2 class="empty-title">No Products Available</h2>
      <p class="empty-message">Our catalog is currently empty. Please check back later!</p>
      <div class="empty-dev-hint">
        <details>
          <summary>Developer Note</summary>
          <p>To populate sample products, run:</p>
          <code>docker-compose exec backend php artisan db:seed --class=ProductSeeder</code>
        </details>
      </div>
      <button @click="fetchProducts" class="refresh-btn">Refresh Catalog</button>
    </div>

    <div v-else class="product-grid">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductCard from './ProductCard.vue'
import { catalogService } from '../../services/catalogService'

const products = ref([])
const loading = ref(false)
const error = ref(null)

const fetchProducts = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await catalogService.getProducts()
    products.value = response.data || []
  } catch (err) {
    error.value = 'Failed to load products. Please try again later.'
    console.error('Error fetching products:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.product-list-container {
  width: 100%;
}

.loading,
.error,
.empty {
  text-align: center;
  padding: 3rem 1rem;
  font-size: 1.1rem;
  color: #666;
}

.error {
  color: #e74c3c;
}

.empty {
  background: white;
  border-radius: 12px;
  padding: 4rem 2rem;
  margin: 2rem auto;
  max-width: 600px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.empty-title {
  margin: 0 0 1rem 0;
  font-size: 1.8rem;
  color: #2c3e50;
}

.empty-message {
  margin: 0 0 2rem 0;
  font-size: 1.1rem;
  color: #666;
}

.empty-dev-hint {
  margin: 2rem 0;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #42b883;
}

.empty-dev-hint details {
  text-align: left;
}

.empty-dev-hint summary {
  cursor: pointer;
  font-weight: 600;
  color: #42b883;
  margin-bottom: 0.5rem;
}

.empty-dev-hint p {
  margin: 0.5rem 0;
  font-size: 0.9rem;
  color: #666;
}

.empty-dev-hint code {
  display: block;
  padding: 0.75rem;
  background-color: #2c3e50;
  color: #42b883;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  overflow-x: auto;
  white-space: nowrap;
}

.retry-btn,
.refresh-btn {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.retry-btn:hover,
.refresh-btn:hover {
  background-color: #359268;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  padding: 1rem 0;
}

@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
  }
}
</style>
