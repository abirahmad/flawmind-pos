<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreSellingPriceGroupRequest;
use Modules\Procurement\Http\Resources\SellingPriceGroupResource;
use Modules\Procurement\Services\SellingPriceGroupService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Selling Price Groups",
    description: "Customer price tier management (Wholesale, Retail, VIP, etc.)"
)]
class SellingPriceGroupController extends Controller
{
    use ApiResponse;

    public function __construct(protected SellingPriceGroupService $service) {}

    #[OA\Get(
        path: "/v1/procurement/selling-price-groups",
        summary: "List selling price groups",
        description: "Get paginated list of selling price groups for the business",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Price groups retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $groups = $this->service->getPriceGroups($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(SellingPriceGroupResource::collection($groups), 'Selling price groups retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/selling-price-groups",
        summary: "Create a selling price group",
        description: "Create a new customer price tier (e.g. Wholesale, VIP)",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name",        type: "string", example: "Wholesale"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Price group created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreSellingPriceGroupRequest $request): JsonResponse
    {
        try {
            $data  = array_merge($request->validated(), ['business_id' => $request->user()->business_id]);
            $group = $this->service->createPriceGroup($data);
            return $this->created(new SellingPriceGroupResource($group), 'Selling price group created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create price group: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/selling-price-groups/{id}",
        summary: "Get price group details",
        description: "Get details of a specific selling price group",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Price group retrieved successfully"),
            new OA\Response(response: 404, description: "Price group not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $group = $this->service->getPriceGroup($id, $request->user()->business_id);

        if (!$group) {
            return $this->notFound('Selling price group not found');
        }

        return $this->success(new SellingPriceGroupResource($group), 'Price group retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/selling-price-groups/{id}",
        summary: "Update a selling price group",
        description: "Update an existing selling price group",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Price group updated successfully"),
            new OA\Response(response: 404, description: "Price group not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreSellingPriceGroupRequest $request, int $id): JsonResponse
    {
        $group = $this->service->getPriceGroup($id, $request->user()->business_id);

        if (!$group) {
            return $this->notFound('Selling price group not found');
        }

        try {
            $group = $this->service->updatePriceGroup($group, $request->validated());
            return $this->success(new SellingPriceGroupResource($group), 'Selling price group updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update price group: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/selling-price-groups/{id}",
        summary: "Delete a selling price group",
        description: "Soft-delete a selling price group",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Price group deleted successfully"),
            new OA\Response(response: 404, description: "Price group not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $group = $this->service->getPriceGroup($id, $request->user()->business_id);

        if (!$group) {
            return $this->notFound('Selling price group not found');
        }

        $this->service->deletePriceGroup($group);
        return $this->success(null, 'Selling price group deleted successfully');
    }

    #[OA\Patch(
        path: "/v1/procurement/selling-price-groups/{id}/toggle-active",
        summary: "Toggle active state",
        description: "Toggle the active/inactive state of a selling price group",
        tags: ["Procurement - Selling Price Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Active state toggled"),
            new OA\Response(response: 404, description: "Price group not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        $group = $this->service->getPriceGroup($id, $request->user()->business_id);

        if (!$group) {
            return $this->notFound('Selling price group not found');
        }

        $group = $this->service->toggleActive($group);
        $state = $group->is_active ? 'activated' : 'deactivated';

        return $this->success(new SellingPriceGroupResource($group), "Price group {$state} successfully");
    }
}
