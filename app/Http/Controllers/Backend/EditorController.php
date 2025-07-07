<?php

namespace App\Http\Controllers\Backend;

use App\Assignment;
use App\AssignmentManuscript;
use App\AssignmentManuscriptEditorCanTake;
use App\Editor;
use App\EditorAssignmentPrices;
use App\EditorGenrePreferences;
use App\Genre;
use App\HiddenEditor;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditorCreateRequest;
use App\Http\Requests\EditorUpdateRequest;
use App\ManuscriptEditorCanTake;
use App\User;
use Carbon\Carbon;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EditorController extends Controller
{
    /**
     * Display all editors
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $editors = Editor::paginate(15);

        return view('backend.editor.index', compact('editors'));
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $editor = [
            'name' => old('name'),
            'description' => old('description'),
            'editor_image' => '',
        ];

        return view('backend.editor.create', compact('editor'));
    }

    /**
     * Create new editor
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(EditorCreateRequest $request): RedirectResponse
    {
        $editor = new Editor;
        $editor->name = $request->name;
        $editor->description = $request->description;

        if ($request->hasFile('editor_image')) {
            $destinationPath = 'images/editors'; // upload path
            $extension = $request->editor_image->extension(); // getting image extension
            $uploadedFile = $request->editor_image->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->editor_image->move($destinationPath, $fileName);

            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($fileName);
                imagepng($image, $fileName, 9);
            } else {
                $image = imagecreatefromjpeg($fileName);
                imagejpeg($image, $fileName, 70);
            }
            $editor->editor_image = '/'.$fileName;
        }

        $editor->save();

        return redirect('/editor');
    }

    /**
     * Display edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id): View
    {
        $editor = Editor::findOrFail($id)->toArray();

        return view('backend.editor.edit', compact('editor'));
    }

    /**
     * Update the editor
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, EditorUpdateRequest $request): RedirectResponse
    {
        $editor = Editor::find($id);
        if ($editor) {
            $editor->name = $request->name;
            $editor->description = $request->description;

            if ($request->hasFile('editor_image')) {
                $destinationPath = 'images/editors'; // upload path
                $extension = $request->editor_image->extension(); // getting image extension
                $uploadedFile = $request->editor_image->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                $request->editor_image->move($destinationPath, $fileName);

                // optimize image
                if (strtolower($extension) == 'png') {
                    $image = imagecreatefrompng($fileName);
                    imagepng($image, $fileName, 9);
                } else {
                    $image = imagecreatefromjpeg($fileName);
                    imagejpeg($image, $fileName, 70);
                }
                $editor->editor_image = '/'.$fileName;
            }

            $editor->save();
        }

        return redirect('/editor');
    }

    /**
     * Delete the editor
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id): RedirectResponse
    {
        $editor = Editor::find($id);
        if ($editor) {
            $image = substr($editor->editor_image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $editor->forceDelete();
        }

        return redirect('/editor');
    }

    public function total($editor_id)
    {
        $pAssgn = DB::select("call editor_total_worked_personal_assignment($editor_id, null, null)");
        $shpMan = DB::select("call editor_total_worked_shop_manuscript($editor_id, null, null)");
        $gAssgn = DB::select("call editor_total_worked_group_assignment($editor_id, null, null)");
        $chngTmr = DB::select("call editor_total_worked_coaching($editor_id, null, null)");
        $crrctn = DB::select("call editor_total_worked_correction($editor_id, null, null)");
        $cpyEdtng = DB::select("call editor_total_worked_copy_editing($editor_id, null, null)");
        $all = array_merge($pAssgn, $shpMan, $gAssgn, $chngTmr, $crrctn, $cpyEdtng);

        if (! $all) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('No data found.'),
                'alert_type' => 'warning',
            ]);
        }

        $year_month = 'year_month';

        $maxYearMonth = max(array_map(function ($o) use ($year_month) {
            return $o->$year_month;
        },
            $all));

        $minYearMonth = min(array_map(function ($o) use ($year_month) {
            return $o->$year_month;
        },
            $all));

        $minYear = substr($minYearMonth, 0, 4);
        $minMonth = substr($minYearMonth, -2);
        $maxYear = substr($maxYearMonth, 0, 4);
        $maxMonth = substr($maxYearMonth, -2);

        $var = [
            'minYear' => $minYear,
            'minMonth' => $minMonth,
            'maxYear' => $maxYear,
            'maxMonth' => $maxMonth,
        ];

        $data = [
            'pAssgn' => $pAssgn,
            'shpMan' => $shpMan,
            'gAssgn' => $gAssgn,
            'chngTmr' => $chngTmr,
            'crrctn' => $crrctn,
            'cpyEdtng' => $cpyEdtng,
        ];

        $editor = User::find($editor_id)->FullName;
        $prices = EditorAssignmentPrices::all();

        $assgnPrice = 0;
        $shpManPrice = 0;
        $chngTmrPrice = 0;
        $crrctnPrice = 0;
        $cpyEdtngPrice = 0;
        foreach ($prices as $key) {
            if ($key->assignment == 'Assignment') {
                $assgnPrice = $key->price;
            } elseif ($key->assignment == 'Shop Manuscript') {
                $shpManPrice = $key->price;
            } elseif ($key->assignment == 'Coaching Timer') {
                $chngTmrPrice = $key->price;
            } elseif ($key->assignment == 'Correction') {
                $crrctnPrice = $key->price;
            } elseif ($key->assignment == 'Copy Editing') {
                $cpyEdtngPrice = $key->price;
            }
        }

        $price = [
            'assgnPrice' => $assgnPrice,
            'shpManPrice' => $shpManPrice,
            'chngTmrPrice' => $chngTmrPrice,
            'crrctnPrice' => $crrctnPrice,
            'cpyEdtngPrice' => $cpyEdtngPrice,
        ];

        return view('backend.admin.total_editor_worked', compact('editor', 'var', 'data', 'editor', 'price'));
    }

    public function settings(): View
    {

        $manuscriptEditorCanTake = ManuscriptEditorCanTake::where('editor_id', Auth::user()->id)
            ->orderBy('date_from', 'asc')
            ->get();

        $genrePrefrences = EditorGenrePreferences::where('editor_id', Auth::user()->id)->get();
        $genreIHaveNotSelected = Genre::whereNotIn('id', function ($query) {
            $query->select('genre_id')->from('editor_genre_preferences')->where('editor_id', Auth::user()->id);
        })
            ->get();

        // get the newly added assignments
        $assignmentsBeforeEditorDeadline = Assignment::where('editor_expected_finish', '>=', Carbon::now())
            ->where('for_editor', 0)
            ->whereNull('parent')
            ->get();

        return view('editor.editor-settings', compact('manuscriptEditorCanTake', 'genrePrefrences', 'genreIHaveNotSelected', 'assignmentsBeforeEditorDeadline'));
    }

    public function saveGenrePrefences($fromAdmin, Request $request): RedirectResponse
    {

        if ($request->genre_id) {
            if ($fromAdmin) {
                $data['editor_id'] = $request->editor_id;
            } else {
                $data['editor_id'] = Auth::user()->id;
            }

            $data['genre_id'] = $request->genre_id;
            EditorGenrePreferences::create($data);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Genre preference saved successfully'),
                'alert_type' => 'success']);

        }

        return redirect()->back();

    }

    public function deleteGenrePreferences($id): RedirectResponse
    {
        EditorGenrePreferences::find($id)->delete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
            'alert_type' => 'success']);
    }

    public function saveAssignmentManuscriptEditorCanTake($id, $assignment_manu_id, Request $request): RedirectResponse
    {
        if (! $request->has('how_many_you_can_take')) {
            return redirect()->back();
        }
        if ($id) { // edit
            $assignmentManuscriptEditorCanTake = AssignmentManuscriptEditorCanTake::find($id);
            $assignmentManuscriptEditorCanTake->how_many_you_can_take = $request->how_many_you_can_take;
            $assignmentManuscriptEditorCanTake->editor_id = Auth::user()->id;
            $assignmentManuscriptEditorCanTake->save();
        } else {
            $data['how_many_you_can_take'] = $request->how_many_you_can_take;
            $data['editor_id'] = Auth::user()->id;
            $data['assignment_manuscript_id'] = $assignment_manu_id;
            AssignmentManuscriptEditorCanTake::create($data);
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success']);
    }

    public function hideShowEditor($editor_id, $hide, Request $request): RedirectResponse
    {
        $dateEnd = null;
        if (! $request->hideUntilTurnedBackUnhidden) {
            $dateEnd = $request->end_date;
        }

        HiddenEditor::create([
            'editor_id' => $editor_id,
            'hide_date_from' => $request->start_date,
            'hide_date_to' => $dateEnd,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record successfully saved.'),
            'alert_type' => 'success']);
    }

    public function showEditorHidden($editor_id): View
    {

        $editor = User::find($editor_id);
        $hiddenEditor = HiddenEditor::where('editor_id', $editor_id)->get();
        $assignmentManuscript = AssignmentManuscript::where('editor_id', $editor_id)->get()->pluck('id');
        $assignment = Assignment::whereIn('id', $assignmentManuscript)->orderBy('created_at', 'desc')->get();

        return view('editor.editor-hidden', compact('hiddenEditor', 'editor', 'assignmentManuscript', 'assignment'));

    }

    public function deleteEditorHidden($id): RedirectResponse
    {
        HiddenEditor::find($id)->delete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Deleted successfully.'),
            'alert_type' => 'success']);
    }

    public function setHowManyManuscriptYouCanTake($id, Request $request): RedirectResponse
    {
        $assignmentManuscriptEditorCanTake = AssignmentManuscriptEditorCanTake::find($id);
        $assignmentManuscriptEditorCanTake->how_many_you_can_take = $request->howManyManuscriptYouCanTake;
        $assignmentManuscriptEditorCanTake->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record successfully saved.'),
            'alert_type' => 'success']);
    }
}
