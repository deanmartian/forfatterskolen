<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddWorkshopPresenterRequest;
use App\Workshop;
use App\WorkshopPresenter;
use File;
use Illuminate\Http\RedirectResponse;

class WorkshopPresenterController extends Controller
{
    public function store($workshop_id, AddWorkshopPresenterRequest $request): RedirectResponse
    {
        $workshop = Workshop::findOrFail($workshop_id);

        $workshopPresenter = new WorkshopPresenter;
        $workshopPresenter->workshop_id = $workshop->id;
        $workshopPresenter->first_name = $request->first_name;
        $workshopPresenter->last_name = $request->last_name;
        $workshopPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/presenter-images/'; // upload path
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
            $workshopPresenter->image = '/'.$destinationPath.$fileName;
        }

        $workshopPresenter->save();

        return redirect()->back();
    }

    public function update($workshop_id, $presenter_id, AddWorkshopPresenterRequest $request): RedirectResponse
    {
        $workshop = Workshop::findOrFail($workshop_id);

        $workshopPresenter = WorkshopPresenter::findOrFail($presenter_id);
        $workshopPresenter->first_name = $request->first_name;
        $workshopPresenter->last_name = $request->last_name;
        $workshopPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $image = substr($workshopPresenter->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/presenter-images/'; // upload path
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
            $workshopPresenter->image = '/'.$destinationPath.$fileName;
        }

        $workshopPresenter->save();

        return redirect()->back();
    }

    public function destroy($workshop_id, $presenter_id): RedirectResponse
    {
        $workshop = Workshop::findOrFail($workshop_id);
        $workshopPresenter = WorkshopPresenter::findOrFail($presenter_id);
        $workshopPresenter->forceDelete();

        return redirect()->back();
    }
}
