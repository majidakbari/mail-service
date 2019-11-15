<?php

namespace App\Http\Controllers\Email;

use App\Http\Requests\Email\SendSingleEmailRequest;
use App\Tools\APIResponse;
use App\Traits\MakeEmailJobTrait;
use App\ValueObjects\QueueManager;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SendSingleEmailAction
 * @package App\Http\Controllers\Email
 */
class SendSingleEmailAction
{
    use MakeEmailJobTrait;

    /**
     * @var Queue
     */
    private $queueFactory;

    /**
     * SendMultipleEmailAction constructor.
     * @param Queue $queueFactory
     */
    public function __construct(Queue $queueFactory)
    {
        $this->queueFactory = $queueFactory;
    }

    /**
     * @param SendSingleEmailRequest $request
     * @return JsonResponse
     */
    public function __invoke(SendSingleEmailRequest $request): JsonResponse
    {
        $this->push($request->all());

        return APIResponse::success(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array $data
     */
    private function push(array $data)
    {
        $jobs = $this->makeJobFromArray($data);

        $this->queueFactory->bulk($jobs, [], QueueManager::BULK_EMAIL_QUEUE);
    }
}
