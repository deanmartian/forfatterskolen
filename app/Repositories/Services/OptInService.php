<?php

namespace App\Repositories\Services;

use App\Http\AdminHelpers;
use App\OptIn;
use Illuminate\Http\Request;

class OptInService
{
    /**
     * Store the solution model
     *
     * @var OptIn
     */
    protected $optIn;

    /**
     * BlogService constructor.
     */
    public function __construct(OptIn $optIn)
    {
        $this->optIn = $optIn;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->optIn->find($id);
        }

        return $this->optIn->paginate($page);
    }

    /**
     * Create new record
     *
     * @param  $request  Request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $data = $request->all();

        if ($request->hasFile('pdf_file')) {
            $destinationPath = 'storage/opt-in-files'; // upload path
            $extensions = ['pdf'];
            $extension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['pdf_file']['name'], PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            if (in_array($extension, $extensions)) {
                $request->pdf_file->move($destinationPath, end($expFileName));
                $data['pdf_file'] = $fileName;
            }
        }

        return $this->optIn->create($data);
    }

    /**
     * Update record
     *
     * @param  $optIn  \Illuminate\Database\Eloquent\Model
     * @param  $request  Request
     */
    public function update($optIn, $request): bool
    {
        $data = $request->toArray();

        if ($request->hasFile('pdf_file')) {
            $destinationPath = 'storage/opt-in-files'; // upload path
            $extensions = ['pdf'];
            $extension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['pdf_file']['name'], PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            if (in_array($extension, $extensions)) {
                $request->pdf_file->move($destinationPath, end($expFileName));
                $data['pdf_file'] = $fileName;
            }
        }

        return $optIn->update($data);
    }

    /**
     * Delete record
     *
     * @param  $optIn  \Illuminate\Database\Eloquent\Model
     */
    public function destroy($optIn): bool
    {
        if ($optIn->forceDelete()) {
            return true;
        }

        return false;
    }
}
