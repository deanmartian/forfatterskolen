<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Workshop;
use App\WorkshopMenu;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Validator;

class WorkshopMenuController extends Controller
{
    public function store($workshop_id, Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $workshop = Workshop::findOrFail($workshop_id);

        $workshopMenu = new WorkshopMenu;
        $workshopMenu->workshop_id = $workshop->id;
        $workshopMenu->title = $request->title;
        $workshopMenu->description = $request->description;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/menu-images/'; // upload path
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
            $workshopMenu->image = '/'.$destinationPath.$fileName;
        }

        $workshopMenu->save();

        return redirect()->back();
    }

    public function update($workshop_id, $presenter_id, Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $workshop = Workshop::findOrFail($workshop_id);

        $workshopMenu = WorkshopMenu::findOrFail($presenter_id);
        $workshopMenu->title = $request->title;
        $workshopMenu->description = $request->description;

        if ($request->hasFile('image')) {
            $image = substr($workshopMenu->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/menu-images/'; // upload path
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
            $workshopMenu->image = '/'.$destinationPath.$fileName;
        }

        $workshopMenu->save();

        return redirect()->back();
    }

    public function destroy($workshop_id, $presenter_id): RedirectResponse
    {
        $workshop = Workshop::findOrFail($workshop_id);
        $workshopMenu = WorkshopMenu::findOrFail($presenter_id);
        $workshopMenu->forceDelete();

        return redirect()->back();
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
    }
}
