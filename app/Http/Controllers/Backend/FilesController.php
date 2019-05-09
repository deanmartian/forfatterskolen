<?php
namespace App\Http\Controllers\Backend;

use App\FileUploaded;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FilesController extends Controller {

    protected $model;
    public function __construct(FileUploaded $fileUploaded)
    {
        $this->model = $fileUploaded;
    }

    public function index()
    {
        $files = $this->model->all();
        return view('backend.files.index', compact('files'));
    }

    /**
     * Create new file record
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $destinationPath = 'storage/files'; // upload path
        $actual_name = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $extension = $file->extension();
        $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

        $request->file->move($destinationPath, $fileName);

        $fileUpload = new FileUploaded();
        $fileUpload->file_location = $fileName;
        $fileUpload->hash = AdminHelpers::generateHash(20);
        $fileUpload->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('File uploaded successfully.'),
            'alert_type' => 'success'
        ]);

    }

    /**
     * Update file record
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $fileUpload = $this->model->find($id);
        if (!$fileUpload) {
            return redirect()->back()->with([
               'errors' => AdminHelpers::createMessageBag('File not found.')
            ]);
        }

        $file = $request->file('file');
        $destinationPath = 'storage/files'; // upload path
        $actual_name = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $extension = $file->extension();
        $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

        $request->file->move($destinationPath, $fileName);

        if (\File::exists($fileUpload->file_location)) {
            \File::delete($fileUpload->file_location);
        }

            $fileUpload->file_location = $fileName;
        $fileUpload->hash = AdminHelpers::generateHash(20);
        $fileUpload->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('File uploaded successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * Delete file record
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $fileUpload = $this->model->find($id);

        if (!$fileUpload) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('File not found.')
            ]);
        }

        if (\File::exists($fileUpload->file_location)) {
            \File::delete($fileUpload->file_location);
        }

        $fileUpload->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('File deleted successfully.'),
            'alert_type' => 'success'
        ]);
    }
    
}