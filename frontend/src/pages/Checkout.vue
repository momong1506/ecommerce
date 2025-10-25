<template>
  <div class="checkout-page">
    <div class="page-header">
      <h1>Checkout</h1>
      <p>Complete your order</p>
    </div>

    <div class="checkout-container">
      <div class="checkout-form-section">
        <CheckoutForm @order-placed="handleOrderPlaced" />
      </div>

      <div class="order-summary-section">
        <OrderSummary />
      </div>
    </div>
  </div>
</template>

<script setup>
import CheckoutForm from '../components/checkout/CheckoutForm.vue'
import OrderSummary from '../components/checkout/OrderSummary.vue'

const handleOrderPlaced = (order) => {
  console.log('Order placed:', order)
}
</script>

<style scoped>
.checkout-page {
  width: 100%;
}

.page-header {
  text-align: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2.5rem;
  color: #2c3e50;
}

.page-header p {
  margin: 0;
  font-size: 1.1rem;
  color: #666;
}

.checkout-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 968px) {
  .checkout-container {
    grid-template-columns: 1fr;
  }

  .order-summary-section {
    order: -1;
  }
}
</style>
