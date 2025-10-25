<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Inventory Service
 *
 * Handles inventory management with pessimistic locking
 * to prevent race conditions during concurrent orders.
 */
class InventoryService
{
    /**
     * Reserve inventory for a product
     *
     * Uses pessimistic locking (SELECT FOR UPDATE) to prevent
     * race conditions when multiple orders are placed concurrently.
     *
     * @param int $productId
     * @param int $quantity
     * @return void
     * @throws Exception
     */
    public function reserveInventory(int $productId, int $quantity): void
    {
        // Use pessimistic locking to prevent race conditions
        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new Exception("Product {$productId} not found");
        }

        if (!$product->is_available) {
            throw new Exception("Product {$product->name} is not available");
        }

        if ($product->quantity < $quantity) {
            throw new Exception("Insufficient inventory for product {$product->name}. Available: {$product->quantity}, Requested: {$quantity}");
        }

        // Decrement inventory
        $product->quantity -= $quantity;

        // Mark as unavailable if quantity reaches zero
        if ($product->quantity === 0) {
            $product->is_available = false;
        }

        $product->save();
    }

    /**
     * Get current stock level for a product
     *
     * @param int $productId
     * @return int|null
     */
    public function getStockLevel(int $productId): ?int
    {
        $product = Product::find($productId);

        return $product ? $product->quantity : null;
    }

    /**
     * Check if product has sufficient stock
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function hasStock(int $productId, int $quantity): bool
    {
        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        return $product->quantity >= $quantity;
    }

    /**
     * Restore inventory (for order cancellations)
     *
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    public function restoreInventory(int $productId, int $quantity): void
    {
        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new Exception("Product {$productId} not found");
        }

        $product->quantity += $quantity;
        $product->is_available = true;
        $product->save();
    }
}
