<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class TinymceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $directory = public_path('photos/1070');

        // Ensure the directory exists
        if (! file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = $originalName.'.'.$extension;
        $counter = 1;

        // Check if the file already exists and add a number if necessary
        while (file_exists($directory.'/'.$fileName)) {
            $fileName = $originalName.'_'.$counter.'.'.$extension;
            $counter++;
        }

        // Move file to public/photos/1070
        $file->move($directory, $fileName);

        return response()->json([
            'location' => asset('photos/1070/'.$fileName), // Correct public URL
        ]);
    }

    public function images(): View
    {
        $folderPath = public_path('photos'); // Change to your folder path
        $files = File::allFiles($folderPath);

        $images = [];
        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $relativePath = str_replace('\\', '/', str_replace(public_path(), '', $file->getRealPath()));
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => asset($relativePath),
                ];
            }
        }

        return view('backend.tinymce-page.image', compact('images'));
    }
}
