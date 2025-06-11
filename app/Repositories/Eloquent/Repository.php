<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var App
     */
    private $app;

    protected $model;

    /**
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param  int  $perPage
     * @param  array  $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param  string  $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (! $model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model->newQuery();
    }
}
