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
      <p>No products available at the moment.</p>
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

.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.retry-btn:hover {
  background-color: #359268;
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
