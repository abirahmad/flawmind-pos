<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreCategoryRequest;
use Modules\Procurement\Http\Resources\CategoryResource;
use Modules\Procurement\Services\CategoryService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Categories",
    description: "Product category and sub-category management"
)]
class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(protected CategoryService $service) {}

    #[OA\Get(
        path: "/v1/procurement/categories",
        summary: "List product categories",
        description: "Get paginated list of main product categories with their sub-categories",
        tags: ["Procurement - Categories"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Categories retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $categories = $this->service->getCategories($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(CategoryResource::collection($categories), 'Categories retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/categories",
        summary: "Create a category",
        description: "Create a product category or sub-category. Set parent_id=0 or omit for a main category.",
        tags: ["Procurement - Categories"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name",          type: "string",  example: "Electronics"),
                    new OA\Property(property: "short_code",    type: "string",  nullable: true),
                    new OA\Property(property: "description",   type: "string",  nullable: true),
                    new OA\Property(property: "parent_id",     type: "integer", example: 0, description: "0 = main category, else sub-category of that parent"),
                    new OA\Property(property: "category_type", type: "string",  enum: ["product","expense"], example: "product"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Category created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by'  => $user->id,
            ]);
            $category = $this->service->createCategory($data);
            return $this->created(new CategoryResource($category), 'Category created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/categories/{id}",
        summary: "Get category details",
        description: "Get details of a specific category with its sub-categories",
        tags: ["Procurement - Categories"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Category retrieved successfully"),
            new OA\Response(response: 404, description: "Category not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $category = $this->service->getCategory($id, $request->user()->business_id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->success(new CategoryResource($category), 'Category retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/categories/{id}",
        summary: "Update a category",
        description: "Update an existing product category",
        tags: ["Procurement - Categories"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Category updated successfully"),
            new OA\Response(response: 404, description: "Category not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->service->getCategory($id, $request->user()->business_id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        try {
            $category = $this->service->updateCategory($category, $request->validated());
            return $this->success(new CategoryResource($category), 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/categories/{id}",
        summary: "Delete a category",
        description: "Soft-delete a product category",
        tags: ["Procurement - Categories"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Category deleted successfully"),
            new OA\Response(response: 404, description: "Category not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = $this->service->getCategory($id, $request->user()->business_id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $this->service->deleteCategory($category);
        return $this->success(null, 'Category deleted successfully');
    }
}
