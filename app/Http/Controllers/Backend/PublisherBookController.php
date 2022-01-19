<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\PublisherBookCreateRequest;
use App\Http\Requests\PublisherBookUpdateRequest;
use App\PublisherBook;
use App\PublisherBookLibrary;
use Illuminate\Http\Request;

class PublisherBookController extends Controller {

    protected $publisherBook;

    public function __construct(PublisherBook $publisherBook)
    {
        $this->publisherBook = $publisherBook;
    }

    /**
     * Display the list of publisher book
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $books = $this->publisherBook->orderBy('id','DESC')->paginate(10);
        return view('backend.publisher-book.index', compact('books'));
    }

    /**
     * Display the create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $book = [
            'id'                => '',
            'title'             => '',
            'description'       => '',
            'quote_description' => '',
            'author_image'      => '',
            'book_image'        => '',
            'book_image_link'   => '',
            'display_order'     => ''
        ];
        return view('backend.publisher-book.create', compact('book'));
    }

    /**
     * Create publisher book
     * @param PublisherBookCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PublisherBookCreateRequest $request)
    {
        $requestData = $request->toArray();

        if ($request->hasFile('author_image')) :
            $destinationPath = 'storage/publisher-books/authors/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        endif;

        if ($request->hasFile('book_image')) :
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        endif;

        $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

        $book = $this->publisherBook->create($requestData);

        return redirect()->route('admin.publisher-book.edit', $book->id)->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * Display the edit page
     * @param $id
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
     * @param $id
     * @param PublisherBookUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, PublisherBookUpdateRequest $request)
    {
        if ($book = $this->publisherBook->find($id)) {
            $requestData = $request->toArray();

            if ($request->hasFile('author_image')) :
                if( \File::exists(public_path($book->author_image)) ) :
                    \File::delete(public_path($book->author_image));
                endif;
                $destinationPath = 'storage/publisher-books/authors/'; // upload path
                $extension = $request->author_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->author_image->move($destinationPath, $fileName);
                $requestData['author_image'] = '/'.$destinationPath.$fileName;
            endif;

            if ($request->hasFile('book_image')) :
                if( \File::exists(public_path($book->book_image)) ) :
                    \File::delete(public_path($book->book_image));
                endif;
                $destinationPath = 'storage/publisher-books/books/'; // upload path
                $extension = $request->book_image->extension(); // getting image extension
                $fileName = time().'.'.$extension; // renaming image
                $request->book_image->move($destinationPath, $fileName);
                $requestData['book_image'] = '/'.$destinationPath.$fileName;
            endif;

            $requestData['display_order'] = $requestData['display_order'] ? $requestData['display_order'] : 0;

            $book->update($requestData);
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book updated successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->route('admin.publisher-book.index');
    }

    /**
     * Delete the publisher book
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($book = $this->publisherBook->find($id)) {
            $author_image = public_path($book->author_image);
            $book_image = public_path($book->book_image);
            if( \File::exists($author_image) ) :
                \File::delete($author_image);
            endif;

            if( \File::exists($book_image) ) :
                \File::delete($book_image);
            endif;
            $book->forceDelete();
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book deleted successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->route('admin.publisher-book.index');
    }

    public function storeLibrary($id,  Request $request )
    {
        $requestData = $request->toArray();

        if ($request->hasFile('book_image')) :
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        endif;

        $requestData['publisher_book_id'] = $id;

        PublisherBookLibrary::create($requestData);
        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    public function updateLibrary( $id, Request $request )
    {
        $book = PublisherBookLibrary::find($id);
        $requestData = $request->toArray();

        if ($request->hasFile('book_image')) :
            if( \File::exists(public_path($book->book_image)) ) :
                \File::delete(public_path($book->book_image));
            endif;
            $destinationPath = 'storage/publisher-books/books/'; // upload path
            $extension = $request->book_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->book_image->move($destinationPath, $fileName);
            $requestData['book_image'] = '/'.$destinationPath.$fileName;
        endif;

        $book->update($requestData);
        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Publisher book updated successfully.'),
            'alert_type' => 'success'
        ]);
    }

    public function deleteLibrary( $id )
    {
        if ($book = PublisherBookLibrary::find($id)) {
            $book_image = public_path($book->book_image);

            if( \File::exists($book_image) ) :
                \File::delete($book_image);
            endif;
            $book->forceDelete();
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Publisher book deleted successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->back();
    }
}