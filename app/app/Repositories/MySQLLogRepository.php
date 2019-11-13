<?php

namespace App\Repositories;

use App\Entities\Log;
use App\Interfaces\LogRepositoryInterface;

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
}
