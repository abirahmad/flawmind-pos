<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreBrandRequest;
use Modules\Procurement\Http\Resources\BrandResource;
use Modules\Procurement\Services\BrandService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Brands",
    description: "Brand management API endpoints"
)]
class BrandController extends Controller
{
    use ApiResponse;

    public function __construct(protected BrandService $service) {}

    #[OA\Get(
        path: "/v1/procurement/brands",
        summary: "List brands",
        description: "Get paginated list of brands for the business",
        tags: ["Procurement - Brands"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Brands retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $brands = $this->service->getBrands($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(BrandResource::collection($brands), 'Brands retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/brands",
        summary: "Create a brand",
        description: "Create a new product brand",
        tags: ["Procurement - Brands"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name",        type: "string",  example: "BrandX"),
                    new OA\Property(property: "description", type: "string",  nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Brand created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreBrandRequest $request): JsonResponse
    {
        try {
            $user  = $request->user();
            $data  = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by'  => $user->id,
            ]);
            $brand = $this->service->createBrand($data);
            return $this->created(new BrandResource($brand), 'Brand created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create brand: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/brands/{id}",
        summary: "Get brand details",
        description: "Get details of a specific brand",
        tags: ["Procurement - Brands"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Brand retrieved successfully"),
            new OA\Response(response: 404, description: "Brand not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $brand = $this->service->getBrand($id, $request->user()->business_id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        return $this->success(new BrandResource($brand), 'Brand retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/brands/{id}",
        summary: "Update a brand",
        description: "Update an existing brand",
        tags: ["Procurement - Brands"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name",        type: "string"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Brand updated successfully"),
            new OA\Response(response: 404, description: "Brand not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreBrandRequest $request, int $id): JsonResponse
    {
        $brand = $this->service->getBrand($id, $request->user()->business_id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        try {
            $brand = $this->service->updateBrand($brand, $request->validated());
            return $this->success(new BrandResource($brand), 'Brand updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update brand: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/brands/{id}",
        summary: "Delete a brand",
        description: "Soft-delete a brand",
        tags: ["Procurement - Brands"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Brand deleted successfully"),
            new OA\Response(response: 404, description: "Brand not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $brand = $this->service->getBrand($id, $request->user()->business_id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        $this->service->deleteBrand($brand);
        return $this->success(null, 'Brand deleted successfully');
    }
}
