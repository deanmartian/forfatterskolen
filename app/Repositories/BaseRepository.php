<?php

namespace App\Repositories;

use DTApi\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $validationRules = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function validatorAttributeNames(): array
    {
        return [];
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     */
    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function with($array)
    {
        return $this->model->with($array);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findBySlug(string $slug): Model
    {

        return $this->model->where('slug', $slug)->first();

    }

    public function query(): Builder
    {
        return $this->model->query();
    }

    public function instance(array $attributes = []): Model
    {
        $model = $this->model;

        return new $model($attributes);
    }

    /**
     * @return mixed
     */
    public function paginate(?int $perPage = null)
    {
        return $this->model->paginate($perPage);
    }

    public function where($key, $where)
    {
        return $this->model->where($key, $where);
    }

    /**
     * @param  null  $rules
     */
    public function validator(array $data = [], $rules = null, array $messages = [], array $customAttributes = []): \Illuminate\Validation\Validator
    {
        if (is_null($rules)) {
            $rules = $this->validationRules;
        }

        return Validator::make($data, $rules, $messages, $customAttributes);
    }

    /**
     * @param  null  $rules
     *
     * @throws ValidationException
     */
    public function validate(array $data = [], $rules = null, array $messages = [], array $customAttributes = []): bool
    {
        $validator = $this->validator($data, $rules, $messages, $customAttributes);

        return $this->_validate($validator);
    }

    public function create(array $data = []): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data = []): Model
    {
        $instance = $this->findOrFail($id);
        $instance->update($data);

        return $instance;
    }

    /**
     * @throws \Exception
     */
    public function delete(int $id): Model
    {
        $model = $this->findOrFail($id);
        $model->delete();

        return $model;
    }

    /**
     * @throws ValidationException
     */
    protected function _validate(\Illuminate\Validation\Validator $validator): bool
    {
        if (! empty($attributeNames = $this->validatorAttributeNames())) {
            $validator->setAttributeNames($attributeNames);
        }

        if ($validator->fails()) {
            return false;
            throw (new ValidationException)->setValidator($validator);
        }

        return true;
    }
}
