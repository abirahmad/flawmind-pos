<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreProductRequest;
use Modules\Procurement\Http\Requests\UpdateProductRequest;
use Modules\Procurement\Http\Resources\ProductResource;
use Modules\Procurement\Http\Resources\ProductCollection;
use Modules\Procurement\Models\Product;
use Modules\Procurement\Services\ProductService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Products",
    description: "Product management API endpoints"
)]
class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProductService $productService) {}

    /*
    |--------------------------------------------------------------------------
    | List products
    |--------------------------------------------------------------------------
    */
    #[OA\Get(
        path: "/v1/procurement/products",
        summary: "List products",
        description: "Get paginated list of products with optional filters",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",        in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page",    in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "type",        in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["single","variable","modifier","combo"])),
            new OA\Parameter(name: "category_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "brand_id",    in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "location_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "is_inactive", in: "query", required: false, schema: new OA\Schema(type: "boolean")),
            new OA\Parameter(name: "search",      in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "sort_by",     in: "query", required: false, schema: new OA\Schema(type: "string", default: "created_at")),
            new OA\Parameter(name: "sort_order",  in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc","desc"], default: "desc")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Products retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user    = $request->user();
        $filters = array_merge($request->only([
            'type', 'category_id', 'brand_id', 'location_id', 'is_inactive', 'search', 'sort_by', 'sort_order',
        ]), ['business_id' => $user->business_id]);

        $products = $this->productService->getProducts($filters, $request->input('per_page', 15));

        return $this->success(new ProductCollection($products), 'Products retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Create product
    |--------------------------------------------------------------------------
    */
    #[OA\Post(
        path: "/v1/procurement/products",
        summary: "Create a new product",
        description: "Create a new product (single, variable, modifier, or combo)",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type", "sku", "unit_id"],
                properties: [
                    new OA\Property(property: "name",               type: "string",  example: "Widget Pro"),
                    new OA\Property(property: "sku",                type: "string",  example: "WGT-001"),
                    new OA\Property(property: "type",               type: "string",  enum: ["single","variable","modifier","combo"]),
                    new OA\Property(property: "unit_id",            type: "integer", example: 1),
                    new OA\Property(property: "secondary_unit_id",  type: "integer", nullable: true),
                    new OA\Property(property: "brand_id",           type: "integer", nullable: true),
                    new OA\Property(property: "category_id",        type: "integer", nullable: true),
                    new OA\Property(property: "sub_category_id",    type: "integer", nullable: true),
                    new OA\Property(property: "tax",                type: "integer", nullable: true),
                    new OA\Property(property: "tax_type",           type: "string",  enum: ["inclusive","exclusive"], example: "exclusive"),
                    new OA\Property(property: "enable_stock",       type: "boolean", example: true),
                    new OA\Property(property: "alert_quantity",     type: "number",  example: 5),
                    new OA\Property(property: "barcode_type",       type: "string",  enum: ["C39","C128","EAN-13","EAN-8","UPC-A","UPC-E","ITF-14"]),
                    new OA\Property(property: "warranty_id",        type: "integer", nullable: true),
                    new OA\Property(property: "weight",             type: "string",  nullable: true),
                    new OA\Property(property: "product_description",type: "string",  nullable: true),
                    new OA\Property(property: "is_inactive",        type: "boolean", example: false),
                    new OA\Property(property: "not_for_selling",    type: "boolean", example: false),
                    new OA\Property(property: "expiry_period",      type: "number",  nullable: true),
                    new OA\Property(property: "expiry_period_type", type: "string",  enum: ["days","months"], nullable: true),
                    new OA\Property(property: "enable_sr_no",       type: "boolean", example: false),
                    new OA\Property(property: "location_ids",       type: "array",   items: new OA\Items(type: "integer")),
                    new OA\Property(property: "product_custom_field1", type: "string", nullable: true),
                    new OA\Property(
                        property: "variations",
                        description: "For single/modifier products",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "sub_sku",                type: "string"),
                                new OA\Property(property: "default_purchase_price", type: "number"),
                                new OA\Property(property: "dpp_inc_tax",            type: "number"),
                                new OA\Property(property: "profit_percent",         type: "number"),
                                new OA\Property(property: "default_sell_price",     type: "number"),
                                new OA\Property(property: "sell_price_inc_tax",     type: "number"),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: "product_variations",
                        description: "For variable products",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "name",                  type: "string"),
                                new OA\Property(property: "variation_template_id", type: "integer", nullable: true),
                                new OA\Property(property: "variations",            type: "array", items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "name",                   type: "string"),
                                        new OA\Property(property: "variation_value_id",     type: "integer", nullable: true),
                                        new OA\Property(property: "sub_sku",                type: "string"),
                                        new OA\Property(property: "default_purchase_price", type: "number"),
                                        new OA\Property(property: "sell_price_inc_tax",     type: "number"),
                                    ]
                                )),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Product created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by'  => $user->id,
            ]);

            // Validate SKU uniqueness within the business
            if (!$this->productService->isSkuUnique($data['sku'], $user->business_id)) {
                return $this->error('SKU already exists in this business', 422);
            }

            $product = $this->productService->createProduct($data);

            return $this->created(new ProductResource($product), 'Product created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show product
    |--------------------------------------------------------------------------
    */
    #[OA\Get(
        path: "/v1/procurement/products/{id}",
        summary: "Get product details",
        description: "Get full details of a product including variations, stock, and price groups",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Product retrieved successfully"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id, $request->user()->business_id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        return $this->success(new ProductResource($product), 'Product retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Update product
    |--------------------------------------------------------------------------
    */
    #[OA\Put(
        path: "/v1/procurement/products/{id}",
        summary: "Update a product",
        description: "Update an existing product's core fields",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Product updated successfully"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $user    = $request->user();
            $product = Product::where('id', $id)->where('business_id', $user->business_id)->first();

            if (!$product) {
                return $this->notFound('Product not found');
            }

            $product = $this->productService->updateProduct($product, $request->validated());

            return $this->success(new ProductResource($product), 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete product
    |--------------------------------------------------------------------------
    */
    #[OA\Delete(
        path: "/v1/procurement/products/{id}",
        summary: "Delete a product",
        description: "Soft-delete a product and its variations",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Product deleted successfully"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user    = $request->user();
            $product = Product::where('id', $id)->where('business_id', $user->business_id)->first();

            if (!$product) {
                return $this->notFound('Product not found');
            }

            $this->productService->deleteProduct($product);

            return $this->success(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get stock per location
    |--------------------------------------------------------------------------
    */
    #[OA\Get(
        path: "/v1/procurement/products/{id}/stock",
        summary: "Get product stock",
        description: "Get stock quantities per location for all variations of a product",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Stock retrieved successfully"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function stock(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $stock = $this->productService->getStock($id, $user->business_id);

        return $this->success($stock, 'Stock retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Get product variations
    |--------------------------------------------------------------------------
    */
    #[OA\Get(
        path: "/v1/procurement/products/{id}/variations",
        summary: "Get product variations",
        description: "Get all variations for a specific product",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Variations retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function variations(Request $request, int $id): JsonResponse
    {
        $variations = $this->productService->getVariations($id, $request->user()->business_id);
        return $this->success($variations, 'Variations retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Get group prices
    |--------------------------------------------------------------------------
    */
    #[OA\Get(
        path: "/v1/procurement/products/{id}/group-prices",
        summary: "Get selling price group prices",
        description: "Get all price group overrides for a product",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Group prices retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function groupPrices(Request $request, int $id): JsonResponse
    {
        $prices = $this->productService->getGroupPrices($id, $request->user()->business_id);
        return $this->success($prices, 'Group prices retrieved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Update group prices
    |--------------------------------------------------------------------------
    */
    #[OA\Post(
        path: "/v1/procurement/products/{id}/group-prices",
        summary: "Update selling price group prices",
        description: "Save or update price group prices for a product's variations",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "group_prices",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "variation_id",   type: "integer"),
                                new OA\Property(property: "price_group_id", type: "integer"),
                                new OA\Property(property: "price_inc_tax",  type: "number"),
                                new OA\Property(property: "price_type",     type: "string", enum: ["fixed","percentage"]),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Group prices updated successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function updateGroupPrices(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'group_prices'                   => 'required|array',
            'group_prices.*.variation_id'   => 'required|integer|exists:variations,id',
            'group_prices.*.price_group_id' => 'required|integer|exists:selling_price_groups,id',
            'group_prices.*.price_inc_tax'  => 'required|numeric|min:0',
            'group_prices.*.price_type'     => 'nullable|string|in:fixed,percentage',
        ]);

        try {
            $this->productService->updateGroupPrices($id, $request->user()->business_id, $request->input('group_prices'));
            return $this->success(null, 'Group prices updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update group prices: ' . $e->getMessage(), 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk deactivate
    |--------------------------------------------------------------------------
    */
    #[OA\Post(
        path: "/v1/procurement/products/mass-deactivate",
        summary: "Bulk deactivate products",
        description: "Deactivate multiple products at once",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "ids", type: "array", items: new OA\Items(type: "integer")),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Products deactivated"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function massDeactivate(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $count = $this->productService->massDeactivate($request->input('ids'), $request->user()->business_id);
        return $this->success(['affected' => $count], "{$count} products deactivated");
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk delete
    |--------------------------------------------------------------------------
    */
    #[OA\Post(
        path: "/v1/procurement/products/mass-delete",
        summary: "Bulk delete products",
        description: "Delete multiple products at once",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "ids", type: "array", items: new OA\Items(type: "integer")),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Products deleted"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function massDelete(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $count = $this->productService->massDelete($request->input('ids'), $request->user()->business_id);
        return $this->success(['affected' => $count], "{$count} products deleted");
    }

    /*
    |--------------------------------------------------------------------------
    | Activate product
    |--------------------------------------------------------------------------
    */
    #[OA\Patch(
        path: "/v1/procurement/products/{id}/activate",
        summary: "Activate a product",
        description: "Set a product as active (is_inactive = 0)",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Product activated"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function activate(Request $request, int $id): JsonResponse
    {
        $activated = $this->productService->activateProduct($id, $request->user()->business_id);

        if (!$activated) {
            return $this->notFound('Product not found');
        }

        return $this->success(null, 'Product activated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Check SKU uniqueness
    |--------------------------------------------------------------------------
    */
    #[OA\Post(
        path: "/v1/procurement/products/check-sku",
        summary: "Validate SKU uniqueness",
        description: "Check whether a SKU is available within the business",
        tags: ["Procurement - Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "sku",        type: "string"),
                    new OA\Property(property: "product_id", type: "integer", nullable: true, description: "Exclude this product ID when editing"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "SKU check result"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function checkSku(Request $request): JsonResponse
    {
        $request->validate(['sku' => 'required|string']);
        $isUnique = $this->productService->isSkuUnique(
            $request->input('sku'),
            $request->user()->business_id,
            $request->input('product_id')
        );

        return $this->success(['is_unique' => $isUnique], $isUnique ? 'SKU is available' : 'SKU already exists');
    }
}
