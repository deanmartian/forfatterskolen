<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Webinar;
use App\WebinarPresenter;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebinarPresenterController extends Controller
{
    public function store($webinar_id, Request $request): RedirectResponse
    {
        $webinar = Webinar::findOrFail($webinar_id);

        $webinarPresenter = new WebinarPresenter;
        $webinarPresenter->webinar_id = $webinar->id;
        $webinarPresenter->first_name = $request->first_name;
        $webinarPresenter->last_name = $request->last_name;
        $webinarPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/webinar-presenters/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $webinarPresenter->image = '/'.$destinationPath.$fileName;
        }

        $webinarPresenter->save();

        return redirect()->back();
    }

    public function update($webinar_id, $id, Request $request): RedirectResponse
    {

        $webinarPresenter = WebinarPresenter::findOrFail($id);
        $webinarPresenter->first_name = $request->first_name;
        $webinarPresenter->last_name = $request->last_name;
        $webinarPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $image = substr($webinarPresenter->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/webinar-presenter-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $webinarPresenter->image = '/'.$destinationPath.$fileName;
        }

        $webinarPresenter->save();

        return redirect()->back();
    }

    public function destroy($webinar_id, $id): RedirectResponse
    {
        $webinarPresenter = WebinarPresenter::findOrFail($id);
        $webinarPresenter->forceDelete();

        return redirect()->back();
    }
}
