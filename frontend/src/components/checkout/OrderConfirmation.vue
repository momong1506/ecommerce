<template>
  <div class="order-confirmation-container">
    <div v-if="loading" class="loading">
      <p>Loading order details...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button @click="$router.push('/products')" class="back-btn">
        Back to Products
      </button>
    </div>

    <div v-else-if="order" class="order-confirmation">
      <div class="success-icon">✓</div>
      <h1>Order Confirmed!</h1>
      <p class="confirmation-message">
        Thank you for your order. A confirmation email has been sent to
        <strong>{{ order.customer_email }}</strong>.
      </p>

      <div class="order-details">
        <h2>Order Details</h2>
        <div class="detail-row">
          <span class="label">Order Number:</span>
          <span class="value">{{ order.order_number }}</span>
        </div>
        <div class="detail-row">
          <span class="label">Customer Name:</span>
          <span class="value">{{ order.customer_name }}</span>
        </div>
        <div class="detail-row">
          <span class="label">Email:</span>
          <span class="value">{{ order.customer_email }}</span>
        </div>
        <div class="detail-row">
          <span class="label">Shipping Address:</span>
          <span class="value address">{{ order.shipping_address }}</span>
        </div>
        <div class="detail-row">
          <span class="label">Order Date:</span>
          <span class="value">{{ formatDate(order.created_at) }}</span>
        </div>
      </div>

      <div class="order-items">
        <h2>Order Items</h2>
        <div
          v-for="item in order.items"
          :key="item.id"
          class="order-item"
        >
          <div class="item-details">
            <span class="item-name">{{ item.product.name }}</span>
            <span class="item-quantity">× {{ item.quantity }}</span>
          </div>
          <span class="item-price">${{ item.subtotal }}</span>
        </div>
      </div>

      <div class="order-total">
        <div class="total-row">
          <span>Total:</span>
          <span class="total-amount">${{ order.total_amount }}</span>
        </div>
      </div>

      <div class="actions">
        <button @click="$router.push('/products')" class="continue-btn">
          Continue Shopping
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { checkoutService } from '../../services/checkoutService'

const route = useRoute()
const router = useRouter()

const order = ref(null)
const loading = ref(false)
const error = ref(null)

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const fetchOrder = async () => {
  loading.value = true
  error.value = null

  try {
    const orderNumber = route.params.orderNumber
    const response = await checkoutService.getOrder(orderNumber)
    order.value = response.data
  } catch (err) {
    error.value = 'Failed to load order details. Order may not exist.'
    console.error('Error fetching order:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchOrder()
})
</script>

<style scoped>
.order-confirmation-container {
  max-width: 800px;
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

.order-confirmation {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.success-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1rem;
  background-color: #42b883;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: bold;
}

.order-confirmation h1 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
}

.confirmation-message {
  color: #666;
  margin-bottom: 2rem;
}

.order-details,
.order-items {
  margin: 2rem 0;
  text-align: left;
}

.order-details h2,
.order-items h2 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
  color: #2c3e50;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e0e0e0;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-row .label {
  font-weight: 600;
  color: #666;
}

.detail-row .value {
  color: #2c3e50;
  text-align: right;
}

.detail-row .value.address {
  white-space: pre-wrap;
  max-width: 60%;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.item-details {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-name {
  color: #2c3e50;
}

.item-quantity {
  color: #666;
  font-size: 0.9rem;
}

.item-price {
  font-weight: 600;
  color: #2c3e50;
}

.order-total {
  margin: 2rem 0;
  padding-top: 1rem;
  border-top: 2px solid #e0e0e0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
}

.actions {
  margin-top: 2rem;
}

.continue-btn,
.back-btn {
  padding: 1rem 2rem;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.continue-btn:hover,
.back-btn:hover {
  background-color: #359268;
}

@media (max-width: 768px) {
  .order-confirmation {
    padding: 1rem;
  }

  .detail-row .value.address {
    max-width: 50%;
  }
}
</style>
