<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptTakenFeedback;
use Validator; 
use Illuminate\Support\Str;

class ShopManuscriptController extends Controller
{
   


    public function index(Request $request)
    {
        if( $request->tab == 'sold' ) :
            $shopManuscripts = ShopManuscriptsTaken::orderBy('created_at', 'desc')->paginate(15);
        else :
            $shopManuscripts = ShopManuscript::orderBy('created_at', 'desc')->paginate(15);
        endif;
        return view('backend.shop-manuscript.index', compact('shopManuscripts'));
    }   



    public function store(Request $request)
    {
        $validator = $this->validator($request->all());
        if( $validator->fails() ) return redirect()->back()->withInput()->withErrors($validator);

        $shopManuscript = new ShopManuscript();
        $shopManuscript->title = $request->title;
        $shopManuscript->description = $request->description;
        $shopManuscript->max_words = $request->max_words;
        $shopManuscript->price = $request->price;
        $shopManuscript->split_payment_price = $request->split_payment_price;
        $shopManuscript->fiken_product = $request->fiken_product;
        $shopManuscript->save();
        return redirect()->back();
    }



    public function update($id, Request $request)
    {
        $validator = $this->validator($request->all());
        if( $validator->fails() ) return redirect()->back()->withInput()->withErrors($validator);

        $shopManuscript = ShopManuscript::findOrFail($id);
        $shopManuscript->title = $request->title;
        $shopManuscript->description = $request->description;
        $shopManuscript->max_words = $request->max_words;
        $shopManuscript->price = $request->price;
        $shopManuscript->split_payment_price = $request->split_payment_price;
        $shopManuscript->fiken_product = $request->fiken_product;
        $shopManuscript->save();
        return redirect()->back();
    }



    public function destroy($id)
    {
        $shopManuscript = ShopManuscript::findOrFail($id);
        $shopManuscript->forceDelete();
        return redirect()->back();
    }


    public function validator($data)
    {
        return Validator::make($data, [
            'title'                 => 'required|string',
            'description'           => 'required|string',
            'max_words'             => 'required|integer',
            'price'                 => 'required|numeric',
            'split_payment_price'   => 'required|numeric',
            'fiken_product'         => 'required|string',
        ]);
    }



    public function addFeedback($shopManuscriptTakenID, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        if( $request->hasFile('files') && $shopManuscriptTaken->feedbacks->count() == 0 ) :
            $files = [];
            foreach( $request->file('files') as $file ) :
                $time = Str::random(10).'-'.time();
                $destinationPath = 'storage/shop-manuscript-taken-feedbacks/'; // upload path
                $extension = $file->getClientOriginalExtension(); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $file->move($destinationPath, $fileName);
                $files[] = '/'.$destinationPath.$fileName;
            endforeach;

            ShopManuscriptTakenFeedback::create([
                'shop_manuscript_taken_id' => $shopManuscriptTaken->id,
                'filename' => json_encode($files),
                'notes' => $request->notes
            ]);
        endif;

        return redirect()->back();
    }
    
    public function destroyFeedback($id)
    {
        $feedback = ShopManuscriptTakenFeedback::findOrFail($id);
        $feedback->forceDelete();
        return redirect()->back();
    }


    
    public function assignEditor($shopManuscriptTakenID, Request $request)
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        $shopManuscriptTaken->feedback_user_id = $request->feedback_user_id;
        $shopManuscriptTaken->save();
        return redirect()->back();
    }
    
}
