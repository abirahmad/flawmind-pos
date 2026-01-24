<?php

namespace Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sales\Http\Requests\StoreContactRequest;
use Modules\Sales\Http\Resources\ContactResource;
use Modules\Sales\Models\Contact;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Contacts",
    description: "API endpoints for customer/supplier management"
)]
class ContactController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: "/v1/sales/contacts",
        summary: "List contacts",
        description: "Get paginated list of contacts",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "type", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["customer", "supplier", "both"])),
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contacts retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->input('per_page', 15);

        $query = Contact::forBusiness($user->business_id)->active();

        if ($request->has('type')) {
            $type = $request->input('type');
            if ($type === 'customer') {
                $query->customers();
            } elseif ($type === 'supplier') {
                $query->suppliers();
            }
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_id', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('name')->paginate($perPage);

        return $this->success(
            ContactResource::collection($contacts)->response()->getData(true),
            'Contacts retrieved successfully'
        );
    }

    #[OA\Post(
        path: "/v1/sales/contacts",
        summary: "Create a contact",
        description: "Create a new customer or supplier",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["type", "name", "mobile"],
                properties: [
                    new OA\Property(property: "type", type: "string", enum: ["customer", "supplier", "both"]),
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "mobile", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "tax_number", type: "string"),
                    new OA\Property(property: "address_line_1", type: "string"),
                    new OA\Property(property: "city", type: "string"),
                    new OA\Property(property: "state", type: "string"),
                    new OA\Property(property: "country", type: "string"),
                    new OA\Property(property: "zip_code", type: "string"),
                    new OA\Property(property: "credit_limit", type: "number"),
                    new OA\Property(property: "pay_term_number", type: "integer"),
                    new OA\Property(property: "pay_term_type", type: "string", enum: ["days", "months"]),
                    new OA\Property(property: "customer_group_id", type: "integer"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Contact created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $data = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by' => $user->id,
                'balance' => 0,
                'total_rp' => 0,
                'total_rp_used' => 0,
                'total_rp_expired' => 0,
                'is_default' => false,
                'is_export' => false,
            ]);

            // Auto generate name from first/last name if not provided
            if (empty($data['name']) && !empty($data['first_name'])) {
                $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            }

            $contact = Contact::create($data);

            return $this->created(
                new ContactResource($contact),
                'Contact created successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to create contact: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/contacts/{id}",
        summary: "Get contact details",
        description: "Get details of a specific contact",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact details retrieved successfully"),
            new OA\Response(response: 404, description: "Contact not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $contact = Contact::forBusiness($user->business_id)->find($id);

        if (!$contact) {
            return $this->notFound('Contact not found');
        }

        return $this->success(
            new ContactResource($contact),
            'Contact retrieved successfully'
        );
    }

    #[OA\Put(
        path: "/v1/sales/contacts/{id}",
        summary: "Update a contact",
        description: "Update an existing contact",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact updated successfully"),
            new OA\Response(response: 404, description: "Contact not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StoreContactRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $contact = Contact::forBusiness($user->business_id)->find($id);

            if (!$contact) {
                return $this->notFound('Contact not found');
            }

            $data = $request->validated();

            // Auto generate name from first/last name if not provided
            if (empty($data['name']) && !empty($data['first_name'])) {
                $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            }

            $contact->update($data);

            return $this->success(
                new ContactResource($contact->fresh()),
                'Contact updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to update contact: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/sales/contacts/{id}",
        summary: "Delete a contact",
        description: "Delete a contact (soft delete)",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact deleted successfully"),
            new OA\Response(response: 404, description: "Contact not found"),
            new OA\Response(response: 422, description: "Cannot delete contact with transactions"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $contact = Contact::forBusiness($user->business_id)->find($id);

            if (!$contact) {
                return $this->notFound('Contact not found');
            }

            // Check if has transactions
            $hasTransactions = Transaction::where('contact_id', $id)->exists();
            if ($hasTransactions) {
                return $this->error('Cannot delete contact with existing transactions', 422);
            }

            $contact->delete();

            return $this->success(null, 'Contact deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete contact: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/contacts/{id}/transactions",
        summary: "Get contact transactions",
        description: "Get all transactions for a contact",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "type", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["sell", "sell_return"])),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact transactions retrieved successfully"),
            new OA\Response(response: 404, description: "Contact not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function transactions(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $contact = Contact::forBusiness($user->business_id)->find($id);

        if (!$contact) {
            return $this->notFound('Contact not found');
        }

        $query = Transaction::forContact($id)
            ->forBusiness($user->business_id)
            ->with(['paymentLines'])
            ->orderBy('transaction_date', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        } else {
            $query->whereIn('type', [Transaction::TYPE_SELL, Transaction::TYPE_SELL_RETURN]);
        }

        $transactions = $query->paginate($request->input('per_page', 15));

        return $this->success([
            'contact' => new ContactResource($contact),
            'transactions' => $transactions,
            'summary' => [
                'total_sales' => Transaction::forContact($id)->sells()->final()->sum('final_total'),
                'total_paid' => Transaction::forContact($id)->sells()->final()->get()->sum('total_paid'),
                'balance_due' => $contact->balance,
                'reward_points' => $contact->available_reward_points,
            ],
        ], 'Contact transactions retrieved successfully');
    }

    #[OA\Get(
        path: "/v1/sales/contacts/types",
        summary: "Get contact types",
        description: "Get available contact types",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Contact types retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function getTypes(): JsonResponse
    {
        return $this->success(
            Contact::contactTypes(),
            'Contact types retrieved successfully'
        );
    }
}
