<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
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
use App\Http\TFPDF\TFPDF;

class WorkshopController extends Controller
{

    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:3');
    }
   
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
        $workshop->is_free = $request->is_free == 'on' ? 1 : 0;

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
        $workshop->is_free = $request->is_free == 'on' ? 1 : 0;

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

    /**
     * Update the email to be sent when approved for this workshop
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_email($id, Request $request)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->email_title = $request->email_title;
        $workshop->email_body = $request->email_body;
        $workshop->save();
        return redirect()->back();
    }

    public function removeAttendee($workshop_taken_id, $attendee_id, Request $request)
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_taken_id);
        $user = User::findOrFail($attendee_id);
        $workshopTaken->forceDelete();
        return redirect()->back();
    }


    public function downloadAttendees($id)
    {
        $workshop = Workshop::findOrFail($id);
        $pdf = new TFPDF();
        $pdf->AddPage();

        foreach( $workshop->taken as $taken ) :
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0, 5, $taken->user->full_name, 0, 1);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0, 5, 'Menu: '.$taken->menu->title, 0, 1);
            $pdf->Cell(0, 5, 'Notes: '.$taken->notes, 0, 1);
        endforeach;



   
        
        $pdf->Output($workshop->title.'-attendees.pdf', 'D');
    }

    /**
     * Send email to the attendees of the workshop
     * @param $id int workshop id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmailToAttendees($id, Request $request)
    {
        $workshop = Workshop::find($id);
        if ($workshop) {
            $attendees = $workshop->attendees;
            $subject    = $request->subject;
            $message    = $request->message;
            $from       = 'post@forfatterskolen.no';
            foreach ($attendees as $attendee) {
                $email = $attendee->user->email;
                //AdminHelpers::send_mail($email, $subject, $message, $from);
                AdminHelpers::send_email($subject, $from, $email, $message);
            }
        }

        return redirect()->back();
    }

    /**
     * Update the status of the workshop
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {

        $workshop = Workshop::find($request->workshop_id);
        $success = false;

        if ($workshop) {
            $workshop->is_active = $request->is_active;
            $workshop->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public function updateForSaleStatus(Request $request)
    {
        $workshop = Workshop::find($request->workshop_id);
        $success = false;

        if ($workshop) {
            $workshop->is_free = $request->is_free;
            $workshop->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

}
