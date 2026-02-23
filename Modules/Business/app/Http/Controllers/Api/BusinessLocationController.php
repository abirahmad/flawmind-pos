<?php

namespace Modules\Business\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Business\Http\Requests\StoreBusinessLocationRequest;
use Modules\Business\Http\Requests\UpdateBusinessLocationRequest;
use Modules\Business\Http\Resources\BusinessLocationResource;
use Modules\Business\Services\BusinessLocationService;
use Modules\Business\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Business - Locations",
    description: "Business location (branch/outlet) management"
)]
class BusinessLocationController extends Controller
{
    use ApiResponse;

    public function __construct(protected BusinessLocationService $service) {}

    #[OA\Get(
        path: "/v1/business/locations",
        summary: "List business locations",
        description: "Get paginated list of all locations (branches/outlets) for the business",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Locations retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean"),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data",    type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id",          type: "integer"),
                                new OA\Property(property: "location_id", type: "string"),
                                new OA\Property(property: "name",        type: "string"),
                                new OA\Property(property: "city",        type: "string", nullable: true),
                                new OA\Property(property: "mobile",      type: "string", nullable: true),
                                new OA\Property(property: "is_active",   type: "boolean"),
                            ]
                        )),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $locations = $this->service->getLocations(
            $request->user()->business_id,
            $request->input('per_page', 15)
        );

        return $this->success(BusinessLocationResource::collection($locations), 'Locations retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/business/locations",
        summary: "Create a business location",
        description: "Create a new branch, outlet, or warehouse location for the business",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["location_id", "name"],
                properties: [
                    new OA\Property(property: "location_id",  type: "string",  example: "HQ",    description: "Unique location identifier within business"),
                    new OA\Property(property: "name",         type: "string",  example: "Head Office"),
                    new OA\Property(property: "landmark",     type: "string",  nullable: true),
                    new OA\Property(property: "country",      type: "string",  nullable: true, example: "Bangladesh"),
                    new OA\Property(property: "state",        type: "string",  nullable: true),
                    new OA\Property(property: "city",         type: "string",  nullable: true, example: "Dhaka"),
                    new OA\Property(property: "zip_code",     type: "string",  nullable: true),
                    new OA\Property(property: "mobile",       type: "string",  nullable: true, example: "01700000000"),
                    new OA\Property(property: "alternate_number", type: "string", nullable: true),
                    new OA\Property(property: "email",        type: "string",  nullable: true, format: "email"),
                    new OA\Property(property: "website",      type: "string",  nullable: true),
                    new OA\Property(property: "print_receipt_on_invoice", type: "boolean", example: false),
                    new OA\Property(property: "receipt_printer_type", type: "string", enum: ["browser", "printer"], nullable: true),
                    new OA\Property(property: "selling_price_group_id", type: "integer", nullable: true),
                    new OA\Property(property: "custom_field1", type: "string", nullable: true),
                    new OA\Property(property: "custom_field2", type: "string", nullable: true),
                    new OA\Property(property: "custom_field3", type: "string", nullable: true),
                    new OA\Property(property: "custom_field4", type: "string", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Location created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreBusinessLocationRequest $request): JsonResponse
    {
        try {
            $data     = array_merge($request->validated(), ['business_id' => $request->user()->business_id]);
            $location = $this->service->createLocation($data);
            return $this->created(new BusinessLocationResource($location), 'Location created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create location: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/business/locations/{id}",
        summary: "Get location details",
        description: "Get details of a specific business location",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Location retrieved successfully"),
            new OA\Response(response: 404, description: "Location not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $location = $this->service->getLocation($id, $request->user()->business_id);

        if (!$location) {
            return $this->notFound('Location not found');
        }

        return $this->success(new BusinessLocationResource($location), 'Location retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/business/locations/{id}",
        summary: "Update a business location",
        description: "Update an existing business location. All fields are optional.",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name",     type: "string"),
                    new OA\Property(property: "landmark", type: "string", nullable: true),
                    new OA\Property(property: "city",     type: "string", nullable: true),
                    new OA\Property(property: "state",    type: "string", nullable: true),
                    new OA\Property(property: "country",  type: "string", nullable: true),
                    new OA\Property(property: "zip_code", type: "string", nullable: true),
                    new OA\Property(property: "mobile",   type: "string", nullable: true),
                    new OA\Property(property: "email",    type: "string", nullable: true, format: "email"),
                    new OA\Property(property: "website",  type: "string", nullable: true),
                    new OA\Property(property: "custom_field1", type: "string", nullable: true),
                    new OA\Property(property: "custom_field2", type: "string", nullable: true),
                    new OA\Property(property: "custom_field3", type: "string", nullable: true),
                    new OA\Property(property: "custom_field4", type: "string", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Location updated successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 404, description: "Location not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(UpdateBusinessLocationRequest $request, int $id): JsonResponse
    {
        $location = $this->service->getLocation($id, $request->user()->business_id);

        if (!$location) {
            return $this->notFound('Location not found');
        }

        try {
            $location = $this->service->updateLocation($location, $request->validated());
            return $this->success(new BusinessLocationResource($location), 'Location updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update location: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/business/locations/{id}",
        summary: "Delete a business location",
        description: "Soft-delete a business location",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Location deleted successfully"),
            new OA\Response(response: 404, description: "Location not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $location = $this->service->getLocation($id, $request->user()->business_id);

        if (!$location) {
            return $this->notFound('Location not found');
        }

        $this->service->deleteLocation($location);
        return $this->success(null, 'Location deleted successfully');
    }

    #[OA\Patch(
        path: "/v1/business/locations/{id}/toggle-active",
        summary: "Toggle location active state",
        description: "Activate or deactivate a business location",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Location active state toggled"),
            new OA\Response(response: 404, description: "Location not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function toggleActive(Request $request, int $id): JsonResponse
    {
        $location = $this->service->getLocation($id, $request->user()->business_id);

        if (!$location) {
            return $this->notFound('Location not found');
        }

        $location = $this->service->toggleActive($location);
        $state    = $location->is_active ? 'activated' : 'deactivated';

        return $this->success(new BusinessLocationResource($location), "Location {$state} successfully");
    }

    #[OA\Get(
        path: "/v1/business/locations/check-location-id",
        summary: "Check if location ID is available",
        description: "Check whether a given location_id is available (unique) for this business",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "location_id", in: "query", required: true,  schema: new OA\Schema(type: "string"),  description: "The location ID to check"),
            new OA\Parameter(name: "exclude_id",  in: "query", required: false, schema: new OA\Schema(type: "integer"), description: "Exclude this location's own ID (for edit scenarios)"),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Check result",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success",   type: "boolean"),
                        new OA\Property(property: "message",   type: "string"),
                        new OA\Property(property: "data",      type: "object",
                            properties: [
                                new OA\Property(property: "available", type: "boolean"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function checkLocationId(Request $request): JsonResponse
    {
        $request->validate([
            'location_id' => 'required|string|max:50',
            'exclude_id'  => 'sometimes|nullable|integer',
        ]);

        $available = $this->service->isLocationIdUnique(
            $request->user()->business_id,
            $request->input('location_id'),
            $request->input('exclude_id')
        );

        return $this->success(
            ['available' => $available],
            $available ? 'Location ID is available' : 'Location ID is already taken'
        );
    }

    #[OA\Get(
        path: "/v1/business/locations/active",
        summary: "List active locations",
        description: "Get all active locations for the business (no pagination, suitable for dropdowns)",
        tags: ["Business - Locations"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Active locations retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function activeLocations(Request $request): JsonResponse
    {
        $locations = $this->service->getActiveLocations($request->user()->business_id);
        return $this->success(BusinessLocationResource::collection($locations), 'Active locations retrieved successfully');
    }
}
