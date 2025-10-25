<template>
  <div class="order-summary">
    <h2>Order Summary</h2>

    <div v-if="cart.length === 0" class="empty-cart">
      <p>Your cart is empty</p>
      <router-link to="/products" class="continue-shopping">
        Continue Shopping
      </router-link>
    </div>

    <div v-else>
      <div class="cart-items">
        <div
          v-for="item in cart"
          :key="item.product.id"
          class="cart-item"
        >
          <div class="item-image">
            <img :src="item.product.image" :alt="item.product.name" />
          </div>
          <div class="item-details">
            <h3>{{ item.product.name }}</h3>
            <p class="item-price">${{ item.product.price }} each</p>
          </div>
          <div class="item-quantity">
            <button
              class="qty-btn"
              @click="decreaseQuantity(item.product.id)"
              :disabled="item.quantity <= 1"
            >
              -
            </button>
            <span class="qty-display">{{ item.quantity }}</span>
            <button
              class="qty-btn"
              @click="increaseQuantity(item.product.id)"
              :disabled="item.quantity >= item.product.quantity"
            >
              +
            </button>
          </div>
          <div class="item-total">
            ${{ (item.product.price * item.quantity).toFixed(2) }}
          </div>
          <button
            class="remove-btn"
            @click="removeItem(item.product.id)"
            title="Remove item"
          >
            ×
          </button>
        </div>
      </div>

      <div class="order-totals">
        <div class="total-row">
          <span>Subtotal:</span>
          <span>${{ cartTotal.toFixed(2) }}</span>
        </div>
        <div class="total-row grand-total">
          <span>Total:</span>
          <span>${{ cartTotal.toFixed(2) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCart } from '../../composables/useCart'

const { cart, cartTotal, updateQuantity, removeFromCart } = useCart()

const increaseQuantity = (productId) => {
  const item = cart.value.find(i => i.product.id === productId)
  if (item) {
    updateQuantity(productId, item.quantity + 1)
  }
}

const decreaseQuantity = (productId) => {
  const item = cart.value.find(i => i.product.id === productId)
  if (item && item.quantity > 1) {
    updateQuantity(productId, item.quantity - 1)
  }
}

const removeItem = (productId) => {
  if (confirm('Are you sure you want to remove this item?')) {
    removeFromCart(productId)
  }
}
</script>

<style scoped>
.order-summary {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-summary h2 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  color: #2c3e50;
}

.empty-cart {
  text-align: center;
  padding: 2rem;
}

.empty-cart p {
  color: #666;
  margin-bottom: 1rem;
}

.continue-shopping {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background-color: #42b883;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.continue-shopping:hover {
  background-color: #359268;
}

.cart-items {
  margin-bottom: 1.5rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto auto auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e0e0e0;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 80px;
  height: 80px;
  overflow: hidden;
  border-radius: 4px;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #2c3e50;
}

.item-price {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.qty-btn:hover:not(:disabled) {
  background-color: #f5f5f5;
  border-color: #42b883;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-display {
  min-width: 30px;
  text-align: center;
  font-weight: 600;
}

.item-total {
  font-weight: 700;
  color: #2c3e50;
  min-width: 80px;
  text-align: right;
}

.remove-btn {
  width: 30px;
  height: 30px;
  border: none;
  background: #e74c3c;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1.5rem;
  line-height: 1;
  transition: background-color 0.2s;
}

.remove-btn:hover {
  background-color: #c0392b;
}

.order-totals {
  border-top: 2px solid #e0e0e0;
  padding-top: 1rem;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 1rem;
}

.grand-total {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid #e0e0e0;
}

@media (max-width: 768px) {
  .cart-item {
    grid-template-columns: 60px 1fr;
    gap: 0.75rem;
  }

  .item-image {
    width: 60px;
    height: 60px;
  }

  .item-quantity,
  .item-total,
  .remove-btn {
    grid-column: 2;
  }

  .item-quantity {
    justify-self: start;
  }

  .item-total {
    justify-self: end;
  }

  .remove-btn {
    justify-self: end;
  }
}
</style>
