<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWorkshopRequest;
use App\User;
use App\Course;
use App\Workshop;
use App\WorkshopsTaken;
use File; 

class WorkshopController extends Controller
{
   
    public function index()
    {
        $workshops = Workshop::orderBy('created_at', 'desc')->paginate(25);
        return view('backend.workshop.index', compact('workshops'));
    }


    public function show($id)
    {
        $workshop = Workshop::findOrFail($id);
        return view('backend.workshop.show', compact('workshop'));
    }


    public function store(AddWorkshopRequest $request)
    {
        $workshop = new Workshop();
        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->price = $request->price;
        $workshop->date = $request->date;
        $workshop->duration = $request->duration;
        $workshop->fiken_product = $request->fiken_product;
        $workshop->seats = $request->seats;
        $workshop->location = $request->location;
        $workshop->gmap = $request->gmap;

        if ($request->hasFile('image')) :
            $destinationPath = 'storage/workshops/'; // upload path
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
            $workshop->image = '/'.$destinationPath.$fileName;
        endif;

        $workshop->save();
        return redirect(route('admin.workshop.show', $workshop->id));
    }



    public function update($id, AddWorkshopRequest $request)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->price = $request->price;
        $workshop->date = $request->date;
        $workshop->duration = $request->duration;
        $workshop->fiken_product = $request->fiken_product;
        $workshop->seats = $request->seats;
        $workshop->location = $request->location;
        $workshop->gmap = $request->gmap;

        if ($request->hasFile('image')) :
            $image = substr($workshop->image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/workshops/'; // upload path
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
            $workshop->image = '/'.$destinationPath.$fileName;
        endif;

        $workshop->save();
        return redirect()->back();
    }




    public function destroy($id, Request $request)
    {   
        $workshop = Workshop::findOrFail($id);
        $workshop->forceDelete();
        return redirect(route('admin.workshop.index'));
    }



    public function removeAttendee($workshop_taken_id, $attendee_id, Request $request)
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_taken_id);
        $user = User::findOrFail($attendee_id);
        $workshopTaken->forceDelete();
        return redirect()->back();
    }

    
}
