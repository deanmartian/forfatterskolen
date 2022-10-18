<?php

namespace App\Http\Controllers\Backend;


use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use App\TimeRegister;
use App\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $learners =  User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $activities = ProjectActivity::all();
        $projects = Project::all();
        return view('backend.project.index', compact('learners', 'activities', 'projects'));
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['books', 'user', 'selfPublishingList']);
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->where('is_self_publishing_learner', 1)->get();
        $timeRegisters = TimeRegister::where('user_id', $project->user_id)->whereNull('project_id')->with('project')->get();
        $projectTimeRegisters = TimeRegister::where('project_id', $project->id)->with('project')->get();
        return view('backend.project.show', compact('project', 'editors', 'learners', 'timeRegisters',
            'projectTimeRegisters'));
    }

    public function saveProject( Request $request )
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        if (!$request->id) {
            $this->validate($request, [
                'number' => 'required|unique:projects,identifier'
            ]);
        }

        $model = $request->id ? Project::find($request->id) : new Project();
        $model->user_id = $request->user_id;
        $model->name = $request->name;
        $model->identifier = $request->number;
        $model->activity_id = $request->activity_id;
        $model->start_date = $request->start_date;
        $model->end_date = $request->end_date;
        $model->description = $request->description;
        $model->is_finished = $request->is_finished;
        $model->save();

        if ($request->user_id) {
            $model->books()->update([
                'user_id' => $request->user_id
            ]);
        }

        return $model;
    }

    public function deleteProject( $project_id )
    {
        $project = Project::find($project_id);

        $activity = ProjectActivity::where('project_id', $project_id)->update([
            'project_id' => NULL
        ]);

        $project->delete();
        return response()->json();
    }

    public function saveActivity( Request $request )
    {
        $this->validate($request, [
            'activity' => 'required'
        ]);

        $model = $request->id ? ProjectActivity::find($request->id) : new ProjectActivity();
        $model->activity = $request->activity;
        $model->project_id = $request->project_id ?: NULL;
        $model->description = $request->description;
        $model->invoicing = $request->invoicing;
        $model->hourly_rate = $request->hourly_rate;
        $model->save();

        return $model;
    }

    public function deleteActivity( $id )
    {
        ProjectActivity::find($id)->delete();
        return response()->json();
    }

    public function saveNote( $project_id, Request $request )
    {
        $project = Project::find($project_id);
        $project->notes = $request->notes;
        $project->save();

        return response()->json($project);
    }

    public function saveBook( $project_id, Request $request )
    {
        $this->validate($request, [
            'book_name' => 'required'
        ]);

        $model = $request->id ? ProjectBook::find($request->id) : new ProjectBook();
        $model->project_id = $project_id;
        $model->user_id = $request->user_id;
        $model->book_name = $request->book_name;
        $model->isbn_hardcover_book = $request->isbn_hardcover_book;
        $model->isbn_ebook = $request->isbn_ebook;
        $model->save();

        if ($request->user_id) {
            $project = Project::find($project_id);
            $project->update(['user_id' => $request->user_id]);
            $project->copyEditings()->update([
                'user_id' => $request->user_id
            ]);
        }

        $project = Project::find($project_id)->load(['books', 'user', 'selfPublishingList']);

        return response()->json([
            'book' => $model,
            'project' => $project
        ]);
    }

    public function deleteBook( $id )
    {
        ProjectBook::find($id)->delete();
        return response()->json();
    }

    /**
     * Add to correction or copy editing
     * @param $project_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addOtherService($project_id, Request $request)
    {
        if ($project = Project::find($project_id)) {
            $data = $request->except('_token');

            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back()->with([
                        'alert_type' => 'danger',
                        'errors' => AdminHelpers::createMessageBag('File type not allowed.'),
                    ]);
                endif;

                $destinationPath = 'storage/correction-manuscripts/'; // upload path

                if ($data['is_copy_editing'] == 1) {
                    $destinationPath = 'storage/copy-editing-manuscripts/'; // upload path
                }

                $time = time();
                $fileName = $time.'.'.$extension;//$original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;

                $word_per_price = 1000;
                $price_per_word = 25;

                if ($data['is_copy_editing'] == 1) {
                    $word_per_price = 1000;
                    $price_per_word = 30;
                }

                $rounded_word       = FrontendHelpers::roundUpToNearestMultiple($word_count);
                $calculated_price   = ($rounded_word/$word_per_price) * $price_per_word;
                $data['price']      = $calculated_price;


                $manuType = 'Correction';
                if ($data['is_copy_editing'] == 1) {
                    $manuType = 'Copy Editing';
                    CopyEditingManuscript::create([
                        'user_id'       => $project->user_id,
                        'project_id'    => $project_id,
                        'file'          => $file,
                        'payment_price' => $data['price'],
                        'editor_id'     => $request->exists('editor_id') ? $data['editor_id'] : NULL
                    ]);
                } else {
                    CorrectionManuscript::create([
                        'user_id'       => $project->user_id,
                        'project_id'    => $project_id,
                        'file'          => $file,
                        'payment_price' => $data['price'],
                        'editor_id'     => $request->exists('editor_id') ? $data['editor_id'] : NULL
                    ]);
                }

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag($manuType.' Manuscript added successfully.'),
                    'alert_type' => 'success',
                    'not-former-courses' => true
                ]);
            endif;

        }

        return redirect()->back();
    }

    public function graphicWork( $project_id )
    {
        $project = Project::find($project_id);
        return view('backend.project.graphic-work', compact('project'));
    }

    public function registration( $project_id )
    {
        $project = Project::find($project_id);
        return view('backend.project.registration', compact('project'));
    }

    public function marketing( $project_id )
    {
        $project = Project::find($project_id);
        return view('backend.project.marketing', compact('project'));
    }
}