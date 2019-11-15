<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface LogRepositoryInterface
 * @package App\Interfaces
 */
interface LogRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * @param $to
     * @param null|string $fromDate
     * @param null|string $toDate
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findManyLogsByEmailAndDate($to, $fromDate = null, $toDate = null, $perPage = 10, $page = 1);
}
