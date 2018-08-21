<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Workshop;
use App\WorkshopPresenter;
use App\Http\Requests\AddWorkshopPresenterRequest;
use File; 

class WorkshopPresenterController extends Controller
{
   


    public function store($workshop_id, AddWorkshopPresenterRequest $request)
    {
        $workshop = Workshop::findOrFail($workshop_id);

        $workshopPresenter = new WorkshopPresenter();
        $workshopPresenter->workshop_id = $workshop->id;
        $workshopPresenter->first_name = $request->first_name;
        $workshopPresenter->last_name = $request->last_name;
        $workshopPresenter->email = $request->email;

        if ($request->hasFile('image')) :
            $destinationPath = 'storage/presenter-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $workshopPresenter->image = '/'.$destinationPath.$fileName;
        endif;

        $workshopPresenter->save();
        return redirect()->back();
    }




    public function update($workshop_id, $presenter_id, AddWorkshopPresenterRequest $request)
    {
        $workshop = Workshop::findOrFail($workshop_id);

        $workshopPresenter = WorkshopPresenter::findOrFail($presenter_id);
        $workshopPresenter->first_name = $request->first_name;
        $workshopPresenter->last_name = $request->last_name;
        $workshopPresenter->email = $request->email;

        if ($request->hasFile('image')) :
            $image = substr($workshopPresenter->image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/presenter-images/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $workshopPresenter->image = '/'.$destinationPath.$fileName;
        endif;

        $workshopPresenter->save();
        return redirect()->back();
    }




    public function destroy($workshop_id, $presenter_id)
    {   
        $workshop = Workshop::findOrFail($workshop_id);
        $workshopPresenter = WorkshopPresenter::findOrFail($presenter_id);
        $workshopPresenter->forceDelete();
        return redirect()->back();
    }





    
}
