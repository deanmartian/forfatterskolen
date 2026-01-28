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
