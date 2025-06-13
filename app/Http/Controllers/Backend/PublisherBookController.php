<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\PublisherBookCreateRequest;
use App\Http\Requests\PublisherBookUpdateRequest;
use App\PublisherBook;
use App\PublisherBookLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublisherBookController extends Controller
{
    protected $publisherBook;

    public function __construct(PublisherBook $publisherBook)
    {
        $this->publisherBook = $publisherBook;
    }

    /**
     * Display the list of publisher book
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $books = $this->publisherBook->orderBy('id', 'DESC')->get();

        return view('backend.publisher-book.index', compact('books'));
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $lastOrderedBook = $this->publisherBook->orderBy('display_order', 'DESC')->first();
        $book = [
            'id' => '',
            'title' => '',
            'description' => '',
            'quote_description' => '',
            'author_image' => '',
            'book_image' => '',
            'book_image_link' => '',
            'display_order' => $lastOrderedBook->display_order + 1,
        ];

        return view('backend.publisher-book.create', compact('book'));
    }

    /**
     * Create publisher book
     */
    public function store(PublisherBookCreateRequest $request): RedirectResponse
    {
        $requestData = $request->toArray();

        if ($request->hasFile('author_image')) {
            $destinationPath = 'storage/publisher-books/authors/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('book_image')) {
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        }

        $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

        $display_order = $requestData['display_order'];
        $book = $this->publisherBook->create($requestData);
        $this->updateDisplayOrder($display_order, $book->id);

        return redirect()->route('admin.publisher-book.edit', $book->id)->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Display the edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        if ($book = $this->publisherBook->find($id)) {
            $book['libraries'] = $book->libraries;

            return view('backend.publisher-book.edit', compact('book'));
        }

        return redirect()->route('admin.publisher-book.index');
    }

    /**
     * Update a publisher book
     */
    public function update($id, PublisherBookUpdateRequest $request): RedirectResponse
    {
        if ($book = $this->publisherBook->find($id)) {
            $requestData = $request->toArray();

            if ($request->hasFile('author_image')) {
                if (\File::exists(public_path($book->author_image))) {
                    \File::delete(public_path($book->author_image));
                }
                $destinationPath = 'storage/publisher-books/authors/'; // upload path
                $extension = $request->author_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->author_image->move($destinationPath, $fileName);
                $requestData['author_image'] = '/'.$destinationPath.$fileName;
            }

            if ($request->hasFile('book_image')) {
                if (\File::exists(public_path($book->book_image))) {
                    \File::delete(public_path($book->book_image));
                }
                $destinationPath = 'storage/publisher-books/books/'; // upload path
                $extension = $request->book_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->book_image->move($destinationPath, $fileName);
                $requestData['book_image'] = '/'.$destinationPath.$fileName;
            }

            $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

            $display_order = $requestData['display_order'];
            $book->update($requestData);
            $this->updateDisplayOrder($display_order, $id);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.publisher-book.index');
    }

    /**
     * Delete the publisher book
     */
    public function destroy($id): RedirectResponse
    {
        if ($book = $this->publisherBook->find($id)) {
            $author_image = public_path($book->author_image);
            $book_image = public_path($book->book_image);
            if (\File::exists($author_image)) {
                \File::delete($author_image);
            }

            if (\File::exists($book_image)) {
                \File::delete($book_image);
            }
            $book->forceDelete();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.publisher-book.index');
    }

    public function storeLibrary($id, Request $request): RedirectResponse
    {
        $requestData = $request->toArray();

        if ($request->hasFile('book_image')) {
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        }

        $requestData['publisher_book_id'] = $id;

        PublisherBookLibrary::create($requestData);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function updateLibrary($id, Request $request): RedirectResponse
    {
        $book = PublisherBookLibrary::find($id);
        $requestData = $request->toArray();

        if ($request->hasFile('book_image')) {
            if (\File::exists(public_path($book->book_image))) {
                \File::delete(public_path($book->book_image));
            }
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        }

        $book->update($requestData);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteLibrary($id): RedirectResponse
    {
        if ($book = PublisherBookLibrary::find($id)) {
            $book_image = public_path($book->book_image);

            if (\File::exists($book_image)) {
                \File::delete($book_image);
            }
            $book->forceDelete();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Update the sequence of display order
     */
    public function updateDisplayOrder($display_order, $id)
    {
        while ($publishedBook = PublisherBook::where('display_order', $display_order)->where('id', '!=', $id)->first()) {
            $lastBook = PublisherBook::orderBy('display_order', 'DESC')->first();
            if ($publishedBook && $publishedBook->id !== $lastBook->id) {
                $publishedBook->display_order = $display_order + 1;
                $publishedBook->save();
            } else {
                $lastDisplay = PublisherBook::where('display_order', $display_order)->get();
                // check if last display order is more than 1
                if ($lastDisplay->count() > 1) {
                    $lastBook->display_order = $lastBook->display_order + 1;
                    $lastBook->save();
                }
            }

            $display_order++;
        }
    }
}
