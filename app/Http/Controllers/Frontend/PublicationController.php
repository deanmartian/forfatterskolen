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

        $file = $request->file('manuscript');
        $original = $file->getClientOriginalName();
        $destinationPath = 'Forfatterskolen_app/publications/' . Auth::id();
        $fileName = \App\Http\AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $original);
        $dropboxFileName = basename($fileName);
        $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');
        $filePath = $destinationPath . '/' . $dropboxFileName;

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
                'content_start_marker',
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

        $content = \Illuminate\Support\Facades\Storage::disk('dropbox')->get($publication->output_pdf);
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . \Illuminate\Support\Str::slug($publication->title) . '.pdf"',
        ]);
    }

    public function download(int $id, string $format)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        $pathField = match ($format) {
            'pdf' => 'output_pdf',
            'epub' => 'output_epub',
            'docx' => 'output_docx',
            'cover' => 'cover_front',
            'cover-preview' => null,
            'cover-template' => null,
            default => null,
        };

        if ($format === 'cover-preview') {
            $filePath = storage_path("app/publications/{$id}/covers/cover-preview.pdf");
            if (!file_exists($filePath)) return back()->with('error', 'Forhåndsvisning ikke tilgjengelig.');
            return response()->download($filePath, \Illuminate\Support\Str::slug($publication->title) . '-cover-preview.pdf');
        }

        if ($format === 'cover-template') {
            $coverGen = app(\App\Services\Publishing\CoverGenerator::class);
            $templatePath = $coverGen->generateTemplatePdf($publication);
            return response()->download($templatePath, \Illuminate\Support\Str::slug($publication->title) . '-cover-template.pdf');
        }

        if (!$pathField || !$publication->$pathField) {
            return back()->with('error', 'Filen er ikke tilgjengelig.');
        }

        $content = \Illuminate\Support\Facades\Storage::disk('dropbox')->get($publication->$pathField);
        $filename = \Illuminate\Support\Str::slug($publication->title) . ".{$format}";

        return response($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function generateCover(Request $request, int $id)
    {
        $publication = Publication::where('user_id', Auth::id())->findOrFail($id);

        // Handle cover image upload
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $imageFile = $request->file('cover_image');
            $imageName = 'cover-image-' . time() . '.' . $imageFile->getClientOriginalExtension();
            $localDir = storage_path("app/publications/{$publication->id}/covers");
            if (!is_dir($localDir)) {
                mkdir($localDir, 0755, true);
            }
            $imageFile->move($localDir, $imageName);
            $coverImagePath = $localDir . '/' . $imageName;
        }

        $coverGen = app(\App\Services\Publishing\CoverGenerator::class);
        $coverGen->generate($publication, [
            'template' => $request->input('cover_template', 'classic'),
            'backgroundColor' => $request->input('background_color', '#1a1a2e'),
            'textColor' => $request->input('text_color', '#ffffff'),
            'blurb' => $request->input('blurb', ''),
            'coverImage' => $coverImagePath,
            'preview' => true,
        ]);

        return redirect()->route('learner.publication.show', $publication->id)
            ->with('success', 'Omslaget er generert!');
    }
}
