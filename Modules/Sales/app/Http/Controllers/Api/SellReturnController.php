<?php

namespace Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sales\Http\Requests\StoreSellReturnRequest;
use Modules\Sales\Http\Resources\TransactionResource;
use Modules\Sales\Http\Resources\TransactionCollection;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Services\TransactionService;
use Modules\Sales\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Sales Returns",
    description: "API endpoints for sales returns"
)]
class SellReturnController extends Controller
{
    use ApiResponse;

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    #[OA\Get(
        path: "/v1/sales/sell-returns",
        summary: "List all sell returns",
        description: "Get paginated list of all sell returns",
        tags: ["Sales Returns"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sell returns retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->input('per_page', 15);

        $returns = Transaction::with(['contact:id,name,mobile', 'createdBy:id,first_name,last_name', 'returnParent'])
            ->sellReturns()
            ->forBusiness($user->business_id)
            ->latest('transaction_date')
            ->paginate($perPage);

        return $this->success(
            new TransactionCollection($returns),
            'Sell returns retrieved successfully'
        );
    }

    #[OA\Post(
        path: "/v1/sales/sell-returns",
        summary: "Create a sell return",
        description: "Create a new sell return for a sale",
        tags: ["Sales Returns"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["transaction_id", "return_lines"],
                properties: [
                    new OA\Property(property: "transaction_id", type: "integer", description: "Original sale transaction ID"),
                    new OA\Property(property: "transaction_date", type: "string", format: "date-time"),
                    new OA\Property(property: "additional_notes", type: "string"),
                    new OA\Property(
                        property: "return_lines",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "sell_line_id", type: "integer"),
                                new OA\Property(property: "quantity", type: "number"),
                                new OA\Property(property: "return_note", type: "string"),
                            ]
                        )
                    ),
                    new OA\Property(property: "refund_amount", type: "number"),
                    new OA\Property(property: "refund_method", type: "string", enum: ["cash", "card", "bank_transfer"]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Sell return created successfully"),
            new OA\Response(response: 404, description: "Original sale not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StoreSellReturnRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get original sale
            $originalSell = $this->transactionService->getTransaction(
                $request->input('transaction_id'),
                $user->business_id
            );

            if (!$originalSell) {
                return $this->notFound('Original sale not found');
            }

            $data = array_merge($request->validated(), [
                'created_by' => $user->id,
            ]);

            $return = $this->transactionService->createSellReturn($originalSell, $data);

            return $this->created(
                new TransactionResource($return),
                'Sell return created successfully'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create sell return: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/sell-returns/{id}",
        summary: "Get sell return details",
        description: "Get details of a specific sell return",
        tags: ["Sales Returns"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Sell return details retrieved successfully"),
            new OA\Response(response: 404, description: "Sell return not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $return = Transaction::with([
                'sellLines.product:id,name,sku',
                'sellLines.variation:id,name,sub_sku',
                'paymentLines',
                'contact',
                'returnParent',
            ])
            ->sellReturns()
            ->forBusiness($user->business_id)
            ->find($id);

        if (!$return) {
            return $this->notFound('Sell return not found');
        }

        return $this->success(
            new TransactionResource($return),
            'Sell return retrieved successfully'
        );
    }

    #[OA\Get(
        path: "/v1/sales/sell-returns/validate/{invoice_no}",
        summary: "Validate invoice for return",
        description: "Check if an invoice can be returned",
        tags: ["Sales Returns"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "invoice_no", in: "path", required: true, schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Invoice validation result"),
            new OA\Response(response: 404, description: "Invoice not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function validateInvoice(Request $request, string $invoiceNo): JsonResponse
    {
        $user = $request->user();

        $transaction = Transaction::with(['sellLines.product:id,name,sku', 'sellLines.variation:id,name'])
            ->sells()
            ->forBusiness($user->business_id)
            ->where('invoice_no', $invoiceNo)
            ->final()
            ->first();

        if (!$transaction) {
            return $this->notFound('Invoice not found or not eligible for return');
        }

        // Check if already returned
        if ($transaction->returnTransaction) {
            return $this->error('Invoice already has a return', 422);
        }

        // Get returnable lines
        $returnableLines = $transaction->sellLines
            ->filter(function ($line) {
                return $line->quantity_available_for_return > 0;
            })
            ->map(function ($line) {
                return [
                    'sell_line_id' => $line->id,
                    'product_name' => $line->product->name ?? '',
                    'variation_name' => $line->variation->name ?? '',
                    'quantity' => $line->quantity,
                    'quantity_returned' => $line->quantity_returned,
                    'quantity_available' => $line->quantity_available_for_return,
                    'unit_price' => $line->unit_price_inc_tax,
                ];
            })
            ->values();

        return $this->success([
            'transaction_id' => $transaction->id,
            'invoice_no' => $transaction->invoice_no,
            'transaction_date' => $transaction->transaction_date,
            'final_total' => $transaction->final_total,
            'contact' => $transaction->contact ? [
                'id' => $transaction->contact->id,
                'name' => $transaction->contact->full_name,
            ] : null,
            'returnable_lines' => $returnableLines,
            'can_return' => $returnableLines->count() > 0,
        ], 'Invoice validated successfully');
    }
}
