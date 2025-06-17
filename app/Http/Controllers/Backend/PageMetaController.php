<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\PageMeta;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageMetaController extends Controller
{
    /**
     * Display the index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $pageMetas = PageMeta::all();

        return view('backend.page-meta.index', compact('pageMetas'));
    }

    /**
     * Create new page meta
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'url' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'meta_title' => 'required|max:70|min:40',
            'meta_description' => 'required|max:160|min:70',
        ]);

        $meta = new PageMeta;

        if ($request->hasFile('meta_image')) {
            if (! File::exists('storage/meta-images/')) {
                File::makeDirectory('meta-images');
            }
            $destinationPath = 'storage/meta-images/'; // upload path
            $extension = $request->meta_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->meta_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $meta->meta_image = '/'.$destinationPath.$fileName;
        }

        $meta->url = $request->url;
        $meta->meta_title = $request->meta_title;
        $meta->meta_description = $request->meta_description;
        $meta->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Page meta created successfully.'), 'alert_type' => 'success']);
    }

    /**
     * Update page meta
     */
    public function update($id, Request $request): RedirectResponse
    {
        $pageMeta = PageMeta::find($id);

        $request->validate([
            'url' => 'required|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'meta_title' => 'required|max:70|min:40',
            'meta_description' => 'required|max:160|min:70',
        ]);

        if ($pageMeta) {

            if ($request->hasFile('meta_image')) {
                if (! File::exists('storage/meta-images/')) {
                    File::makeDirectory('meta-images');
                }
                $destinationPath = 'storage/meta-images/'; // upload path
                $extension = $request->meta_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->meta_image->move($destinationPath, $fileName);
                // optimize image
                if (strtolower($extension) == 'png') {
                    $image = imagecreatefrompng($destinationPath.$fileName);
                    imagepng($image, $destinationPath.$fileName, 9);
                } else {
                    $image = imagecreatefromjpeg($destinationPath.$fileName);
                    imagejpeg($image, $destinationPath.$fileName, 70);
                }
                $pageMeta->meta_image = '/'.$destinationPath.$fileName;
            }

            $pageMeta->url = $request->url;
            $pageMeta->meta_title = $request->meta_title;
            $pageMeta->meta_description = $request->meta_description;
            $pageMeta->save();
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Page meta updated successfully'),
            'alert_type' => 'success']);
    }

    /**
     * Delete page meta
     */
    public function destroy($id): RedirectResponse
    {
        $pageMeta = PageMeta::where('id', $id)->firstOrFail();
        $image = substr($pageMeta->meta_image, 1);
        if (File::exists($image)) {
            File::delete($image);
        }
        $pageMeta->forceDelete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Page meta deleted successfully'),
            'alert_type' => 'success']);
    }
}
