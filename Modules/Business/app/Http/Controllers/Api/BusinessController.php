<?php

namespace Modules\Business\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Business\Http\Requests\RegisterBusinessRequest;
use Modules\Business\Http\Requests\UpdateBusinessRequest;
use Modules\Business\Http\Resources\BusinessLocationResource;
use Modules\Business\Http\Resources\BusinessResource;
use Modules\Business\Services\BusinessRegistrationService;
use Modules\Business\Services\BusinessService;
use Modules\Business\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Business - Settings",
    description: "Business profile and settings management"
)]
class BusinessController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BusinessService $service,
        protected BusinessRegistrationService $registrationService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Registration (public — no auth required)
    // ─────────────────────────────────────────────────────────────────────────

    #[OA\Post(
        path: "/v1/business/register",
        summary: "Register a new business",
        description: "Create a new business with an owner account and a first location. Returns a bearer token for immediate use. This endpoint is public and does not require authentication.",
        tags: ["Business - Settings"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "currency_id", "time_zone", "fy_start_month", "accounting_method", "first_name", "username", "password", "location_name"],
                properties: [
                    new OA\Property(property: "name",              type: "string",  example: "My Business",    description: "Business name"),
                    new OA\Property(property: "currency_id",       type: "integer", example: 1,                description: "Currency ID from currencies table"),
                    new OA\Property(property: "time_zone",         type: "string",  example: "Asia/Dhaka",     description: "PHP timezone string"),
                    new OA\Property(property: "fy_start_month",    type: "integer", example: 1,                description: "Fiscal year start month (1-12)"),
                    new OA\Property(property: "accounting_method", type: "string",  enum: ["fifo", "lifo", "avco"], example: "fifo"),
                    new OA\Property(property: "start_date",        type: "string",  format: "date",            nullable: true, example: "2024-01-01"),
                    new OA\Property(property: "tax_label_1",       type: "string",  nullable: true,            example: "VAT"),
                    new OA\Property(property: "tax_number_1",      type: "string",  nullable: true),
                    new OA\Property(property: "tax_label_2",       type: "string",  nullable: true),
                    new OA\Property(property: "tax_number_2",      type: "string",  nullable: true),
                    new OA\Property(property: "surname",           type: "string",  nullable: true,            example: "Mr."),
                    new OA\Property(property: "first_name",        type: "string",  example: "John",           description: "Owner first name"),
                    new OA\Property(property: "last_name",         type: "string",  nullable: true,            example: "Doe"),
                    new OA\Property(property: "username",          type: "string",  example: "johndoe",        description: "Owner username (min 4 chars, unique)"),
                    new OA\Property(property: "email",             type: "string",  format: "email",           nullable: true, example: "john@example.com"),
                    new OA\Property(property: "password",          type: "string",  format: "password",        example: "secret123"),
                    new OA\Property(property: "language",          type: "string",  nullable: true,            example: "en"),
                    new OA\Property(property: "location_name",     type: "string",  example: "Head Office",    description: "First location name"),
                    new OA\Property(property: "landmark",          type: "string",  nullable: true,            example: "123 Main St"),
                    new OA\Property(property: "country",           type: "string",  nullable: true,            example: "Bangladesh"),
                    new OA\Property(property: "state",             type: "string",  nullable: true),
                    new OA\Property(property: "city",              type: "string",  nullable: true,            example: "Dhaka"),
                    new OA\Property(property: "zip_code",          type: "string",  nullable: true,            example: "1000"),
                    new OA\Property(property: "mobile",            type: "string",  nullable: true,            example: "01700000000"),
                    new OA\Property(property: "alternate_number",  type: "string",  nullable: true),
                    new OA\Property(property: "website",           type: "string",  nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Business registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success",     type: "boolean", example: true),
                        new OA\Property(property: "message",     type: "string",  example: "Business registered successfully"),
                        new OA\Property(property: "data", type: "object",
                            properties: [
                                new OA\Property(property: "access_token", type: "string",  description: "Passport bearer token — use this for all subsequent requests"),
                                new OA\Property(property: "token_type",   type: "string",  example: "Bearer"),
                                new OA\Property(property: "user",         type: "object"),
                                new OA\Property(property: "business",     type: "object"),
                                new OA\Property(property: "location",     type: "object"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 500, description: "Server error"),
        ]
    )]
    public function register(RegisterBusinessRequest $request): JsonResponse
    {
        try {
            $result = $this->registrationService->register($request->validated());

            return $this->created([
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type'],
                'user'         => $result['user'],
                'business'     => new BusinessResource($result['business']),
                'location'     => new BusinessLocationResource($result['location']),
            ], 'Business registered successfully');
        } catch (\Exception $e) {
            return $this->error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/business/settings",
        summary: "Get business settings",
        description: "Retrieve the full settings and configuration of the authenticated user's business",
        tags: ["Business - Settings"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Business settings retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data",    type: "object",
                            properties: [
                                new OA\Property(property: "id",              type: "integer"),
                                new OA\Property(property: "name",            type: "string"),
                                new OA\Property(property: "currency_id",     type: "integer"),
                                new OA\Property(property: "time_zone",       type: "string"),
                                new OA\Property(property: "fy_start_month",  type: "integer"),
                                new OA\Property(property: "accounting_method", type: "string"),
                                new OA\Property(property: "logo",            type: "string", nullable: true),
                                new OA\Property(property: "enabled_modules", type: "array", items: new OA\Items(type: "string")),
                                new OA\Property(property: "pos_settings",    type: "object"),
                                new OA\Property(property: "is_active",       type: "boolean"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Business not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        $business = $this->service->getBusinessSettings($request->user()->business_id);

        if (!$business) {
            return $this->notFound('Business not found');
        }

        return $this->success(new BusinessResource($business), 'Business settings retrieved successfully');
    }

    #[OA\Put(
        path: "/v1/business/settings",
        summary: "Update business settings",
        description: "Update the settings and configuration for the authenticated user's business. All fields are optional — only send fields you want to update.",
        tags: ["Business - Settings"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name",              type: "string",  example: "My Business"),
                    new OA\Property(property: "currency_id",       type: "integer", example: 1),
                    new OA\Property(property: "time_zone",         type: "string",  example: "Asia/Dhaka"),
                    new OA\Property(property: "fy_start_month",    type: "integer", example: 1, description: "Fiscal year start month (1-12)"),
                    new OA\Property(property: "accounting_method", type: "string",  enum: ["fifo", "lifo", "avco"]),
                    new OA\Property(property: "default_profit_percent",  type: "number",  example: 25),
                    new OA\Property(property: "default_sales_discount",  type: "number",  example: 0),
                    new OA\Property(property: "sell_price_tax",    type: "string",  enum: ["includes", "excludes"]),
                    new OA\Property(property: "sku_prefix",        type: "string",  example: "PRD"),
                    new OA\Property(property: "logo",              type: "string",  format: "binary", nullable: true),
                    new OA\Property(property: "enable_product_expiry", type: "boolean"),
                    new OA\Property(property: "enable_brand",      type: "boolean"),
                    new OA\Property(property: "enable_category",   type: "boolean"),
                    new OA\Property(property: "enable_sub_category", type: "boolean"),
                    new OA\Property(property: "enable_price_tax",  type: "boolean"),
                    new OA\Property(property: "enable_lot_number", type: "boolean"),
                    new OA\Property(property: "enable_racks",      type: "boolean"),
                    new OA\Property(property: "enable_rp",         type: "boolean"),
                    new OA\Property(property: "enabled_modules",   type: "array",   items: new OA\Items(type: "string")),
                    new OA\Property(property: "pos_settings",      type: "object"),
                    new OA\Property(property: "email_settings",    type: "object"),
                    new OA\Property(property: "sms_settings",      type: "object"),
                    new OA\Property(property: "common_settings",   type: "object"),
                    new OA\Property(property: "ref_no_prefixes",   type: "object"),
                    new OA\Property(property: "date_format",       type: "string",  example: "d/m/Y"),
                    new OA\Property(property: "time_format",       type: "string",  enum: ["12", "24"]),
                    new OA\Property(property: "currency_precision", type: "integer", example: 2),
                    new OA\Property(property: "quantity_precision", type: "integer", example: 2),
                    new OA\Property(property: "theme_color",       type: "string",  example: "blue"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Business settings updated successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 404, description: "Business not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(UpdateBusinessRequest $request): JsonResponse
    {
        $business = $this->service->getBusinessById($request->user()->business_id);

        if (!$business) {
            return $this->notFound('Business not found');
        }

        try {
            $business = $this->service->updateBusiness($business, $request->validated());
            return $this->success(new BusinessResource($business), 'Business settings updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update business settings: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Patch(
        path: "/v1/business/toggle-active",
        summary: "Toggle business active state",
        description: "Toggle the active/inactive state of the authenticated user's business",
        tags: ["Business - Settings"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Business active state toggled"),
            new OA\Response(response: 404, description: "Business not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function toggleActive(Request $request): JsonResponse
    {
        $business = $this->service->getBusinessById($request->user()->business_id);

        if (!$business) {
            return $this->notFound('Business not found');
        }

        $business = $this->service->toggleActive($business);
        $state    = $business->is_active ? 'activated' : 'deactivated';

        return $this->success(new BusinessResource($business), "Business {$state} successfully");
    }
}
