<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface AbstractRepositoryInterface
 * @package App\Interfaces
 */
interface AbstractRepositoryInterface
{
    /**
     * @param Model $model
     * @return Model
     */
    public function save(Model $model);


    /**
     * @param Model $model
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Model $model);

    /**
     * @param $id
     * @param bool $throwException
     * @return Model|null
     */
    public function findById($id, $throwException = false);

    /**
     * @param array $criteria
     * @param bool $throwException
     * @return Model|null
     */
    public function findOneByCriteria($criteria, $throwException = false);
}
