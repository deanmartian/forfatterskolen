<?php

namespace App\Http\Controllers\Api\V1;

use App\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $invoices = $user->invoices()
            ->latest()
            ->take(10)
            ->get()
            ->map(function (Invoice $invoice): array {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'reference' => $invoice->kid_number,
                    'status' => $this->statusLabel($invoice),
                    'total' => $invoice->gross ?? $invoice->balance,
                    'due_date' => $invoice->fiken_dueDate,
                    'created_at' => $invoice->getRawOriginal('created_at'),
                ];
            })
            ->values()
            ->all();

        return response()->json(['data' => $invoices]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);
        $invoice = Invoice::find($id);

        if (! $invoice) {
            return $this->errorResponse('Invoice not found.', 'invoice_not_found', Response::HTTP_NOT_FOUND);
        }

        if ($invoice->user_id !== $user->id) {
            return $this->errorResponse('You do not have access to this invoice.', 'invoice_forbidden', Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'data' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'reference' => $invoice->kid_number,
                'status' => $this->statusLabel($invoice),
                'total' => $invoice->gross ?? $invoice->balance,
                'balance' => $invoice->balance,
                'due_date' => $invoice->fiken_dueDate,
                'issue_date' => $invoice->fiken_issueDate,
                'pdf_url' => $invoice->pdf_url,
                'fiken_weblink' => $invoice->fiken_weblink,
                'created_at' => $invoice->getRawOriginal('created_at'),
            ],
        ]);
    }

    public function pdf(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);
        $invoice = Invoice::find($id);

        if (! $invoice) {
            return $this->errorResponse('Invoice not found.', 'invoice_not_found', Response::HTTP_NOT_FOUND);
        }

        if ($invoice->user_id !== $user->id) {
            return $this->errorResponse('You do not have access to this invoice.', 'invoice_forbidden', Response::HTTP_FORBIDDEN);
        }

        if (! $invoice->pdf_url) {
            return $this->errorResponse('Invoice PDF not available.', 'invoice_pdf_missing', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
                'url' => $invoice->pdf_url,
            ],
        ]);
    }

    private function statusLabel(Invoice $invoice): string
    {
        if ($invoice->fiken_is_paid === Invoice::COMPLETED) {
            return 'paid';
        }

        if ($invoice->fiken_is_paid === Invoice::CREDITED) {
            return 'credited';
        }

        return 'unpaid';
    }
}
