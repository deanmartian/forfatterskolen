<?php

namespace App\Services;


use App\Contract;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Project;
use App\ProjectActivity;
use App\ProjectBook;
use App\ProjectBookFormatting;
use App\ProjectBookPicture;
use App\ProjectGraphicWork;
use App\ProjectMarketing;
use App\ProjectWholeBook;
use Carbon\Carbon;
use Illuminate\Http\Concerns\InteractsWithInput;
use Illuminate\Http\Request;

class ProjectService
{

    /**
     * @param Request $request
     * @return $this|mixed
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
        $model->status = $request->status;
        $model->notes = NULL;
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

    public function saveBookPicture( Request $request )
    {
        if ($request->hasFile('images')) :
            $destinationPath = 'storage/project-book-pictures'; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveMultipleFileOrImage($destinationPath, 'images');

            if ($request->id) {
                $bookPicture = ProjectBookPicture::find($request->id);
                $bookPicture->image = $filePath;
                $bookPicture->description = $request->description;
                $bookPicture->save();
            } else {
                foreach (explode(', ', $filePath) as $picture) {
                    ProjectBookPicture::create([
                        'project_id' => $request->project_id,
                        'image' => $picture,
                        'description' => $request->description
                    ]);
                }
            }
        endif;
    }

    public function saveBookFormatting( Request $request )
    {

        $filePath = NULL;

        if ($request->hasFile('file')) :
            $destinationPath = 'storage/project-book-formatting'; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImage($destinationPath, 'file');
        endif;

        if ($request->id) {

            $bookPicture = ProjectBookFormatting::find($request->id);
            $bookPicture->file = $filePath;
            $bookPicture->save();

        } else {

            ProjectBookFormatting::create([
                'project_id' => $request->project_id,
                'file' => $filePath
            ]);

        }

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

        if ($request->type == 1) {
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

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function saveGraphicWorks( Request $request )
    {
        $data = $request->except('_token');

        switch ($request->type) {
            case 'cover':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'cover');
                $data['description'] = $request->description;
                $data['is_checked'] = $request->has('is_approved') && $request->is_approved ? 1 : 0;
                break;

            case 'barcode':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'barcode');
                $data['date'] = Carbon::today();
                $data['is_checked'] = $request->has('is_sent') && $request->is_sent ? 1 : 0;
                break;

            case 'rewrite-script':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'rewrite_script');
                break;

            case 'trial-page':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'trial_page');
                break;

            case 'sample-book-pdf':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'sample_book_pdf');
                break;
        }

        if ($request->id) {
            $graphicWork = ProjectGraphicWork::find($request->id);
            $graphicWork->update($data);
        } else {
            $graphicWork = ProjectGraphicWork::create($data);
        }

        return $graphicWork;
    }

    /**
     * @param Request $request
     * @param $fieldName
     * @return null|string
     */
    public function saveGraphicWorkFileOrImage( Request $request, $fieldName)
    {
        $filePath = NULL;

        if ($request->id) {
            $graphicWork = ProjectGraphicWork::find($request->id);
            $filePath = $graphicWork->value;
        }

        if ($request->hasFile($fieldName)) :
            $destinationPath = 'storage/project-graphic-work/' . $fieldName; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImage($destinationPath, $fieldName);

        endif;

        return $filePath;
    }

    public function uploadWholeBook( Request $request )
    {
        $filePath = NULL;

        if ($request->id) {
            $wholeBook = ProjectWholeBook::find($request->id);
            $filePath = $wholeBook->book_content;
        }

        if ($request->hasFile('book_file')) :
            $destinationPath = 'storage/project-books'; // upload path

            if ($request->has('is_book_critique')) {
                $destinationPath = 'storage/project-book-critique'; // upload path
            }

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImage($destinationPath, 'book_file');

        endif;

        return $filePath;
    }

    public function uploadFeedback(Request $request)
    {
        $filePath = NULL;
        $destinationPath = 'storage/project-book-critiques'; // upload path
        AdminHelpers::createDirectory($destinationPath);
        $filePath = $this->saveFileOrImage($destinationPath, 'feedback');
        return $filePath;
    }

    /**
     * @param Request $request
     * @param $fieldName
     * @return null|string
     */
    public function saveMarketingFileOrImage( Request $request, $fieldName)
    {
        $filePath = NULL;

        if ($request->has('id') && $request->id) {
            $marketing = ProjectMarketing::find($request->id);
            $filePath = $marketing->value;
        }

        if ($request->hasFile($fieldName)) :
            $destinationPath = 'storage/project-marketing/' . $fieldName; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImage($destinationPath, $fieldName);

        endif;

        return $filePath;
    }

    /**
     * @param $destinationPath
     * @param $requestFile
     * @return string
     */
    public function saveFileOrImage( $destinationPath, $requestFilename )
    {
        $requestFile = \request()->file($requestFilename);
        $extension = $requestFile->getClientOriginalExtension();
        $original_filename = $requestFile->getClientOriginalName();
        $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

        $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
        $requestFile->move($destinationPath, $fileName);
        return '/'.$fileName;
    }

    /**
     * @param $destinationPath
     * @param $requestFile
     * @return string
     */
    public function saveMultipleFileOrImage( $destinationPath, $requestFilename )
    {
        $filesWithPath = '';
        foreach (\request()->file($requestFilename) as $k => $file) {
            $extension = pathinfo($_FILES[$requestFilename]['name'][$k],PATHINFO_EXTENSION);
            $original_filename = $file->getClientOriginalName();
            $filename = pathinfo($original_filename, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $filename, $extension);
            $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $filename, $extension).", ";

            $file->move($destinationPath, $fileName);
        }

        return $filesWithPath = trim($filesWithPath,", ");
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function uploadContract( Request $request )
    {
        $data = $request->except('_token');
        if ($request->hasFile('sent_file')) :
            $destinationPath = 'storage/contract-sent-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['sent_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        endif;

        if ($request->hasFile('signed_file')) :
            $destinationPath = 'storage/contract-signed-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['signed_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
            $data['signed_date'] = Carbon::now();
            $data['signature'] = 'Signed';
        endif;

        $data['is_file'] = 1;

        if ($request->has('id')) {
            $contract = Contract::find($request->id);
            $contract->update($data);
        } else {
            $contract = Contract::create($data);
        }

        return $contract;
    }

    public function saveContract( Request $request, $id = null )
    {
        $data = $request->except('_token');

        if ($request->hasFile('sent_file')) :
            $destinationPath = 'storage/contract-sent-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['sent_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        endif;

        if ($request->hasFile('signed_file')) :
            $destinationPath = 'storage/contract-signed-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['signed_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
        endif;

        $data['status'] = 1;
        $data['is_file'] = $request->has('is_file') && $request->is_file ? 1 : 0;
        if($data['is_file']) {
            $data['signature'] = $request->has('signature') ? 'Signed' : NULL;
            if ($request->has('signature')) {
                $data['signed_date'] = Carbon::now();
            }
        }

        if ($id) {
            $contract = Contract::find($id);
            $contract->update($data);
        } else {
            $contract = Contract::create($data);
        }

        return $contract;
    }
}