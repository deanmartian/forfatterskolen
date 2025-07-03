<?php

namespace App\Repositories;

use App\Http\AdminHelpers;
use App\Testimonial;
use Illuminate\Http\Request;

class TestimonialRepository extends BaseRepository
{
    public $validationRules = [
        'name' => 'required',
        'description' => 'required',
        'testimony' => 'required',
    ];

    public function __construct(Testimonial $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  null  $id
     * @param  $request  Request
     * @return mixed
     */
    public function createOrUpdate($id, $request)
    {
        $model = is_null($id) ? new $this->model : $this->findOrFail($id);
        $model->name = $request->name;
        $model->description = $request->description;
        $model->testimony = $request->testimony;

        $destinationPath = 'storage/testimonials'; // upload path
        if ($request->hasFile('author_image') && $request->file('author_image')->isValid()) {
            $extension = pathinfo($_FILES['author_image']['name'], PATHINFO_EXTENSION); // getting document extension

            $actual_name = pathinfo($request->author_image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            $request->author_image->move($destinationPath, end($expFileName));
            $model->author_image = $fileName;
        }

        if ($request->hasFile('book_image') && $request->file('book_image')->isValid()) {
            $extension = pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION); // getting document extension

            $actual_name = pathinfo($request->book_image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            $request->book_image->move($destinationPath, end($expFileName));
            $model->book_image = $fileName;
        }

        if ($request->has('status')) {
            $model->status = $model::ACTIVE;
        } else {
            $model->status = $model::INACTIVE;
        }

        if (! $model->save()) {
            return false;
        }

        return true;
    }

    /**
     * Delete testimonial
     */
    public function destroy($id): bool
    {
        $testimonial = $this->find($id);
        if (\File::exists($testimonial->author_image)) {
            \File::delete($testimonial->author_image);
        }

        if (\File::exists($testimonial->book_image)) {
            \File::delete($testimonial->book_image);
        }

        if (! $this->delete($id)) {
            return false;
        }

        return true;
    }
}
