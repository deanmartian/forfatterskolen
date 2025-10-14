<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\DocumentConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class DocumentConversionController extends Controller
{
    public function convertToDocx(Request $request, DocumentConversionService $documentConversionService)
    {
        $request->validate([
            'document' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,odt,pages'],
        ]);

        $uploadedFile = $request->file('document');
        $conversion = null;

        try {
            $conversion = $documentConversionService->convertUploadedFileToDocx(
                $uploadedFile,
                'shop-manuscript-document-converter',
                Auth::id()
            );
        } catch (\Throwable $exception) {
            Log::error('Shop manuscript document conversion failed', [
                'user_id' => Auth::id(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'message' => $exception->getMessage(),
            ]);
        }

        if (! $conversion) {
            $message = __('We could not convert the file. Make sure the document contains selectable text and try again.');

            return response()->json([
                'errors' => [
                    'manuscript' => [$message],
                ],
                'message' => $message,
            ], 422);
        }

        return response()->download(
            $conversion['full_path'],
            $conversion['download_name'],
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
        )->deleteFileAfterSend(true);
    }
}
