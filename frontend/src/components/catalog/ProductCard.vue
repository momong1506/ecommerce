<template>
  <div class="product-card">
    <div class="product-image">
      <img
        :src="productImage"
        :alt="product.name"
        @error="handleImageError"
      />
      <div v-if="!product.is_available" class="out-of-stock-badge">
        Out of Stock
      </div>
    </div>
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-description">{{ truncatedDescription }}</p>
      <div class="product-footer">
        <span class="product-price">${{ product.price }}</span>
        <span class="product-stock">{{ stockText }}</span>
      </div>
      <button
        class="view-details-btn"
        @click="viewDetails"
        :disabled="!product.is_available"
      >
        View Details
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
})

const router = useRouter()
const imageError = ref(false)

// Inline SVG placeholder as data URI - always works, no external dependency
const PLACEHOLDER_IMAGE = `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect width='400' height='300' fill='%2342b883'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial, sans-serif' font-size='24' fill='white'%3E${encodeURIComponent(props.product.name || 'Product Image')}%3C/text%3E%3C/svg%3E`

const productImage = computed(() => {
  if (imageError.value) return PLACEHOLDER_IMAGE
  if (!props.product.image || props.product.image === '') return PLACEHOLDER_IMAGE
  return props.product.image
})

const handleImageError = (event) => {
  // If the placeholder also fails, use inline SVG
  if (!imageError.value) {
    imageError.value = true
  }
}

const truncatedDescription = computed(() => {
  if (!props.product.description) return ''
  return props.product.description.length > 100
    ? props.product.description.substring(0, 100) + '...'
    : props.product.description
})

const stockText = computed(() => {
  if (!props.product.is_available) return 'Out of stock'
  if (props.product.quantity < 10) return `Only ${props.product.quantity} left`
  return 'In stock'
})

const viewDetails = () => {
  router.push(`/products/${props.product.id}`)
}
</script>

<style scoped>
.product-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
  background: white;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product-image {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  background-color: #f5f5f5;
  transition: opacity 0.3s;
}

.product-image img[src*="data:image"] {
  object-fit: contain;
  padding: 1rem;
}

.out-of-stock-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: #e74c3c;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.product-info {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.product-name {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
}

.product-description {
  margin: 0 0 1rem 0;
  font-size: 0.9rem;
  color: #666;
  flex: 1;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.product-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #42b883;
}

.product-stock {
  font-size: 0.85rem;
  color: #999;
}

.view-details-btn {
  width: 100%;
  padding: 0.75rem;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.view-details-btn:hover:not(:disabled) {
  background-color: #359268;
}

.view-details-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}
</style>
