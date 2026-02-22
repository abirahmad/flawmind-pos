<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreUnitRequest;
use Modules\Procurement\Http\Resources\UnitResource;
use Modules\Procurement\Services\UnitService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Units",
    description: "Unit of measurement management API endpoints"
)]
class UnitController extends Controller
{
    use ApiResponse;

    public function __construct(protected UnitService $service) {}

    #[OA\Get(
        path: "/v1/procurement/units",
        summary: "List units",
        description: "Get paginated list of units of measurement",
        tags: ["Procurement - Units"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Units retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $units = $this->service->getUnits($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(UnitResource::collection($units), 'Units retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/units",
        summary: "Create a unit",
        description: "Create a new unit of measurement. Optionally link to a base unit for sub-unit conversion.",
        tags: ["Procurement - Units"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["actual_name", "short_name"],
                properties: [
                    new OA\Property(property: "actual_name",          type: "string",  example: "Kilogram"),
                    new OA\Property(property: "short_name",           type: "string",  example: "kg"),
                    new OA\Property(property: "allow_decimal",        type: "boolean", example: true),
                    new OA\Property(property: "base_unit_id",         type: "integer", nullable: true, description: "Parent unit ID for sub-unit"),
                    new OA\Property(property: "base_unit_multiplier", type: "number",  nullable: true, description: "Conversion factor (e.g. 0.001 for gram→kg)"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Unit created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreUnitRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by'  => $user->id,
            ]);
            $unit = $this->service->createUnit($data);
            return $this->created(new UnitResource($unit), 'Unit created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create unit: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/units/{id}",
        summary: "Get unit details",
        description: "Get details of a specific unit including sub-units",
        tags: ["Procurement - Units"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Unit retrieved successfully"),
            new OA\Response(response: 404, description: "Unit not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $unit = $this->service->getUnit($id, $request->user()->business_id);

        if (!$unit) {
            return $this->notFound('Unit not found');
        }

        return $this->success(new UnitResource($unit), 'Unit retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/units/{id}",
        summary: "Update a unit",
        description: "Update an existing unit of measurement",
        tags: ["Procurement - Units"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Unit updated successfully"),
            new OA\Response(response: 404, description: "Unit not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreUnitRequest $request, int $id): JsonResponse
    {
        $unit = $this->service->getUnit($id, $request->user()->business_id);

        if (!$unit) {
            return $this->notFound('Unit not found');
        }

        try {
            $unit = $this->service->updateUnit($unit, $request->validated());
            return $this->success(new UnitResource($unit), 'Unit updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update unit: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/units/{id}",
        summary: "Delete a unit",
        description: "Soft-delete a unit of measurement",
        tags: ["Procurement - Units"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Unit deleted successfully"),
            new OA\Response(response: 404, description: "Unit not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $unit = $this->service->getUnit($id, $request->user()->business_id);

        if (!$unit) {
            return $this->notFound('Unit not found');
        }

        $this->service->deleteUnit($unit);
        return $this->success(null, 'Unit deleted successfully');
    }
}
