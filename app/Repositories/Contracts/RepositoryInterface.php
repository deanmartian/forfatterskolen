<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    /**
     * @param  array  $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * @return mixed
     */
    public function create(array $data);

    /**
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * @return mixed
     */
    public function delete($id);

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * @param  array  $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);
}
