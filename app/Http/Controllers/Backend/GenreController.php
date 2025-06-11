<?php

namespace App\Http\Controllers\Backend;

use App\Genre;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $genres = Genre::all();

        return view('backend.genre.index', compact('genres'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Genre::create($request->except('_token'));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Genre created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $genre = Genre::find($id);
        $genre->update($request->except('_token'));
        $genre->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Genre updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function destroy($id)
    {
        $genre = Genre::find($id);
        $genre->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Genre deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }
}
