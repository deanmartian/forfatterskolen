<?php

namespace App\Http\Controllers\Api\V1;

use App\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $unpaid = $user->invoices()
            ->where('fiken_is_paid', 0)
            ->orderBy('fiken_dueDate', 'ASC')
            ->get();

        $paid = $user->invoices()
            ->whereIn('fiken_is_paid', [1, 3])
            ->orderBy('fiken_dueDate', 'DESC')
            ->get();

        $invoices = $unpaid->merge($paid)->paginate(15);

        if ($request->filled('filter')) {
            $invoices = $user->invoices()
                ->where('id', $request->get('filter'))
                ->paginate(15);
        }

        $sveaOrders = $user->orders()->svea()->with('coachingTime')->paginate(10);
        $payLaterOrders = $user->orders()
            ->where([
                'is_pay_later' => 1,
                'is_processed' => 1,
                'is_invoice_sent' => 0,
                'is_order_withdrawn' => 0,
            ])
            ->paginate(10);

        $orderAttachments = DB::table('course_order_attachments')
            ->leftJoin('courses', 'course_order_attachments.course_id', '=', 'courses.id')
            ->leftJoin('packages', 'course_order_attachments.package_id', '=', 'packages.id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->select(
                'course_order_attachments.*',
                'courses.title as course_title',
                'courses_taken.id as course_taken_id',
                'courses_taken.deleted_at'
            )
            ->where('courses_taken.user_id', $user->id)
            ->where('course_order_attachments.user_id', $user->id)
            ->whereNull('courses_taken.deleted_at')
            ->groupBy('course_order_attachments.id')
            ->get();

        return response()->json([
            'data' => [
                'invoices' => $invoices,
                'svea_orders' => $sveaOrders,
                'user' => $user,
                'order_attachments' => $orderAttachments,
                'gift_purchases' => $user->giftPurchases,
                'order_history' => $user->orders,
                'time_registers' => $user->timeRegisters->load('project'),
                'pay_later_orders' => $payLaterOrders,
            ],
        ]);
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

    public function pdf(Request $request, int $id)
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

        $pdfUrl = $this->normalizedPdfUrl($invoice->pdf_url);
        $download = $this->downloadPdf($pdfUrl);

        if ($download['status'] !== Response::HTTP_OK) {
            return $this->errorResponse($download['message'], $download['code'], $download['status']);
        }

        return response()
            ->download(
                $download['path'],
                $download['filename'],
                ['Content-Type' => 'application/pdf']
            )
            ->deleteFileAfterSend(true);
    }

    public function receipt(Request $request, int $id)
    {
        $receipt = $this->buildReceipt($request, $id);

        if ($receipt instanceof JsonResponse) {
            return $receipt;
        }

        return $receipt['pdf']->download($receipt['filename']);
    }

    public function receiptView(Request $request, int $id)
    {
        $receipt = $this->buildReceipt($request, $id);

        if ($receipt instanceof JsonResponse) {
            return $receipt;
        }

        return response(view('frontend.pdf.invoice-receipt', [
            'invoice' => $receipt['invoice'],
            'user' => $receipt['user'],
        ]));
    }

    public function pdfView(Request $request, int $id)
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

        $pdfUrl = $this->normalizedPdfUrl($invoice->pdf_url);
        $download = $this->downloadPdf($pdfUrl);

        if ($download['status'] !== Response::HTTP_OK) {
            return $this->errorResponse($download['message'], $download['code'], $download['status']);
        }

        return response()->file(
            $download['path'],
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$download['filename'].'"',
            ]
        )->deleteFileAfterSend(true);
    }


    /**
     * @return array{invoice:Invoice,user:mixed,pdf:mixed,filename:string}|JsonResponse
     */
    private function buildReceipt(Request $request, int $id)
    {
        $user = $this->apiUser($request);

        $invoice = Invoice::with(['transactions', 'package.course', 'payment_plan'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $invoice) {
            return $this->errorResponse('Invoice not found.', 'invoice_not_found', Response::HTTP_NOT_FOUND);
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML(view('frontend.pdf.invoice-receipt', compact('invoice', 'user')));

        $invoiceNumber = $invoice->invoice_number ?? $invoice->id;
        $fileName = ($invoiceNumber ? str_pad((string) $invoiceNumber, 6, '0', STR_PAD_LEFT) : $invoice->id).'-kvittering.pdf';

        return [
            'invoice' => $invoice,
            'user' => $user,
            'pdf' => $pdf,
            'filename' => $fileName,
        ];
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

    private function normalizedPdfUrl(string $pdfUrl): string
    {
        $withExtension = str_contains($pdfUrl, '.pdf') ? $pdfUrl : $pdfUrl.'.pdf';

        if (str_contains($pdfUrl, 'v2')) {
            return $withExtension;
        }

        return str_replace('https://fiken.no/filer/', 'https://fiken.no/api/v1/files/', $withExtension);
    }

    /**
     * @return array{status:int,code:string,message:string,path?:string,filename?:string}
     */
    private function downloadPdf(string $pdfUrl): array
    {
        $directory = storage_path('app/tmp');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'code' => 'invoice_pdf_download_failed',
                'message' => 'Unable to prepare invoice PDF download.',
            ];
        }

        $filePath = tempnam($directory, 'invoice-pdf-');

        if ($filePath === false) {
            return [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'code' => 'invoice_pdf_download_failed',
                'message' => 'Unable to prepare invoice PDF download.',
            ];
        }

        $handle = fopen($filePath, 'wb');

        if ($handle === false) {
            return [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'code' => 'invoice_pdf_download_failed',
                'message' => 'Unable to prepare invoice PDF download.',
            ];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $handle);
        curl_setopt($ch, CURLOPT_URL, $pdfUrl);

        if (str_contains($pdfUrl, 'v2')) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headersV2());
        } else {
            curl_setopt($ch, CURLOPT_USERPWD, $this->basicAuth());
        }

        curl_exec($ch);
        $curlError = curl_errno($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($handle);

        if ($curlError || $status >= 400) {
            @unlink($filePath);

            if ($status === Response::HTTP_NOT_FOUND) {
                return [
                    'status' => Response::HTTP_NOT_FOUND,
                    'code' => 'invoice_pdf_missing',
                    'message' => 'Invoice PDF not available.',
                ];
            }

            return [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'code' => 'invoice_pdf_download_failed',
                'message' => 'Unable to download invoice PDF.',
            ];
        }

        $filename = basename(parse_url($pdfUrl, PHP_URL_PATH) ?? '') ?: 'invoice.pdf';

        return [
            'status' => Response::HTTP_OK,
            'code' => 'ok',
            'message' => 'ok',
            'path' => $filePath,
            'filename' => $filename,
        ];
    }

    private function headersV2(): array
    {
        return [
            'Accept: application/json',
            'Authorization: Bearer '.config('services.fiken.personal_api_key'),
            'Content-Type: Application/json',
        ];
    }

    private function basicAuth(): string
    {
        return 'cleidoscope@gmail.com:moonfang';
    }
}
