<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\PageMeta;
use Illuminate\Http\Request;

class PageMetaController extends Controller
{

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

        PageMeta::create([
                'url'               => $request->url,
                'meta_title'        => $request->meta_title,
                'meta_description'  => $request->meta_description
            ]);
        return redirect()->back();
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
            $pageMeta->url              = $request->url;
            $pageMeta->meta_title       = $request->meta_title;
            $pageMeta->meta_description = $request->meta_description;
            $pageMeta->save();
        }
        return redirect()->back();
    }

    /**
     * Delete page meta
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $pageMeta = PageMeta::where('id', $id)->firstOrFail();
        $pageMeta->forceDelete();

        return redirect()->back();
    }

}