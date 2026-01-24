<?php

namespace Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sales\Http\Requests\StoreSellRequest;
use Modules\Sales\Http\Requests\UpdateSellRequest;
use Modules\Sales\Http\Resources\TransactionResource;
use Modules\Sales\Http\Resources\TransactionCollection;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Services\TransactionService;
use Modules\Sales\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Sales",
    description: "API endpoints for sales transactions"
)]
class SellController extends Controller
{
    use ApiResponse;

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    #[OA\Get(
        path: "/v1/sales/sells",
        summary: "List all sales",
        description: "Get paginated list of all sales transactions",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
            new OA\Parameter(name: "contact_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "location_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "payment_status", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["paid", "due", "partial"])),
            new OA\Parameter(name: "start_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "end_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sales list retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $filters = [
            'business_id' => $user->business_id,
            'location_id' => $request->input('location_id'),
            'contact_id' => $request->input('contact_id'),
            'payment_status' => $request->input('payment_status'),
            'status' => $request->input('status', Transaction::STATUS_FINAL),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by', 'transaction_date'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $perPage = $request->input('per_page', 15);
        $transactions = $this->transactionService->getTransactions($filters, $perPage);

        return $this->success(
            new TransactionCollection($transactions),
            'Sales retrieved successfully'
        );
    }

    #[OA\Post(
        path: "/v1/sales/sells",
        summary: "Create a new sale",
        description: "Create a new sales transaction",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["contact_id", "sell_lines"],
                properties: [
                    new OA\Property(property: "contact_id", type: "integer"),
                    new OA\Property(property: "location_id", type: "integer"),
                    new OA\Property(property: "status", type: "string", enum: ["draft", "final"]),
                    new OA\Property(property: "transaction_date", type: "string", format: "date-time"),
                    new OA\Property(property: "discount_type", type: "string", enum: ["fixed", "percentage"]),
                    new OA\Property(property: "discount_amount", type: "number"),
                    new OA\Property(property: "tax_id", type: "integer"),
                    new OA\Property(property: "shipping_charges", type: "number"),
                    new OA\Property(
                        property: "sell_lines",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "product_id", type: "integer"),
                                new OA\Property(property: "variation_id", type: "integer"),
                                new OA\Property(property: "quantity", type: "number"),
                                new OA\Property(property: "unit_price", type: "number"),
                                new OA\Property(property: "line_discount_type", type: "string"),
                                new OA\Property(property: "line_discount_amount", type: "number"),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: "payments",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "amount", type: "number"),
                                new OA\Property(property: "method", type: "string"),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Sale created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreSellRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            $data = array_merge($request->validated(), [
                'business_id' => $user->business_id,
                'created_by' => $user->id,
            ]);

            $transaction = $this->transactionService->createSell($data);

            return $this->created(
                new TransactionResource($transaction),
                'Sale created successfully'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create sale: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/sells/{id}",
        summary: "Get sale details",
        description: "Get details of a specific sale",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sale details retrieved successfully"),
            new OA\Response(response: 404, description: "Sale not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $transaction = $this->transactionService->getTransaction($id, $user->business_id);

        if (!$transaction) {
            return $this->notFound('Sale not found');
        }

        return $this->success(
            new TransactionResource($transaction),
            'Sale retrieved successfully'
        );
    }

    #[OA\Put(
        path: "/v1/sales/sells/{id}",
        summary: "Update a sale",
        description: "Update an existing sales transaction",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sale updated successfully"),
            new OA\Response(response: 404, description: "Sale not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(UpdateSellRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $transaction = $this->transactionService->getTransaction($id, $user->business_id);

            if (!$transaction) {
                return $this->notFound('Sale not found');
            }

            // Check if has return
            if ($transaction->returnTransaction) {
                return $this->error('Cannot update sale with existing return', 422);
            }

            $transaction = $this->transactionService->updateSell($transaction, $request->validated());

            return $this->success(
                new TransactionResource($transaction),
                'Sale updated successfully'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update sale: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/sales/sells/{id}",
        summary: "Delete a sale",
        description: "Delete a sales transaction",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sale deleted successfully"),
            new OA\Response(response: 404, description: "Sale not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $transaction = $this->transactionService->getTransaction($id, $user->business_id);

            if (!$transaction) {
                return $this->notFound('Sale not found');
            }

            // Check if has return
            if ($transaction->returnTransaction) {
                return $this->error('Cannot delete sale with existing return', 422);
            }

            $this->transactionService->deleteSell($transaction);

            return $this->success(null, 'Sale deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete sale: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/sells/drafts",
        summary: "Get draft sales",
        description: "Get list of draft sales",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Draft sales retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function drafts(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->input('per_page', 15);

        $drafts = $this->transactionService->getDrafts($user->business_id, $perPage);

        return $this->success(
            new TransactionCollection($drafts),
            'Draft sales retrieved successfully'
        );
    }

    #[OA\Get(
        path: "/v1/sales/sells/quotations",
        summary: "Get quotations",
        description: "Get list of quotations",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Quotations retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function quotations(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->input('per_page', 15);

        $quotations = $this->transactionService->getQuotations($user->business_id, $perPage);

        return $this->success(
            new TransactionCollection($quotations),
            'Quotations retrieved successfully'
        );
    }

    #[OA\Post(
        path: "/v1/sales/sells/{id}/convert-to-invoice",
        summary: "Convert to invoice",
        description: "Convert a draft or quotation to final invoice",
        tags: ["Sales"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Converted to invoice successfully"),
            new OA\Response(response: 404, description: "Sale not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function convertToInvoice(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            $transaction = $this->transactionService->getTransaction($id, $user->business_id);

            if (!$transaction) {
                return $this->notFound('Sale not found');
            }

            $transaction = $this->transactionService->convertToInvoice($transaction, $user->id);

            return $this->success(
                new TransactionResource($transaction),
                'Converted to invoice successfully'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to convert: ' . $e->getMessage(), 500);
        }
    }
}
