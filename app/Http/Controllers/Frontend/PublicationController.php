<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Jobs\ProcessPublicationJob;
use App\Models\Publication;
use App\Services\Publishing\BindingType;
use App\Services\Publishing\CoverLamination;
use App\Services\Publishing\PaperType;
use App\Services\Publishing\TrimSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function index(): View
    {
        $publications = Publication::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.learner.self-publishing.publication.index', compact('publications'));
    }

    public function create(): View
    {
        return view('frontend.learner.self-publishing.publication.wizard', [
            'publication' => null,
            'step' => 1,
            'trimSizes' => TrimSize::cases(),
            'paperTypes' => PaperType::cases(),
            'bindingTypes' => BindingType::cases(),
            'coverLaminations' => CoverLamination::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'manuscript' => 'required|file|mimes:docx|max:51200',
            'title' => 'required|string|max:500',
            'author_name' => 'required|string|max:255',
        ]);

        $filePath = FrontendHelpers::saveFile($request, 'publications', 'manuscript');

        $publication = Publication::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'author_name' => $request->author_name,
            'source_manuscript' => $filePath,
            'status' => 'draft',
            'wizard_step' => 2,
        ]);

        return redirect()->route('learner.publication.show', $publication->id);
    }

    public function show(int $id): View
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        return view('frontend.learner.self-publishing.publication.wizard', [
            'publication' => $publication,
            'step' => $publication->wizard_step,
            'trimSizes' => TrimSize::cases(),
            'paperTypes' => PaperType::cases(),
            'bindingTypes' => BindingType::cases(),
            'coverLaminations' => CoverLamination::cases(),
        ]);
    }

    public function updateStep(Request $request, int $id, int $step)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        $data = ['wizard_step' => min($step + 1, 5)];

        if ($step === 2) {
            $request->validate([
                'title' => 'required|string|max:500',
                'author_name' => 'required|string|max:255',
            ]);
            $data += $request->only([
                'title', 'subtitle', 'author_name', 'isbn',
                'language', 'genre', 'description', 'dedication', 'colophon_extra',
            ]);
        }

        if ($step === 3) {
            $data += $request->only([
                'theme', 'trim_size', 'paper_type', 'binding_type', 'cover_lamination',
            ]);
        }

        if ($step === 4) {
            if ($request->hasFile('cover_front')) {
                $data['cover_front'] = FrontendHelpers::saveFile($request, 'publications/covers', 'cover_front');
            }
        }

        $publication->update($data);

        return redirect()->route('learner.publication.show', $publication->id);
    }

    public function generate(int $id)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        if ($publication->isProcessing()) {
            return back()->with('error', 'Boken genereres allerede.');
        }

        $publication->update(['status' => 'draft', 'error_message' => null]);
        dispatch(new ProcessPublicationJob($publication->id));

        return redirect()->route('learner.publication.show', $publication->id)
            ->with('success', 'Bokgenerering startet! Du vil se resultatet her snart.');
    }

    public function status(int $id): JsonResponse
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'status' => $publication->status,
            'error_message' => $publication->error_message,
            'word_count' => $publication->word_count,
            'page_count' => $publication->page_count,
            'chapter_count' => $publication->chapter_count,
        ]);
    }

    public function preview(int $id)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        if (!$publication->output_pdf) {
            return back()->with('error', 'PDF er ikke generert ennå.');
        }

        $pdfPath = storage_path('app/' . $publication->output_pdf);
        if (!file_exists($pdfPath)) {
            return back()->with('error', 'PDF-filen ble ikke funnet.');
        }

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function download(int $id, string $format)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        $pathField = match ($format) {
            'pdf' => 'output_pdf',
            'epub' => 'output_epub',
            'docx' => 'output_docx',
            default => null,
        };

        if (!$pathField || !$publication->$pathField) {
            return back()->with('error', 'Filen er ikke tilgjengelig.');
        }

        $filePath = storage_path('app/' . $publication->$pathField);
        $filename = \Illuminate\Support\Str::slug($publication->title) . ".{$format}";

        return response()->download($filePath, $filename);
    }
}
