<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePublishingRequest;
use App\Repositories\Services\PublishingService;
use Illuminate\Http\Request;

class PublishingController extends Controller {

    /**
     * Variable to store the publishing service
     * @var PublishingService
     */
    protected $publishingService;

    /**
     * PublishingController constructor.
     * @param PublishingService $publishingService
     */
    public function __construct(PublishingService $publishingService)
    {
        $this->middleware('checkPageAccess:6');
        $this->publishingService = $publishingService;
    }

    /**
     * Index page
     * @var PublishingService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if( $request->search && !empty($request->search) ) :
            $publishingHouses = $this->publishingService->search($request->search);
        else :
            $publishingHouses = $this->publishingService->paginate();
        endif;
        return view('backend.publishing.index', compact('publishingHouses'));
    }

    /**
     * Create page
     * @var PublishingService create
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $publishingHouse = $this->publishingService->fields();
        return view('backend.publishing.create', compact('publishingHouse'));
    }

    /**
     * Create new publishing house
     * @param CreatePublishingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreatePublishingRequest $request)
    {
        if ($this->publishingService->store($request->all())) {
            return redirect()->route('admin.publishing.index');
        }

        return redirect()->back();
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $publishingHouse = $this->publishingService->find($id);
        if ($publishingHouse) {
            $publishingHouse = $publishingHouse->toArray();
            return view('backend.publishing.edit', compact('publishingHouse'));
        }

        return redirect()->route('admin.publishing.index');
    }

    /**
     * Update publishing house
     * @param $id
     * @param CreatePublishingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, CreatePublishingRequest $request)
    {
        if ($this->publishingService->update($id, $request->except('_token'))) {
            return redirect()->route('admin.publishing.edit', $id);
        }
        return redirect()->route('admin.publishing.index');
    }

    /**
     * Delete a publishing house
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->publishingService->destroy($id);
        return redirect()->route('admin.publishing.index');
    }
    
}