<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TinymceController extends Controller {

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);
    
        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $directory = public_path('photos/1070');
    
        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    
        $fileName = $originalName . '.' . $extension;
        $counter = 1;
    
        // Check if the file already exists and add a number if necessary
        while (file_exists($directory . '/' . $fileName)) {
            $fileName = $originalName . '_' . $counter . '.' . $extension;
            $counter++;
        }
    
        // Move file to public/photos/1070
        $file->move($directory, $fileName);
    
        return response()->json([
            'location' => asset('photos/1070/' . $fileName) // Correct public URL
        ]);
    }

}