<?php
namespace App\Http\Controllers\Backend;

use App\Editor;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditorCreateRequest;
use App\Http\Requests\EditorUpdateRequest;
use File;

class EditorController extends Controller
{
    /**
     * Display all editors
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $editors = Editor::paginate(15);
        return view('backend.editor.index', compact('editors'));
    }

    /**
     * Display the create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $editor = [
            'name' => old('name'),
            'description' => old('description'),
            'editor_image' => ''
        ];
        return view('backend.editor.create', compact('editor'));
    }

    /**
     * Create new editor
     * @param EditorCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(EditorCreateRequest $request)
    {
        $editor                 = new Editor();
        $editor->name           = $request->name;
        $editor->description    = $request->description;

        if ($request->hasFile('editor_image')) :
            $destinationPath = 'images/editors'; // upload path
            $extension = $request->editor_image->extension(); // getting image extension
            $uploadedFile = $request->editor_image->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->editor_image->move($destinationPath, $fileName);

            // optimize image
            if ( strtolower( $extension ) == "png" ) :
                $image = imagecreatefrompng($fileName);
                imagepng($image, $fileName, 9);
            else :
                $image = imagecreatefromjpeg($fileName);
                imagejpeg($image, $fileName, 70);
            endif;
            $editor->editor_image = '/'.$fileName;
        endif;

        $editor->save();

       return redirect('/editor');
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $editor = Editor::findOrFail($id)->toArray();
        return view('backend.editor.edit', compact('editor'));
    }

    /**
     * Update the editor
     * @param $id
     * @param EditorUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, EditorUpdateRequest $request)
    {
        $editor = Editor::find($id);
        if ($editor) {
            $editor->name           = $request->name;
            $editor->description    = $request->description;

            if ($request->hasFile('editor_image')) :
                $destinationPath = 'images/editors'; // upload path
                $extension = $request->editor_image->extension(); // getting image extension
                $uploadedFile = $request->editor_image->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
                $request->editor_image->move($destinationPath, $fileName);

                // optimize image
                if ( strtolower( $extension ) == "png" ) :
                    $image = imagecreatefrompng($fileName);
                    imagepng($image, $fileName, 9);
                else :
                    $image = imagecreatefromjpeg($fileName);
                    imagejpeg($image, $fileName, 70);
                endif;
                $editor->editor_image = '/'.$fileName;
            endif;

            $editor->save();
        }
        return redirect('/editor');
    }

    /**
     * Delete the editor
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id){
        $editor = Editor::find($id);
        if ($editor) {
            $image = substr($editor->editor_image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $editor->forceDelete();
        }
        return redirect('/editor');
    }
}