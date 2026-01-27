<?php

namespace App\Http\Controllers\Api\V1;

use App\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                    'gross' => $invoice->gross,
                    'balance' => $invoice->balance,
                    'fiken_balance' => $invoice->fiken_balance,
                    'fiken_due_date' => $invoice->fiken_dueDate,
                    'fiken_issue_date' => $invoice->fiken_issueDate,
                    'status' => $invoice->fiken_is_paid,
                    'is_paid' => $invoice->paid(),
                    'pdf_url' => $invoice->pdf_url,
                    'fiken_weblink' => $invoice->fiken_weblink,
                    'created_at' => $invoice->getRawOriginal('created_at'),
                ];
            })
            ->values()
            ->all();

        return response()->json(['data' => $invoices]);
    }
}
