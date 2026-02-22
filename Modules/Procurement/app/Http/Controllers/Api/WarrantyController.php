<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreWarrantyRequest;
use Modules\Procurement\Http\Resources\WarrantyResource;
use Modules\Procurement\Services\WarrantyService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Warranties",
    description: "Warranty management API endpoints"
)]
class WarrantyController extends Controller
{
    use ApiResponse;

    public function __construct(protected WarrantyService $service) {}

    #[OA\Get(
        path: "/v1/procurement/warranties",
        summary: "List warranties",
        description: "Get paginated list of warranties for the business",
        tags: ["Procurement - Warranties"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Warranties retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $warranties = $this->service->getWarranties($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(WarrantyResource::collection($warranties), 'Warranties retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/warranties",
        summary: "Create a warranty",
        description: "Create a new warranty definition",
        tags: ["Procurement - Warranties"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "duration", "duration_type"],
                properties: [
                    new OA\Property(property: "name",          type: "string",  example: "Standard Warranty"),
                    new OA\Property(property: "description",   type: "string",  nullable: true),
                    new OA\Property(property: "duration",      type: "integer", example: 12),
                    new OA\Property(property: "duration_type", type: "string",  enum: ["days","months","years"], example: "months"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Warranty created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreWarrantyRequest $request): JsonResponse
    {
        try {
            $data     = array_merge($request->validated(), ['business_id' => $request->user()->business_id]);
            $warranty = $this->service->createWarranty($data);
            return $this->created(new WarrantyResource($warranty), 'Warranty created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create warranty: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/warranties/{id}",
        summary: "Get warranty details",
        description: "Get details of a specific warranty",
        tags: ["Procurement - Warranties"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Warranty retrieved successfully"),
            new OA\Response(response: 404, description: "Warranty not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $warranty = $this->service->getWarranty($id, $request->user()->business_id);

        if (!$warranty) {
            return $this->notFound('Warranty not found');
        }

        return $this->success(new WarrantyResource($warranty), 'Warranty retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/warranties/{id}",
        summary: "Update a warranty",
        description: "Update an existing warranty",
        tags: ["Procurement - Warranties"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Warranty updated successfully"),
            new OA\Response(response: 404, description: "Warranty not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreWarrantyRequest $request, int $id): JsonResponse
    {
        $warranty = $this->service->getWarranty($id, $request->user()->business_id);

        if (!$warranty) {
            return $this->notFound('Warranty not found');
        }

        try {
            $warranty = $this->service->updateWarranty($warranty, $request->validated());
            return $this->success(new WarrantyResource($warranty), 'Warranty updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update warranty: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/warranties/{id}",
        summary: "Delete a warranty",
        description: "Delete a warranty definition",
        tags: ["Procurement - Warranties"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Warranty deleted successfully"),
            new OA\Response(response: 404, description: "Warranty not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $warranty = $this->service->getWarranty($id, $request->user()->business_id);

        if (!$warranty) {
            return $this->notFound('Warranty not found');
        }

        $this->service->deleteWarranty($warranty);
        return $this->success(null, 'Warranty deleted successfully');
    }
}
