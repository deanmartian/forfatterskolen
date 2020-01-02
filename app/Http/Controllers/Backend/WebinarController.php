<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\WebinarEmailOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWebinarRequest;
use App\Course;
use App\Webinar;
use File; 

class WebinarController extends Controller
{
   



    public function store(AddWebinarRequest $request)
    {
        $course = Course::findOrFail($request->course_id);
        Course::where('','');
        $webinar = new Webinar();
        $webinar->course_id = $course->id;
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) :
            /*
             * original code for inserting image
             *
             * $destinationPath = 'storage/webinars/'; // upload path
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
            $webinar->image = '/'.$destinationPath.$fileName;*/


            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileSize = $request->image->getClientSize();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            $largeImageLoc = 'storage/webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/webinars/thumb/'.$fileName; // upload path thumb

            if(move_uploaded_file($fileTmp, $largeImageLoc)){
                //file permission
                chmod ($largeImageLoc, 0777);

                //get dimensions of the original image
                list($width_org, $height_org) = getimagesize($largeImageLoc);

                //get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                //define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                //crop and resize image
                $newImage = imagecreatetruecolor($width_new,$height_new);

                switch($fileType) {
                    case "image/gif":
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case "image/png":
                    case "image/x-png":
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage,$source,0,0,$x,$y,$width_new,$height_new,$width,$height);

                //check if the folder exists if not then create the folder
                if (!file_exists('storage/webinars/thumb/')) {
                    File::makeDirectory('storage/webinars/thumb/', 0775, true);
                }

                switch($fileType) {
                    case "image/gif":
                        imagegif($newImage,$thumbImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        imagejpeg($newImage,$thumbImageLoc,90);
                        break;
                    case "image/png":
                    case "image/x-png":
                        imagepng($newImage,$thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                //remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }

        endif;

        $webinar->save();
        return redirect()->back();
    }



    public function update($id, AddWebinarRequest $request)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) :
           /*
            * Original Code
            *
            *  $image = substr($webinar->image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/webinars/'; // upload path
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
            $webinar->image = '/'.$destinationPath.$fileName;*/

            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileSize = $request->image->getClientSize();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            $largeImageLoc = 'storage/webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/webinars/thumb/'.$fileName; // upload path thumb

            if(move_uploaded_file($fileTmp, $largeImageLoc)){
                //file permission
                chmod ($largeImageLoc, 0777);

                //get dimensions of the original image
                list($width_org, $height_org) = getimagesize($largeImageLoc);

                //get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                //define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                //crop and resize image
                $newImage = imagecreatetruecolor($width_new,$height_new);

                switch($fileType) {
                    case "image/gif":
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case "image/png":
                    case "image/x-png":
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage,$source,0,0,$x,$y,$width_new,$height_new,$width,$height);

                //check if the folder exists if not then create the folder
                if (!file_exists('storage/webinars/thumb/')) {
                    File::makeDirectory('storage/webinars/thumb/', 0775, true);
                }

                switch($fileType) {
                    case "image/gif":
                        imagegif($newImage,$thumbImageLoc);
                        break;
                    case "image/pjpeg":
                    case "image/jpeg":
                    case "image/jpg":
                        imagejpeg($newImage,$thumbImageLoc,90);
                        break;
                    case "image/png":
                    case "image/x-png":
                        imagepng($newImage,$thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                //remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }

        endif;

        $webinar->save();
        return redirect()->back();
    }




    public function destroy($id, Request $request)
    {   
        $webinar = Webinar::findOrFail($id);
        $webinar->forceDelete();
        return redirect()->back();
    }

    public function makeReplay($webinar_id, Request $request)
    {
        $webinar = Webinar::find($webinar_id);
        if($webinar) {
            $webinar->set_as_replay = $request->set_as_replay;
            $webinar->save();
            return redirect()->back();
        }
        return redirect()->route('admin.course.index');
    }

    /**
     * Save email out for webinar
     * @param $course_id
     * @param $webinar_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function webinarEmailOut($webinar_id, $course_id, Request $request)
    {
        $webinar = Webinar::where('course_id', $course_id)->where('id', $webinar_id)->first();

        if (!$webinar) {
            return redirect()->back();
        }

        $this->validate($request, [
            'send_date' => 'required|date',
            'message' => 'required',
            'subject' => 'required'
        ]);

        $emailOut = WebinarEmailOut::firstOrNew(['course_id' => $course_id, 'webinar_id' => $webinar_id]);
        $emailOut->subject = $request->get('subject');
        $emailOut->send_date = $request->get('send_date');
        $emailOut->message = $request->get('message');
        $emailOut->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Webinar email save successfully.'),
            'alert_type' => 'success'
        ]);
    }
    
}
