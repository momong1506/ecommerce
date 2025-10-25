<template>
  <div class="checkout-form">
    <h2>Customer Information</h2>

    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="customer_name">Full Name *</label>
        <input
          type="text"
          id="customer_name"
          v-model="formData.customer_name"
          placeholder="John Doe"
          required
          minlength="2"
          maxlength="255"
        />
        <span v-if="errors.customer_name" class="error">{{ errors.customer_name }}</span>
      </div>

      <div class="form-group">
        <label for="customer_email">Email Address *</label>
        <input
          type="email"
          id="customer_email"
          v-model="formData.customer_email"
          placeholder="john.doe@example.com"
          required
          maxlength="255"
        />
        <span v-if="errors.customer_email" class="error">{{ errors.customer_email }}</span>
      </div>

      <div class="form-group">
        <label for="shipping_address">Shipping Address *</label>
        <textarea
          id="shipping_address"
          v-model="formData.shipping_address"
          placeholder="123 Main St, Apt 4B&#10;New York, NY 10001&#10;United States"
          required
          minlength="10"
          maxlength="500"
          rows="4"
        ></textarea>
        <span v-if="errors.shipping_address" class="error">{{ errors.shipping_address }}</span>
      </div>

      <div class="form-actions">
        <button
          type="submit"
          class="submit-btn"
          :disabled="submitting || cart.length === 0"
        >
          {{ submitting ? 'Processing...' : 'Place Order' }}
        </button>
        <button
          type="button"
          class="cancel-btn"
          @click="$router.push('/products')"
          :disabled="submitting"
        >
          Continue Shopping
        </button>
      </div>

      <div v-if="errors.general" class="error-message">
        {{ errors.general }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '../../composables/useCart'
import { checkoutService } from '../../services/checkoutService'

const router = useRouter()
const { cart, clearCart } = useCart()

const emit = defineEmits(['order-placed'])

const formData = ref({
  customer_name: '',
  customer_email: '',
  shipping_address: '',
})

const errors = ref({})
const submitting = ref(false)

const validateForm = () => {
  errors.value = {}

  if (!formData.value.customer_name || formData.value.customer_name.length < 2) {
    errors.value.customer_name = 'Name must be at least 2 characters'
  }

  if (!formData.value.customer_email || !formData.value.customer_email.includes('@')) {
    errors.value.customer_email = 'Please provide a valid email address'
  }

  if (!formData.value.shipping_address || formData.value.shipping_address.length < 10) {
    errors.value.shipping_address = 'Address must be at least 10 characters'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  if (cart.value.length === 0) {
    errors.value.general = 'Your cart is empty'
    return
  }

  submitting.value = true
  errors.value = {}

  try {
    const orderData = {
      ...formData.value,
      items: cart.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity,
      })),
    }

    const response = await checkoutService.createOrder(orderData)

    // Clear cart and redirect
    clearCart()
    emit('order-placed', response.data)
    router.push(`/order-confirmation/${response.data.order_number}`)
  } catch (error) {
    console.error('Order submission error:', error)
    errors.value.general = error.response?.data?.message || 'Failed to place order. Please try again.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.checkout-form {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.checkout-form h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #42b883;
}

.form-group textarea {
  resize: vertical;
}

.error {
  display: block;
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.submit-btn,
.cancel-btn {
  flex: 1;
  padding: 1rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.submit-btn {
  background-color: #42b883;
  color: white;
}

.submit-btn:hover:not(:disabled) {
  background-color: #359268;
}

.submit-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.cancel-btn {
  background-color: #f5f5f5;
  color: #2c3e50;
}

.cancel-btn:hover:not(:disabled) {
  background-color: #e0e0e0;
}

.error-message {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #fee;
  border: 1px solid #e74c3c;
  border-radius: 4px;
  color: #e74c3c;
}

@media (max-width: 768px) {
  .form-actions {
    flex-direction: column;
  }
}
</style>
