<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Checkout Service
 *
 * Handles order creation, validation, and order management.
 * Works with InventoryService to reserve stock and EmailService
 * to send order confirmations.
 */
class CheckoutService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected EmailService $emailService,
        protected CatalogService $catalogService
    ) {
    }

    /**
     * Create a new order
     *
     * @param array $orderData
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            // Validate inventory availability
            $this->validateInventory($orderData['items']);

            // Calculate total amount
            $totalAmount = $this->calculateTotal($orderData['items']);

            // Create the order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'],
                'shipping_address' => $orderData['shipping_address'],
                'total_amount' => $totalAmount,
            ]);

            // Create order items and reserve inventory
            foreach ($orderData['items'] as $item) {
                $price = $this->catalogService->getProductPrice($item['product_id']);
                $subtotal = $price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Reserve inventory
                $this->inventoryService->reserveInventory(
                    $item['product_id'],
                    $item['quantity']
                );
            }

            // Queue email notification (async, non-blocking)
            // Wrapped in try-catch to ensure order succeeds even if email queueing fails
            try {
                $this->emailService->sendOrderConfirmation($order);
            } catch (\Exception $e) {
                // Log the error but don't fail the order
                \Log::error('Failed to queue order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $order;
        });
    }

    /**
     * Get order by order number
     *
     * @param string $orderNumber
     * @return Order|null
     */
    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->first();
    }

    /**
     * Validate inventory availability for order items
     *
     * @param array $items
     * @throws Exception
     */
    protected function validateInventory(array $items): void
    {
        foreach ($items as $item) {
            if (!$this->catalogService->isProductAvailable($item['product_id'], $item['quantity'])) {
                throw new Exception("Product {$item['product_id']} is not available in requested quantity");
            }
        }
    }

    /**
     * Calculate total amount for order items
     *
     * @param array $items
     * @return float
     */
    protected function calculateTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $price = $this->catalogService->getProductPrice($item['product_id']);
            $total += $price * $item['quantity'];
        }

        return $total;
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "{$prefix}-{$timestamp}-{$random}";
    }
}
