<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\PageMeta;
use Illuminate\Http\Request;
use File;

class PageMetaController extends Controller
{

    /**
     * Display the index page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $pageMetas = PageMeta::all();
        return view('backend.page-meta.index', compact('pageMetas'));
    }

    /**
     * Create new page meta
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'url'               => 'required|url',
            'meta_description'  => 'required|max:350'
        ]);

        $meta = new PageMeta();

        if ($request->hasFile('meta_image')) :
            if( !File::exists('storage/meta-images/') ) :
                File::makeDirectory('meta-images');
            endif;
            $destinationPath = 'storage/meta-images/'; // upload path
            $extension = $request->meta_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->meta_image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $meta->meta_image = '/'.$destinationPath.$fileName;
        endif;

        $meta->url              = $request->url;
        $meta->meta_title       = $request->meta_title;
        $meta->meta_description = $request->meta_description;
        $meta->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Page meta created successfully.'), 'alert_type' => 'success']);
    }

    /**
     * Update page meta
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $pageMeta = PageMeta::find($id);
        if ($pageMeta) {

            if ($request->hasFile('meta_image')) :
                if( !File::exists('storage/meta-images/') ) :
                    File::makeDirectory('meta-images');
                endif;
                $destinationPath = 'storage/meta-images/'; // upload path
                $extension = $request->meta_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->meta_image->move($destinationPath, $fileName);
                // optimize image
                if ( strtolower( $extension ) == "png" ) :
                    $image = imagecreatefrompng($destinationPath.$fileName);
                    imagepng($image, $destinationPath.$fileName, 9);
                else :
                    $image = imagecreatefromjpeg($destinationPath.$fileName);
                    imagejpeg($image, $destinationPath.$fileName, 70);
                endif;
                $pageMeta->meta_image = '/'.$destinationPath.$fileName;
            endif;

            $pageMeta->url              = $request->url;
            $pageMeta->meta_title       = $request->meta_title;
            $pageMeta->meta_description = $request->meta_description;
            $pageMeta->save();
        }
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Page meta updated successfully'),
            'alert_type' => 'success']);
    }

    /**
     * Delete page meta
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
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