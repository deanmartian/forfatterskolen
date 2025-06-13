<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $lesson = Lesson::findOrFail($request->lesson_id);
        if (! empty($request->embed_code)) {
            Video::create([
                'lesson_id' => $lesson->id,
                'embed_code' => $request->embed_code,
            ]);
        }

        return redirect()->back();
    }

    public function update($id, Request $request): RedirectResponse
    {
        $video = Video::findOrFail($id);
        if (! empty($request->embed_code)) {
            $video->embed_code = $request->embed_code;
            $video->save();
        }

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        $video = Video::findOrFail($id);
        $video->forceDelete();

        return redirect()->back();
    }
}
