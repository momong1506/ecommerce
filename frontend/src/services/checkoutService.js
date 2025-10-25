import api from './api'

/**
 * Checkout Service
 *
 * API calls for order checkout and management
 */

export const checkoutService = {
  /**
   * Create a new order
   */
  async createOrder(orderData) {
    return await api.post('/checkout/orders', orderData)
  },

  /**
   * Get order by order number
   */
  async getOrder(orderNumber) {
    return await api.get(`/checkout/orders/${orderNumber}`)
  },
}
