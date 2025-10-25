<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

/**
 * Order Controller
 *
 * Handles order placement and retrieval API endpoints
 */
class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {
    }

    /**
     * Create a new order
     *
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->checkoutService->createOrder($request->validated());

            return response()->json([
                'data' => $order->load('items.product'),
                'message' => 'Order created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'order_creation_failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get order by order number
     *
     * @param string $orderNumber
     * @return JsonResponse
     */
    public function showByOrderNumber(string $orderNumber): JsonResponse
    {
        try {
            $order = $this->checkoutService->getOrderByNumber($orderNumber);

            if (!$order) {
                return response()->json([
                    'error' => 'order_not_found',
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'failed_to_fetch_order',
                'message' => 'Unable to retrieve order details',
            ], 500);
        }
    }

    /**
     * Get order by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->checkoutService->getOrderByNumber($id);

            if (!$order) {
                return response()->json([
                    'error' => 'order_not_found',
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'failed_to_fetch_order',
                'message' => 'Unable to retrieve order details',
            ], 500);
        }
    }
}
