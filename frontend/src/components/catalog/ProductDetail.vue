<template>
  <div class="product-detail-container">
    <div v-if="loading" class="loading">
      <p>Loading product details...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button @click="$router.push('/products')" class="back-btn">
        Back to Products
      </button>
    </div>

    <div v-else-if="product" class="product-detail">
      <div class="product-image-section">
        <img :src="product.image" :alt="product.name" class="product-image" />
        <div v-if="!product.is_available" class="out-of-stock-badge">
          Out of Stock
        </div>
      </div>

      <div class="product-info-section">
        <h1 class="product-title">{{ product.name }}</h1>

        <div class="product-price-section">
          <span class="product-price">${{ product.price }}</span>
          <span class="product-availability" :class="availabilityClass">
            {{ availabilityText }}
          </span>
        </div>

        <div class="product-description">
          <h3>Description</h3>
          <p>{{ product.description || 'No description available.' }}</p>
        </div>

        <div class="product-stock-info">
          <p><strong>Stock:</strong> {{ stockText }}</p>
          <p v-if="product.is_available">
            <strong>Availability:</strong> In Stock
          </p>
        </div>

        <div class="product-actions">
          <button
            class="add-to-cart-btn"
            :disabled="!product.is_available"
            @click="addToCartHandler"
          >
            {{ product.is_available ? 'Add to Cart' : 'Out of Stock' }}
          </button>
          <button class="back-btn" @click="$router.push('/products')">
            Back to Products
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { catalogService } from '../../services/catalogService'
import { useCart } from '../../composables/useCart'
import { useToast } from '../../composables/useToast'

const route = useRoute()
const router = useRouter()
const { addToCart } = useCart()
const { success } = useToast()

const product = ref(null)
const loading = ref(false)
const error = ref(null)

const availabilityText = computed(() => {
  if (!product.value) return ''
  return product.value.is_available ? 'In Stock' : 'Out of Stock'
})

const availabilityClass = computed(() => {
  if (!product.value) return ''
  return product.value.is_available ? 'in-stock' : 'out-of-stock'
})

const stockText = computed(() => {
  if (!product.value) return ''
  if (!product.value.is_available) return 'Out of stock'
  if (product.value.quantity < 10) return `Only ${product.value.quantity} left`
  return `${product.value.quantity} available`
})

const fetchProduct = async () => {
  loading.value = true
  error.value = null

  try {
    const productId = route.params.id
    const response = await catalogService.getProduct(productId)
    product.value = response.data
  } catch (err) {
    error.value = 'Failed to load product details. Product may not exist or is unavailable.'
    console.error('Error fetching product:', err)
  } finally {
    loading.value = false
  }
}

const addToCartHandler = () => {
  addToCart(product.value, 1)
  success(`${product.value.name} has been added to cart!`)
  // Optionally redirect to checkout
  // router.push('/checkout')
}

onMounted(() => {
  fetchProduct()
})
</script>

<style scoped>
.product-detail-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.loading,
.error {
  text-align: center;
  padding: 3rem 1rem;
  font-size: 1.1rem;
}

.error {
  color: #e74c3c;
}

.product-detail {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.product-image-section {
  position: relative;
}

.product-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
  object-fit: cover;
}

.out-of-stock-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background-color: #e74c3c;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 600;
}

.product-info-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.product-title {
  margin: 0;
  font-size: 2rem;
  color: #2c3e50;
}

.product-price-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.product-price {
  font-size: 2rem;
  font-weight: 700;
  color: #42b883;
}

.product-availability {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 600;
}

.product-availability.in-stock {
  background-color: #d4edda;
  color: #155724;
}

.product-availability.out-of-stock {
  background-color: #f8d7da;
  color: #721c24;
}

.product-description h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
  color: #2c3e50;
}

.product-description p {
  margin: 0;
  line-height: 1.6;
  color: #666;
}

.product-stock-info p {
  margin: 0.5rem 0;
  color: #666;
}

.product-actions {
  display: flex;
  gap: 1rem;
  margin-top: auto;
}

.add-to-cart-btn,
.back-btn {
  flex: 1;
  padding: 1rem 2rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.add-to-cart-btn {
  background-color: #42b883;
  color: white;
}

.add-to-cart-btn:hover:not(:disabled) {
  background-color: #359268;
}

.add-to-cart-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.back-btn {
  background-color: #f5f5f5;
  color: #2c3e50;
}

.back-btn:hover {
  background-color: #e0e0e0;
}

@media (max-width: 768px) {
  .product-detail {
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 1rem;
  }

  .product-title {
    font-size: 1.5rem;
  }

  .product-price {
    font-size: 1.5rem;
  }

  .product-actions {
    flex-direction: column;
  }
}
</style>
