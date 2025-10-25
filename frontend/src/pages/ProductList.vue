<template>
  <div class="product-list-page">
    <div class="page-header">
      <h1>Product Catalog</h1>
      <p>Browse our collection of quality products</p>
    </div>

    <ProductList />
  </div>
</template>

<script setup>
import ProductList from '../components/catalog/ProductList.vue'
</script>

<style scoped>
.product-list-page {
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
</style>
