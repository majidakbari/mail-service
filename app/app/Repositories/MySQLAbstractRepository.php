<?php

namespace App\Repositories;

use App\Interfaces\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MySQLAbstractRepository
 * @package App\Repositories
 */
class MySQLAbstractRepository implements AbstractRepositoryInterface
{
    /**
     * @var Model
     */
    protected $entityClass;

    /**
     * AbstractRepository constructor.
     * @param Model $entityClass
     */
    public function __construct(Model $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function save(Model $model): Model
    {
        $model->save();

        return $model;
    }

    /**
     * @param $id
     * @param bool $throwException
     * @return Model|null
     */
    public function findById($id, $throwException = false)
    {
        $q = $this->entityClass::query();

        return $throwException ? $q->findOrFail($id) : $q->find($id);
    }

    /**
     * @param Model $model
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * @param array $criteria
     * @param bool $throwException
     * @return Model|null
     */
    public function findOneByCriteria($criteria, $throwException = false)
    {
        $q = $this->entityClass->query()->where($criteria);

        return $throwException ? $q->firstOrFail() : $q->first();
    }
}
