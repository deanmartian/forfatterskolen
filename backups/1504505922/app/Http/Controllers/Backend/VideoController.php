<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\Video;

class VideoController extends Controller
{
   



    public function store(Request $request)
    {
        $lesson = Lesson::findOrFail($request->lesson_id);
        if( !empty($request->embed_code) ) :
            Video::create([
                'lesson_id' => $lesson->id,
                'embed_code' => $request->embed_code
            ]);
        endif;

    	return redirect()->back();
    }


    public function update($id, Request $request)
    {
        $video = Video::findOrFail($id);
        if( !empty($request->embed_code) ) :
            $video->embed_code = $request->embed_code;
            $video->save();
        endif;

        return redirect()->back();
    }



    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->forceDelete();

        return redirect()->back();
    }
}
