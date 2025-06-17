<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Poem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PoemController extends Controller
{
    /**
     * Display index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $poems = Poem::orderBy('created_at', 'DESC')->paginate(15);

        return view('backend.poem.index', compact('poems'));
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $poem = [
            'id' => '',
            'title' => '',
            'poem' => '',
            'author_image' => '',
            'author' => '',
        ];

        return view('backend.poem.create', compact('poem'));
    }

    /**
     * Create new poem
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'poem' => 'required',
            'author' => 'required|alpha_spaces',
        ]);

        $requestData = $request->toArray();

        if ($request->hasFile('author_image')) {
            $destinationPath = 'storage/poem/authors/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        }

        Poem::create($requestData);

        return redirect()->route('admin.poem.index')->with([
            'errors' => AdminHelpers::createMessageBag('Poem created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Display edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $poem = Poem::find($id);
        if ($poem) {
            $poem = $poem->toArray();

            return view('backend.poem.edit', compact('poem'));
        }

        return redirect()->route('admin.poem.index');
    }

    public function update($id, Request $request): RedirectResponse
    {
        $poem = Poem::find($id);
        if ($poem) {
            $requestData = $request->toArray();
            if ($request->hasFile('author_image')) {
                if (\File::exists(public_path($poem->author_image))) {
                    \File::delete(public_path($poem->author_image));
                }

                $destinationPath = 'storage/poem/authors/'; // upload path
                $extension = $request->author_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->author_image->move($destinationPath, $fileName);
                $requestData['author_image'] = '/'.$destinationPath.$fileName;
            }

            $poem->update($requestData);

            return redirect()->route('admin.poem.index')->with([
                'errors' => AdminHelpers::createMessageBag('Poem updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.poem.index');
    }

    public function destroy($id): RedirectResponse
    {
        $poem = Poem::find($id);
        if ($poem) {
            if (\File::exists(public_path($poem->author_image))) {
                \File::delete(public_path($poem->author_image));
            }
            $poem->delete();

            return redirect()->route('admin.poem.index')->with([
                'errors' => AdminHelpers::createMessageBag('Poem deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.poem.index');
    }
}
