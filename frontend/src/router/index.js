import { createRouter, createWebHistory } from 'vue-router'

/**
 * Vue Router Configuration
 *
 * Defines application routes for:
 * - Product Catalog
 * - Product Details
 * - Checkout
 * - Order Confirmation
 */

const routes = [
  {
    path: '/',
    name: 'Home',
    redirect: '/products',
  },
  {
    path: '/products',
    name: 'ProductList',
    component: () => import('../pages/ProductList.vue'),
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: () => import('../pages/ProductDetail.vue'),
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: () => import('../pages/Checkout.vue'),
  },
  {
    path: '/order-confirmation/:orderNumber',
    name: 'OrderConfirmation',
    component: () => import('../pages/OrderConfirmation.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
