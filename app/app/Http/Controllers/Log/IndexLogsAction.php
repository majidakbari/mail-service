<?php

namespace App\Http\Controllers\Log;

use App\Http\Requests\Log\IndexLogsRequest;
use App\Interfaces\LogRepositoryInterface;
use App\Tools\APIResponse;
use Illuminate\Http\JsonResponse;

/**
 * Class IndexLogsAction
 * @package App\Http\Controllers\Log
 */
class IndexLogsAction
{
    /**
     * @var LogRepositoryInterface
     */
    private $logRepository;

    /**
     * IndexLogsAction constructor.
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param IndexLogsRequest $request
     * @return JsonResponse
     */
    public function __invoke(IndexLogsRequest $request): JsonResponse
    {
        list($perPage, $page) = get_paginate_params(
            $request->get('perPage'),
            $request->get('page')
        );

        $result = $this->logRepository->findManyLogsByEmailAndDate(
            $request->get('email'),
            $request->get('fromDate'),
            $request->get('toDate'),
            $perPage, $page
        );

        return APIResponse::success($result);
    }
}
