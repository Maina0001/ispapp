<?php

namespace Modules\Billing\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Billing\Http\Requests\GenerateInvoiceRequest;
use Modules\Billing\Http\Resources\InvoiceResource;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\InvoiceGenerator;
use Modules\Customer\Models\Customer;

/**
 * Class InvoiceController
 * @package Modules\Billing\Http\Controllers\Api\V1
 */
class InvoiceController extends Controller
{
    /**
     * @param InvoiceGenerator $invoiceGenerator
     */
    public function __construct(
        protected InvoiceGenerator $invoiceGenerator
    ) {}

    /**
     * List all invoices with pagination.
     * Automatically scoped by tenant_id via BaseModel.
     */
    public function index(): AnonymousResourceCollection
    {
        $invoices = Invoice::query()
            ->with(['customer', 'items'])
            ->latest()
            ->paginate(request()->query('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    /**
     * Generate an ad-hoc invoice for a specific customer.
     */
    public function store(GenerateInvoiceRequest $request): JsonResponse
    {
        $customer = Customer::findOrFail($request->customer_id);

        // Delegate generation logic to the domain service
        $invoice = $this->invoiceGenerator->generateForCustomer($customer, $request->validated());

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load('items'));
    }

    /**
     * Mark an invoice as void/cancelled.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->update(['status' => 'void']);

        return response()->json(['message' => 'Invoice has been voided.']);
    }
}