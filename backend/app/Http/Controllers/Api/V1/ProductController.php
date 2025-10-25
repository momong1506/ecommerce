<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductsRequest;
use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;

/**
 * Product Controller
 *
 * Handles product catalog API endpoints
 */
class ProductController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {
    }

    /**
     * Get all available products
     *
     * @param ListProductsRequest $request
     * @return JsonResponse
     */
    public function index(ListProductsRequest $request): JsonResponse
    {
        try {
            $products = $this->catalogService->getProducts();

            return response()->json([
                'data' => $products,
                'meta' => [
                    'total' => $products->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'failed_to_fetch_products',
                'message' => 'Unable to retrieve products',
            ], 500);
        }
    }

    /**
     * Get a single product by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->catalogService->getProduct($id);

            if (!$product) {
                return response()->json([
                    'error' => 'product_not_found',
                    'message' => 'Product not found or not available',
                ], 404);
            }

            return response()->json([
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'failed_to_fetch_product',
                'message' => 'Unable to retrieve product details',
            ], 500);
        }
    }
}
