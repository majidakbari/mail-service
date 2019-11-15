<?php

namespace App\Repositories;

use App\Entities\Log;
use App\Interfaces\LogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MySQLLogRepository
 * @package App\Repositories
 */
class MySQLLogRepository extends MySQLAbstractRepository implements LogRepositoryInterface
{
    /**
     * MySQLLogRepository constructor.
     */
    public function __construct()
    {
        parent::__construct(new Log());
    }

    /**
     * @param $to
     * @param null|string $fromDate
     * @param null|string $toDate
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findManyLogsByEmailAndDate(
        $to,
        $fromDate = null,
        $toDate = null,
        $perPage = 10,
        $page = 1
    ): LengthAwarePaginator {
        $query = $this->entityClass->query()->where('to', $to);

        if ($fromDate) {
            $query->where(function (Builder $q) use ($fromDate) {
                $q->whereDate('sent_at', '>=', $fromDate)
                    ->orWhereDate('failed_at', '>=', $fromDate);
            });
        }
        if ($toDate) {
            $query->where(function (Builder $q) use ($toDate) {
                $q->whereDate('sent_at', '<=', $toDate)
                    ->orWhereDate('failed_at', '<=', $toDate);
            });
        }

        return $query->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $page);
    }
}
