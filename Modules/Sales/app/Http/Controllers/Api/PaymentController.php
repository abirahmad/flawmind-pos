<?php

namespace Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sales\Http\Requests\StorePaymentRequest;
use Modules\Sales\Http\Resources\PaymentResource;
use Modules\Sales\Models\TransactionPayment;
use Modules\Sales\Services\PaymentService;
use Modules\Sales\Services\TransactionService;
use Modules\Sales\Traits\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Payments",
    description: "API endpoints for payment management"
)]
class PaymentController extends Controller
{
    use ApiResponse;

    protected PaymentService $paymentService;
    protected TransactionService $transactionService;

    public function __construct(PaymentService $paymentService, TransactionService $transactionService)
    {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
    }

    #[OA\Get(
        path: "/v1/sales/payments/{transaction_id}",
        summary: "Get payments for a transaction",
        description: "Get all payments for a specific transaction",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "transaction_id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payments retrieved successfully"),
            new OA\Response(response: 404, description: "Transaction not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $transaction = $this->transactionService->getTransaction($transactionId, $user->business_id);

        if (!$transaction) {
            return $this->notFound('Transaction not found');
        }

        $payments = $this->paymentService->getPayments($transactionId);

        return $this->success(
            PaymentResource::collection($payments),
            'Payments retrieved successfully'
        );
    }

    #[OA\Post(
        path: "/v1/sales/payments/{transaction_id}",
        summary: "Add payment to transaction",
        description: "Add a new payment to a transaction",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "transaction_id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["amount", "method"],
                properties: [
                    new OA\Property(property: "amount", type: "number"),
                    new OA\Property(property: "method", type: "string", enum: ["cash", "card", "cheque", "bank_transfer", "other"]),
                    new OA\Property(property: "paid_on", type: "string", format: "date-time"),
                    new OA\Property(property: "note", type: "string"),
                    new OA\Property(property: "account_id", type: "integer"),
                    new OA\Property(property: "card_number", type: "string"),
                    new OA\Property(property: "card_type", type: "string"),
                    new OA\Property(property: "card_holder_name", type: "string"),
                    new OA\Property(property: "cheque_number", type: "string"),
                    new OA\Property(property: "bank_account_number", type: "string"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Payment added successfully"),
            new OA\Response(response: 404, description: "Transaction not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(StorePaymentRequest $request, int $transactionId): JsonResponse
    {
        try {
            $user = $request->user();

            $transaction = $this->transactionService->getTransaction($transactionId, $user->business_id);

            if (!$transaction) {
                return $this->notFound('Transaction not found');
            }

            $data = array_merge($request->validated(), [
                'created_by' => $user->id,
            ]);

            $payment = $this->paymentService->addPayment($transaction, $data);

            // Update transaction payment status
            $this->transactionService->updatePaymentStatus($transaction);

            return $this->created(
                new PaymentResource($payment),
                'Payment added successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to add payment: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/payments/view/{id}",
        summary: "Get payment details",
        description: "Get details of a specific payment",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment details retrieved successfully"),
            new OA\Response(response: 404, description: "Payment not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $payment = $this->paymentService->getPayment($id);

        if (!$payment) {
            return $this->notFound('Payment not found');
        }

        // Verify business access
        $user = $request->user();
        if ($payment->business_id !== $user->business_id) {
            return $this->forbidden('Access denied');
        }

        return $this->success(
            new PaymentResource($payment),
            'Payment retrieved successfully'
        );
    }

    #[OA\Put(
        path: "/v1/sales/payments/view/{id}",
        summary: "Update payment",
        description: "Update an existing payment",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment updated successfully"),
            new OA\Response(response: 404, description: "Payment not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(StorePaymentRequest $request, int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);

            if (!$payment) {
                return $this->notFound('Payment not found');
            }

            // Verify business access
            $user = $request->user();
            if ($payment->business_id !== $user->business_id) {
                return $this->forbidden('Access denied');
            }

            $payment = $this->paymentService->updatePayment($payment, $request->validated());

            // Update transaction payment status if applicable
            if ($payment->transaction_id) {
                $transaction = $payment->transaction;
                $this->transactionService->updatePaymentStatus($transaction);
            }

            return $this->success(
                new PaymentResource($payment),
                'Payment updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Failed to update payment: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Delete(
        path: "/v1/sales/payments/view/{id}",
        summary: "Delete payment",
        description: "Delete a payment",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment deleted successfully"),
            new OA\Response(response: 404, description: "Payment not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);

            if (!$payment) {
                return $this->notFound('Payment not found');
            }

            // Verify business access
            $user = $request->user();
            if ($payment->business_id !== $user->business_id) {
                return $this->forbidden('Access denied');
            }

            $transactionId = $payment->transaction_id;
            $this->paymentService->deletePayment($payment);

            // Update transaction payment status if applicable
            if ($transactionId) {
                $transaction = $this->transactionService->getTransaction($transactionId);
                if ($transaction) {
                    $this->transactionService->updatePaymentStatus($transaction);
                }
            }

            return $this->success(null, 'Payment deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete payment: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Post(
        path: "/v1/sales/payments/pay-contact-due/{contact_id}",
        summary: "Pay contact due",
        description: "Pay outstanding dues for a contact",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "contact_id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["amount"],
                properties: [
                    new OA\Property(property: "amount", type: "number"),
                    new OA\Property(property: "method", type: "string"),
                    new OA\Property(property: "note", type: "string"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Payment applied successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function payContactDue(Request $request, int $contactId): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'method' => 'nullable|string',
                'note' => 'nullable|string',
                'paid_on' => 'nullable|date',
            ]);

            $user = $request->user();

            $result = $this->paymentService->payContactDue($contactId, [
                'business_id' => $user->business_id,
                'amount' => $request->input('amount'),
                'method' => $request->input('method', 'cash'),
                'note' => $request->input('note'),
                'paid_on' => $request->input('paid_on', now()),
                'created_by' => $user->id,
            ]);

            return $this->success($result, 'Payment applied successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to apply payment: ' . $e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: "/v1/sales/payments/contact-due/{contact_id}",
        summary: "Get contact due",
        description: "Get total due amount for a contact",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "contact_id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact due retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function getContactDue(Request $request, int $contactId): JsonResponse
    {
        $user = $request->user();

        $due = $this->paymentService->getContactDue($contactId);
        $advance = $this->paymentService->getContactAdvance($contactId, $user->business_id);

        return $this->success([
            'contact_id' => $contactId,
            'total_due' => $due,
            'advance_balance' => $advance,
            'net_due' => $due - $advance,
        ], 'Contact due retrieved successfully');
    }

    #[OA\Get(
        path: "/v1/sales/payments/methods",
        summary: "Get payment methods",
        description: "Get available payment methods",
        tags: ["Payments"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Payment methods retrieved successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function getMethods(): JsonResponse
    {
        return $this->success(
            PaymentService::getPaymentMethods(),
            'Payment methods retrieved successfully'
        );
    }
}
