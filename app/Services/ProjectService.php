<?php

namespace App\Services;


use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\FrontendHelpers;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use Illuminate\Http\Concerns\InteractsWithInput;
use Illuminate\Http\Request;

class ProjectService
{

    /**
     * @param Request $request
     * @return $this
     */
    public function saveProject( Request $request )
    {
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

            $model->copyEditings()->update([
                'user_id' => $request->user_id
            ]);
        }

        return $model->load('user');
    }

    /**
     * @param Request $request
     * @return ProjectActivity
     */
    public function saveActivity( Request $request )
    {
        $model = $request->id ? ProjectActivity::find($request->id) : new ProjectActivity();
        $model->activity = $request->activity;
        $model->project_id = $request->project_id ?: NULL;
        $model->description = $request->description;
        $model->invoicing = $request->invoicing;
        $model->hourly_rate = $request->hourly_rate;
        $model->save();

        return $model;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveBook( Request $request )
    {
        $model = $request->id ? ProjectBook::find($request->id) : new ProjectBook();
        $model->project_id = $request->project_id;
        $model->user_id = $request->user_id;
        $model->book_name = $request->book_name;
        $model->isbn_hardcover_book = $request->isbn_hardcover_book;
        $model->isbn_ebook = $request->isbn_ebook;
        $model->save();

        if ($request->user_id) {
            $this->updateUserInProjectChildren($request->project_id, $request->user_id);
        }

        $project = Project::find($request->project_id)->load(['books', 'user', 'selfPublishingList']);

        return [
            'book' => $model,
            'project' => $project
        ];
    }

    /**
     * @param Request $request
     * @return string
     */
    public function saveOtherService( Request $request )
    {
        $filePath = $this->saveFile($request);
        $calculatedPrice = $this->calculateFileTextPrice($filePath, $request->is_copy_editing);

        $manuType = 'Correction';
        if ($request->is_copy_editing == 1) {
            $manuType = 'Copy Editing';
            CopyEditingManuscript::create([
                'user_id'       => $request->user_id,
                'project_id'    => $request->project_id,
                'file'          => $filePath,
                'payment_price' => $calculatedPrice,
                'editor_id'     => $request->exists('editor_id') ? $request->editor_id : NULL
            ]);
        } else {
            CorrectionManuscript::create([
                'user_id'       => $request->user_id,
                'project_id'    => $request->project_id,
                'file'          => $filePath,
                'payment_price' => $calculatedPrice,
                'editor_id'     => $request->exists('editor_id') ? $request->editor_id : NULL
            ]);
        }

        return $manuType;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function saveFile( Request $request )
    {
        $extension = $request->manuscript->extension();
        $destinationPath = 'storage/correction-manuscripts/'; // upload path

        if ($request->is_copy_editing == 1) {
            $destinationPath = 'storage/copy-editing-manuscripts/'; // upload path
        }

        $time = time();
        $fileName = $time.'.'.$extension;//$original_filename; // rename document
        $request->manuscript->move($destinationPath, $fileName);

        return $destinationPath.$fileName;
    }

    /**
     * @param $file
     * @param $is_copy_editing
     * @return int
     */
    public function calculateFileTextPrice( $file, $is_copy_editing )
    {
        $docObj = new FileToText($file);
        // count characters with space
        $word_count = strlen($docObj->convertToText()) - 2;

        $word_per_price = 1000;
        $price_per_word = 25;

        if ($is_copy_editing == 1) {
            $word_per_price = 1000;
            $price_per_word = 30;
        }

        $rounded_word       = FrontendHelpers::roundUpToNearestMultiple($word_count);
        $calculated_price   = ($rounded_word/$word_per_price) * $price_per_word;
        return $calculated_price;
    }

    /**
     * Update project relations
     * @param Request $request
     * @return mixed
     */
    public function updateUserInProjectChildren( $project_id, $user_id )
    {
        $project = Project::find($project_id);
        $project->update(['user_id' => $user_id]);
        $project->copyEditings()->update([
            'user_id' => $user_id
        ]);

        return $project;
    }
}