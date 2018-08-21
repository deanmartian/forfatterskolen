<?php
namespace App\Repositories\Services;

use App\OptIn;
use Illuminate\Http\Request;

class OptInService {

    /**
     * Store the solution model
     * @var OptIn
     */
    protected $optIn;

    /**
     * BlogService constructor.
     * @param OptIn $optIn
     */
    public function __construct(OptIn $optIn)
    {
        $this->optIn = $optIn;
    }

    /**
     * @param null $id
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = NULL, $page = 15)
    {
        if ($id) {
            return $this->optIn->find($id);
        }
        return $this->optIn->paginate($page);
    }

    /**
     * Create new record
     * @param $request Request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $data = $request->all();
        return $this->optIn->create($data);
    }

    /**
     * Update record
     * @param $optIn \Illuminate\Database\Eloquent\Model
     * @param $request Request
     * @return bool
     */
    public function update($optIn, $request)
    {
        $data = $request->toArray();
        return $optIn->update($data);
    }

    /**
     * Delete record
     * @param $optIn \Illuminate\Database\Eloquent\Model
     * @return bool
     */
    public function destroy($optIn)
    {
        if ($optIn->forceDelete()) {
            return true;
        }
        return false;
    }
}