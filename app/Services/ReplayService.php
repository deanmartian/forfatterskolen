<?php

namespace App\Services;

use App\Http\AdminHelpers;
use App\Replay;

class ReplayService
{
    public function saveReplay($request, $id = null)
    {
        $replay = $id ? Replay::findOrFail($id) : new Replay;

        $replay->title = $request->title;
        $replay->video_link = $request->video_link;
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $pathinfo = pathinfo($_FILES['file']['name']);
            $extension = $pathinfo['extension'];
            $original_filename = $pathinfo['filename'];
            $destinationPath = 'storage/files';
            $filePath = AdminHelpers::checkFileName($destinationPath, $original_filename, $extension); // rename document
            $expFileName = explode('/', $filePath);
            $request->file->move($destinationPath, end($expFileName));

            $replay->file = $filePath;
        }
        $replay->save();
    }
}
