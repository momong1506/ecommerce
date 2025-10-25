<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Catalog Service
 *
 * Handles product catalog management including product listing,
 * retrieval, and availability checking.
 */
class CatalogService
{
    /**
     * Get all available products
     *
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return Product::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a single product by ID
     *
     * @param int $productId
     * @return Product|null
     */
    public function getProduct(int $productId): ?Product
    {
        return Product::where('id', $productId)
            ->where('is_available', true)
            ->first();
    }

    /**
     * Check if a product is available and has sufficient quantity
     *
     * @param int $productId
     * @param int $requestedQuantity
     * @return bool
     */
    public function isProductAvailable(int $productId, int $requestedQuantity): bool
    {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        return $product->is_available && $product->quantity >= $requestedQuantity;
    }

    /**
     * Get product current price
     *
     * @param int $productId
     * @return float|null
     */
    public function getProductPrice(int $productId): ?float
    {
        $product = Product::find($productId);

        return $product ? (float) $product->price : null;
    }
}
