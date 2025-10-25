import api from './api'

/**
 * Catalog Service
 *
 * API calls for product catalog management
 */

export const catalogService = {
  /**
   * Get all products
   */
  async getProducts() {
    return await api.get('/catalog/products')
  },

  /**
   * Get single product by ID
   */
  async getProduct(id) {
    return await api.get(`/catalog/products/${id}`)
  },
}
