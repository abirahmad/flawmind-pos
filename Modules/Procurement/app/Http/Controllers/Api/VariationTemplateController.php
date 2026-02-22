<?php

namespace Modules\Procurement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Procurement\Http\Requests\StoreVariationTemplateRequest;
use Modules\Procurement\Http\Resources\VariationTemplateResource;
use Modules\Procurement\Services\VariationTemplateService;
use Modules\Procurement\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Procurement - Variation Templates",
    description: "Reusable variation template management (e.g. Color, Size)"
)]
class VariationTemplateController extends Controller
{
    use ApiResponse;

    public function __construct(protected VariationTemplateService $service) {}

    #[OA\Get(
        path: "/v1/procurement/variation-templates",
        summary: "List variation templates",
        description: "Get paginated list of variation templates with their values",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page",     in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Templates retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $templates = $this->service->getTemplates($request->user()->business_id, $request->input('per_page', 15));
        return $this->success(VariationTemplateResource::collection($templates), 'Variation templates retrieved successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/variation-templates",
        summary: "Create a variation template",
        description: "Create a new reusable variation template with values",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Color"),
                    new OA\Property(
                        property: "values",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "Red"),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Template created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreVariationTemplateRequest $request): JsonResponse
    {
        try {
            $user     = $request->user();
            $data     = array_merge($request->validated(), ['business_id' => $user->business_id]);
            $template = $this->service->createTemplate($data);
            return $this->created(new VariationTemplateResource($template), 'Variation template created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create template: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/procurement/variation-templates/{id}",
        summary: "Get variation template",
        description: "Get a specific variation template with all its values",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Template retrieved successfully"),
            new OA\Response(response: 404, description: "Template not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $template = $this->service->getTemplate($id, $request->user()->business_id);

        if (!$template) {
            return $this->notFound('Variation template not found');
        }

        return $this->success(new VariationTemplateResource($template), 'Variation template retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/procurement/variation-templates/{id}",
        summary: "Update a variation template",
        description: "Update a variation template and sync its values",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(
                        property: "values",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id",   type: "integer", nullable: true, description: "Existing value ID (omit for new)"),
                                new OA\Property(property: "name", type: "string"),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Template updated successfully"),
            new OA\Response(response: 404, description: "Template not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:191',
            'values'        => 'nullable|array',
            'values.*.id'   => 'nullable|integer',
            'values.*.name' => 'required|string|max:191',
        ]);

        $template = $this->service->getTemplate($id, $request->user()->business_id);

        if (!$template) {
            return $this->notFound('Variation template not found');
        }

        try {
            $template = $this->service->updateTemplate($template, $request->all());
            return $this->success(new VariationTemplateResource($template), 'Variation template updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update template: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/procurement/variation-templates/{id}",
        summary: "Delete a variation template",
        description: "Delete a variation template and all its values",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Template deleted successfully"),
            new OA\Response(response: 404, description: "Template not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $template = $this->service->getTemplate($id, $request->user()->business_id);

        if (!$template) {
            return $this->notFound('Variation template not found');
        }

        $this->service->deleteTemplate($template);
        return $this->success(null, 'Variation template deleted successfully');
    }

    #[OA\Post(
        path: "/v1/procurement/variation-templates/{id}/values",
        summary: "Add value to template",
        description: "Add a new value to an existing variation template",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Blue"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Value added"),
            new OA\Response(response: 404, description: "Template not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function addValue(Request $request, int $id): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:191']);

        $template = $this->service->getTemplate($id, $request->user()->business_id);

        if (!$template) {
            return $this->notFound('Variation template not found');
        }

        $value = $this->service->addValue($template, $request->input('name'));
        return $this->created(['id' => $value->id, 'name' => $value->name], 'Value added successfully');
    }

    #[OA\Delete(
        path: "/v1/procurement/variation-templates/{id}/values/{valueId}",
        summary: "Remove value from template",
        description: "Delete a specific value from a variation template",
        tags: ["Procurement - Variation Templates"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id",      in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "valueId", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Value removed"),
            new OA\Response(response: 404, description: "Template not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function removeValue(Request $request, int $id, int $valueId): JsonResponse
    {
        $template = $this->service->getTemplate($id, $request->user()->business_id);

        if (!$template) {
            return $this->notFound('Variation template not found');
        }

        $this->service->removeValue($template, $valueId);
        return $this->success(null, 'Value removed successfully');
    }
}
