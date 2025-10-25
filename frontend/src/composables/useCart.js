import { ref, computed } from 'vue'

/**
 * Shopping Cart Composable
 *
 * Manages shopping cart state and operations
 */

const cart = ref([])

export function useCart() {
  const addToCart = (product, quantity = 1) => {
    const existingItem = cart.value.find(item => item.product.id === product.id)

    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      cart.value.push({
        product,
        quantity,
      })
    }
  }

  const removeFromCart = (productId) => {
    const index = cart.value.findIndex(item => item.product.id === productId)
    if (index > -1) {
      cart.value.splice(index, 1)
    }
  }

  const updateQuantity = (productId, quantity) => {
    const item = cart.value.find(item => item.product.id === productId)
    if (item) {
      item.quantity = quantity
    }
  }

  const clearCart = () => {
    cart.value = []
  }

  const cartItems = computed(() => cart.value)

  const cartItemCount = computed(() => {
    return cart.value.reduce((total, item) => total + item.quantity, 0)
  })

  const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => {
      return total + (parseFloat(item.product.price) * item.quantity)
    }, 0)
  })

  return {
    cart: cartItems,
    cartItemCount,
    cartTotal,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
  }
}
