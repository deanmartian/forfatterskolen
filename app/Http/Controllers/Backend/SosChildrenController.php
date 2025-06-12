<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\SosChildren;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SosChildrenController extends Controller
{
    /**
     * Storage of SosChildren
     *
     * @var SosChildren|string
     */
    protected $sosChildren = '';

    /**
     * SosChildrenController constructor.
     */
    public function __construct(SosChildren $sosChildren)
    {
        $this->sosChildren = $sosChildren;
    }

    /**
     * Display index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $hasMainDescription = $this->sosChildren->getMainDescription();
        $primaryVideo = $this->sosChildren->getPrimaryVideo();
        $documents = $this->sosChildren->getVideoRecords();

        return view('backend.sos-children.index', compact('hasMainDescription', 'primaryVideo',
            'documents'));
    }

    /**
     * For adding/editing the main description
     */
    public function editMainDescription(Request $request): RedirectResponse
    {
        $data = $request->except('_token');

        $validator = \Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (is_numeric($request->get('id'))) {
            $sosChildren = $this->sosChildren->find($request->get('id'));
            if ($sosChildren) {
                $sosChildren->description = $data['description'];
                $sosChildren->bottom_description = $data['bottom_description'];
                $sosChildren->save();

                return redirect()->route('admin.sos-children.index')->with(['errors' => AdminHelpers::createMessageBag('Main description updated successfully.'),
                    'alert_type' => 'success']);
            }
        } else {
            $data['title'] = 'Main Description';
            $data['is_main_description'] = 1;
            $this->sosChildren->create($data);

            return redirect()->route('admin.sos-children.index')->with(['errors' => AdminHelpers::createMessageBag('Main description updated successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $document = [
            'id' => '',
            'title' => old('title'),
            'description' => old('description'),
            'video_url' => old('description_simplemde'),
        ];

        $primaryVideo = $this->sosChildren->getPrimaryVideo();

        return view('backend.sos-children.create', compact('document', 'primaryVideo'));
    }

    /**
     * Create new document
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;

        if ($this->sosChildren->create($data)) {
            return redirect()->route('admin.sos-children.index')->with(['errors' => AdminHelpers::createMessageBag('Document created successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Display the edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $sosChildren = $this->sosChildren->find($id);
        if ($sosChildren) {
            $primaryVideo = $this->sosChildren->getPrimaryVideo();
            $document = $sosChildren->toArray();

            return view('backend.sos-children.edit', compact('document', 'primaryVideo'));
        }

        return redirect()->route('admin.sos-children.index');
    }

    /**
     * Update the document
     */
    public function update($id, Request $request): RedirectResponse
    {
        $sosChildren = $this->sosChildren->find($id);
        if ($sosChildren) {
            $data = $request->except('_token');
            $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;
            $sosChildren->update($data);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Document updated successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Delete document
     */
    public function destroy($id): RedirectResponse
    {
        $sosChildren = $this->sosChildren->find($id);
        if ($sosChildren) {
            $sosChildren->delete();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Document deleted successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Display the edit description page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEditMainDescription(): View
    {
        $hasMainDescription = $this->sosChildren->getMainDescription();

        return view('backend.sos-children.main-description', compact('hasMainDescription'));
    }
}
