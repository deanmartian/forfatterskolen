<?php

namespace App\Services;

use App\Contract;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Project;
use App\ProjectActivity;
use App\ProjectAudio;
use App\ProjectBook;
use App\ProjectBookFormatting;
use App\ProjectBookPicture;
use App\ProjectEbook;
use App\ProjectGraphicWork;
use App\ProjectMarketing;
use App\ProjectPrint;
use App\ProjectWholeBook;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectService
{
    /**
     * @return $this|mixed
     */
    public function saveProject(Request $request)
    {
        $model = $request->id ? Project::find($request->id) : new Project;
        $model->user_id = $request->user_id;
        $model->name = $request->name;
        $model->identifier = $request->number;
        $model->activity_id = $request->activity_id;
        $model->start_date = $request->start_date;
        $model->end_date = $request->end_date;
        $model->description = $request->description;
        $model->status = $request->status;
        $model->editor_id = $request->editor_id;
        $model->notes = null;
        $model->save();

        if ($request->user_id) {
            $model->books()->update([
                'user_id' => $request->user_id,
            ]);

            $model->copyEditings()->update([
                'user_id' => $request->user_id,
            ]);
        }

        return $model->load('user');
    }

    public function saveActivity(Request $request): ProjectActivity
    {
        $model = $request->id ? ProjectActivity::find($request->id) : new ProjectActivity;
        $model->activity = $request->activity;
        $model->project_id = $request->project_id ?: null;
        $model->description = $request->description;
        $model->invoicing = $request->invoicing;
        $model->hourly_rate = $request->hourly_rate;
        $model->save();

        return $model;
    }

    public function saveBook(Request $request): array
    {
        $model = $request->id ? ProjectBook::find($request->id) : new ProjectBook;
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
            'project' => $project,
        ];
    }

    public function saveBookPicture(Request $request)
    {
        if ($request->hasFile('images')) {
            /* $destinationPath = 'storage/project-book-pictures'; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveMultipleFileOrImage($destinationPath, 'images'); */
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/book-pictures/';
            $filePath = $this->saveMultipleFileOrImageDropbox($destinationPath, 'images');
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
                        'description' => $request->description,
                    ]);
                }
            }
        }
    }

    public function saveBookFormatting(Request $request)
    {

        $filePath = null;
        $corporatePage = null;
        $formatImage = null;

        if ($request->hasFile('file')) {
            // $destinationPath = 'storage/project-book-formatting'; // upload path

            // AdminHelpers::createDirectory($destinationPath);
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/book-formatting/';
            $filePath = $this->saveMultipleFileOrImageDropbox($destinationPath, 'file');
        }

        if ($request->hasFile('corporate_page')) {
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id
            .'/graphic-work/book-formatting/corporate_page';
            $corporatePage = $this->saveFileOrImageDropbox($destinationPath, 'corporate_page');
        }

        if ($request->format && $request->hasFile('format_image')) {
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id
                .'/graphic-work/book-formatting/format_image';
            $formatImage = $this->saveFileOrImageDropbox($destinationPath, 'format_image');
        }

        $format = $request->input('format');
        $customFormat = $request->width.'x'.$request->height;

        // If "Other" is selected, use the custom format
        if ((empty($format) || is_null($format)) && ! empty($request->width) && ! empty($request->height)) {
            $finalFormat = $customFormat;
        } else {
            // Use the selected predefined format
            $finalFormat = $format;
        }

        if ($request->id) {
            $bookPicture = ProjectBookFormatting::find($request->id);
            $bookPicture->file = $filePath ?? $bookPicture->file;
            $bookPicture->corporate_page = $corporatePage ?? $bookPicture->corporate_page;
            $bookPicture->designer_id = $request->designer_id ?? $bookPicture->designer_id;
            $bookPicture->format = $finalFormat ?? $bookPicture->format;
            $bookPicture->format_image = $formatImage ?? $bookPicture->format_image;
            $bookPicture->description = $request->description ?? $bookPicture->description;
            $bookPicture->save();

        } else {

            $bookPicture = ProjectBookFormatting::create([
                'project_id' => $request->project_id,
                'file' => $filePath,
                'corporate_page' => $corporatePage,
                'designer_id' => $request->has('designer_id') ? $request->designer_id : null,
                'format' => $finalFormat,
                'format_image' => $formatImage,
                'description' => $request->description,
            ]);

        }

        if ($bookPicture->designer_id) {
            $emailTemplate = AdminHelpers::emailTemplate('Graphic Designer Notification');
            $user = User::find($bookPicture->designer_id);
            $to = $user->email;

            $loginLink = route('giutbok.login.emailRedirect', [encrypt($user->email), encrypt(route('g-admin.dashboard'))]);
            $searchString = [
                ':login_link',
            ];

            $replaceString = [
                "<a href='$loginLink'>Klikk her for å logge inn</a>",
            ];

            $emailContent = str_replace($searchString, $replaceString, $emailTemplate->email_content);

            dispatch(new AddMailToQueueJob($to, $emailTemplate->subject, $emailContent,
                $emailTemplate->from_email, null, null,
                'admin', $user->id));
        }

        return $bookPicture;

    }

    public function saveBookFormatFeedback(Request $request)
    {
        $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/book-formatting/feedback';
        $filePath = $this->saveFileOrImageDropbox($destinationPath, 'file');

        $bookPicture = ProjectBookFormatting::find($request->id);
        $bookPicture->feedback = $filePath;
        $bookPicture->feedback_status = 'pending';
        $bookPicture->save();
    }

    public function saveOtherService($project_id, Request $request): string
    {
        $filePath = null;
        $calculatedPrice = 0;

        if ($request->has('manuscript')) {
            $filePath = $this->saveFile($project_id, $request);
            $calculatedPrice = $this->calculateFileTextPrice($filePath, $request->is_copy_editing);
        }

        $manuType = 'Correction';
        if ($request->is_copy_editing == 1) {
            $manuType = 'Copy Editing';
            CopyEditingManuscript::create([
                'user_id' => $request->user_id,
                'project_id' => $request->project_id,
                'file' => $filePath,
                'payment_price' => $calculatedPrice,
                'editor_id' => $request->exists('editor_id') ? $request->editor_id : null,
            ]);
        } else {
            CorrectionManuscript::create([
                'user_id' => $request->user_id,
                'project_id' => $request->project_id,
                'file' => $filePath,
                'payment_price' => $calculatedPrice,
                'editor_id' => $request->exists('editor_id') ? $request->editor_id : null,
            ]);
        }

        return $manuType;
    }

    public function saveFile($project_id, Request $request): string
    {
        $extension = $request->manuscript->extension();
        $destinationPath = 'Forfatterskolen_app/project/project-'.$project_id.'/correction-manuscripts'; // upload path

        if ($request->type == 1) {
            $destinationPath = 'Forfatterskolen_app/project/project-'.$project_id.'/copy-editing-manuscripts'; // upload path
        }

        $time = time();
        $fileName = $time.'.'.$extension; // $original_filename; // rename document

        // $request->manuscript->move($destinationPath, $fileName);
        return $this->saveFileOrImageDropbox($destinationPath, 'manuscript');
    }

    public function calculateFileTextPrice($file, $is_copy_editing): int
    {

        $word_count = AdminHelpers::dropboxFileCountWords($file, basename($file));
        /* $docObj = new FileToText($file);
        // count characters with space
        $word_count = strlen($docObj->convertToText()) - 2; */

        $word_per_price = 1000;
        $price_per_word = 25;

        if ($is_copy_editing == 1) {
            $word_per_price = 1000;
            $price_per_word = 30;
        }

        $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);
        $calculated_price = ($rounded_word / $word_per_price) * $price_per_word;

        return $calculated_price;
    }

    /**
     * Update project relations
     *
     * @param  Request  $request
     * @return mixed
     */
    public function updateUserInProjectChildren($project_id, $user_id)
    {
        $project = Project::find($project_id);
        $project->update(['user_id' => $user_id]);
        $project->copyEditings()->update([
            'user_id' => $user_id,
        ]);

        return $project;
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function saveGraphicWorks(Request $request)
    {
        $data = $request->except('_token');

        switch ($request->type) {
            case 'cover':
                $destinationPathCover = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/cover/';
                if (\request()->hasFile('cover')) {
                    $data['value'] = $this->saveMultipleFileOrImageDropbox($destinationPathCover, 'cover');
                }

                // $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'cover');
                // $data['description'] = $this->saveGraphicWorkFileOrImage($request, 'interior', null, true);
                $data['is_checked'] = $request->has('is_approved') && $request->is_approved ? 1 : 0;
                $format = $request->input('cover_format');
                $customFormat = $request->cover_width.'x'.$request->cover_height;

                // If "Other" is selected, use the custom format
                if ((empty($format) || is_null($format)) && ! empty($request->cover_width) && ! empty($request->cover_height)) {
                    $finalFormat = $customFormat;
                } else {
                    // Use the selected predefined format
                    $finalFormat = $format;
                }
                $data['format'] = $finalFormat;

                if (\request()->hasFile('backside_image')) {
                    $destinationPathCover = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/cover/backside_image/';
                    $data['backside_image'] = $this->saveMultipleFileOrImageDropbox($destinationPathCover, 'backside_image');
                }

                if (! \request()->has('backside_type')) {
                    if (\request()->hasFile('backside_file')) {
                        $data['backside_text'] = $this->saveGraphicWorkFileOrImage($request, 'backside_file', 'cover/');
                    } else {
                        if ($request->id) {
                            $graphicWork = ProjectGraphicWork::find($request->id);
                            $data['backside_text'] = $graphicWork->backside_text;
                        }
                    }
                }
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

            case 'print-ready':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'print_ready');
                $data['print_ready'] = null;

                $format = $request->input('format');
                $customFormat = $request->width.'x'.$request->height;

                // If "Other" is selected, use the custom format
                if ((empty($format) || is_null($format)) && ! empty($request->width) && ! empty($request->height)) {
                    $finalFormat = $customFormat;
                } else {
                    // Use the selected predefined format
                    $finalFormat = $format;
                }
                $data['format'] = $finalFormat;
                break;

            case 'sample-book-pdf':
                $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'sample_book_pdf');
                break;

            case 'indesign':
                // $data['value'] = $this->saveGraphicWorkFileOrImage($request, 'cover', 'indesign/');
                if (\request()->hasFile('cover')) {
                    $destinationPathCover = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/indesign/cover/';
                    $data['value'] = $this->saveMultipleFileOrImageDropbox($destinationPathCover, 'cover');
                }

                if (\request()->hasFile('interior')) {
                    $data['description'] = $this->saveGraphicWorkFileOrImage($request, 'interior', 'indesign/', true);
                }
                break;

            case 'cover-print-ready':
                foreach ($data as $key => $value) {
                    if (is_null($value)) {
                        unset($data[$key]);
                    }
                }
                
                $data['type'] = 'cover';
                $data['print_ready'] = $this->saveGraphicWorkFileOrImage($request, 'cover_print_ready', 'cover/', true);
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

    public function saveEbook(Request $request)
    {
        $data = $request->except('_token');

        switch ($request->type) {
            case 'epub':
                $data['value'] = $this->saveEbookFile($request, 'epub');
                break;

            case 'mobi':
                $data['value'] = $this->saveEbookFile($request, 'mobi');
                break;

            case 'cover':
                $data['value'] = $this->saveEbookFile($request, 'cover');
                break;
        }

        if ($request->id) {
            $ebook = ProjectEbook::find($request->id);
            $ebook->update($data);
        } else {
            $ebook = ProjectEbook::create($data);
        }

        return $ebook;
    }

    public function saveAudio(Request $request)
    {
        $data = $request->except('_token');

        switch ($request->type) {
            case 'files':
                $data['value'] = $this->saveAudioFile($request, 'files');
                break;

            case 'cover':
                $data['value'] = $this->saveAudioFile($request, 'cover');
                break;
        }

        if ($request->id) {
            $audio = ProjectAudio::find($request->id);
            $audio->update($data);
        } else {
            $audio = ProjectAudio::create($data);
        }

        return $audio;
    }

    public function savePrint(Request $request)
    {
        $data = $request->except('_token');

        return ProjectPrint::updateOrCreate([
            'project_id' => $data['project_id'],
        ], $data);
    }

    public function saveGraphicWorkFileOrImage(Request $request, $fieldName, $additionFolder = null, $isDescription = false): ?string
    {
        $filePath = null;

        if ($request->id) {
            $graphicWork = ProjectGraphicWork::find($request->id);
            $filePath = $isDescription ? $graphicWork->description : $graphicWork->value;
        }

        if ($request->hasFile($fieldName)) {
            // $destinationPath = 'storage/project-graphic-work/' . $fieldName; // upload path
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/graphic-work/'
                .$additionFolder.$fieldName; // upload path

            // AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImageDropbox($destinationPath, $fieldName);

        }

        return $filePath;
    }

    public function saveEbookFile(Request $request, $fieldName)
    {
        $filePath = null;

        if ($request->id) {
            $ebook = ProjectEbook::find($request->id);
            $filePath = $ebook->value;
        }

        if ($request->hasFile($fieldName)) {
            // $destinationPath = 'storage/project-graphic-work/' . $fieldName; // upload path
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/ebook/'.$fieldName; // upload path

            // AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImageDropbox($destinationPath, $fieldName);

        }

        return $filePath;
    }

    public function saveAudioFile(Request $request, $fieldName)
    {
        $filePath = null;

        if ($request->id) {
            $audio = ProjectAudio::find($request->id);
            $filePath = $audio->value;
        }

        if ($request->hasFile($fieldName)) {
            // $destinationPath = 'storage/project-graphic-work/' . $fieldName; // upload path
            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/audio/'.$fieldName; // upload path

            // AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImageDropbox($destinationPath, $fieldName);

        }

        return $filePath;
    }

    public function uploadWholeBook($project_id, Request $request)
    {
        $filePath = null;

        if ($request->id) {
            $wholeBook = ProjectWholeBook::find($request->id);
            $filePath = $wholeBook->book_content;
        }

        if ($request->hasFile('book_file')) {
            $destinationPath = 'Forfatterskolen_app/project/project-'.$project_id.'/project-books'; // upload path

            if ($request->has('is_book_critique')) {
                $destinationPath = 'Forfatterskolen_app/project/project-'.$project_id.'/project-book-critique'; // upload path
            }

            // AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImageDropbox($destinationPath, 'book_file');

        }

        return $filePath;
    }

    public function uploadFeedback(Request $request)
    {
        $filePath = null;
        $destinationPath = 'storage/project-book-critiques'; // upload path
        AdminHelpers::createDirectory($destinationPath);
        $filePath = $this->saveFileOrImage($destinationPath, 'feedback');

        return $filePath;
    }

    public function saveMarketingFileOrImage(Request $request, $fieldName): ?string
    {
        $filePath = null;

        if ($request->has('id') && $request->id) {
            $marketing = ProjectMarketing::find($request->id);
            $filePath = $marketing->value;
        }

        if ($request->hasFile($fieldName)) {
            $destinationPath = 'storage/project-marketing/'.$fieldName; // upload path

            AdminHelpers::createDirectory($destinationPath);
            $filePath = $this->saveFileOrImage($destinationPath, $fieldName);

        }

        return $filePath;
    }

    /**
     * @param  $requestFile
     */
    public function saveFileOrImage($destinationPath, $requestFilename): string
    {
        $requestFile = \request()->file($requestFilename);
        $extension = $requestFile->getClientOriginalExtension();
        $original_filename = $requestFile->getClientOriginalName();
        $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

        $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
        $requestFile->move($destinationPath, $fileName);

        return '/'.$fileName;
    }

    public function saveFileOrImageDropbox($destinationPath, $requestFilename)
    {
        $requestFile = \request()->file($requestFilename);
        $extension = $requestFile->getClientOriginalExtension();
        $original_filename = $requestFile->getClientOriginalName();
        $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

        $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension); // rename document
        $expFileName = explode('/', $fileName);
        $dropboxFileName = end($expFileName);

        $requestFile->storeAs($destinationPath, $dropboxFileName, 'dropbox');

        // remove the project_id in front which is numeric
        return '/'.$destinationPath.'/'.$dropboxFileName;
    }

    /**
     * @param  $requestFile
     */
    public function saveMultipleFileOrImage($destinationPath, $requestFilename): string
    {
        $filesWithPath = '';
        foreach (\request()->file($requestFilename) as $k => $file) {
            $extension = pathinfo($_FILES[$requestFilename]['name'][$k], PATHINFO_EXTENSION);
            $original_filename = $file->getClientOriginalName();
            $filename = pathinfo($original_filename, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $filename, $extension);
            $filesWithPath .= '/'.AdminHelpers::checkFileName($destinationPath, $filename, $extension).', ';

            $file->move($destinationPath, $fileName);
        }

        return $filesWithPath = trim($filesWithPath, ', ');
    }

    public function saveMultipleFileOrImageDropbox($destinationPath, $requestFilename)
    {
        $filesWithPath = '';
        foreach (\request()->file($requestFilename) as $k => $file) {
            $extension = pathinfo($_FILES[$requestFilename]['name'][$k], PATHINFO_EXTENSION);
            $original_filename = $file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension); // rename document
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            $filesWithPath .= '/'.$destinationPath.$fileName.', ';

            $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');
        }

        return $filesWithPath = trim($filesWithPath, ', ');
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function uploadContract(Request $request)
    {
        $data = $request->except('_token');
        if ($request->hasFile('sent_file')) {
            $destinationPath = 'storage/contract-sent-file/'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['sent_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        }

        if ($request->hasFile('signed_file')) {
            $destinationPath = 'storage/contract-signed-file/'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['signed_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
            $data['signed_date'] = Carbon::now();
            $data['signature'] = 'Signed';
        }

        $data['is_file'] = 1;

        if ($request->has('id')) {
            $contract = Contract::find($request->id);
            $contract->update($data);
        } else {
            $contract = Contract::create($data);
        }

        return $contract;
    }

    public function saveContract(Request $request, $id = null)
    {
        $data = $request->except('_token');

        if ($request->hasFile('sent_file')) {
            $destinationPath = 'storage/contract-sent-file/'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['sent_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        }

        if ($request->hasFile('signed_file')) {
            $destinationPath = 'storage/contract-signed-file/'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['signed_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
        }

        $data['status'] = 1;
        $data['is_file'] = $request->has('is_file') && $request->is_file ? 1 : 0;
        if ($data['is_file']) {
            $data['signature'] = $request->has('signature') ? 'Signed' : null;
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
