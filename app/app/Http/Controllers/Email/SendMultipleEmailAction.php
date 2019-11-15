<?php

namespace App\Http\Controllers\Email;

use App\Http\Requests\SendMultipleEmailRequest;
use App\Jobs\SendSingleEmailJob;
use App\Tools\APIResponse;
use App\ValueObjects\Email;
use App\ValueObjects\QueueManager;
use http\Exception;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SendMultipleEmailAction
 * @package App\Http\Controllers\Email
 */
class SendMultipleEmailAction
{
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
     * Because we need separate and detailed logs for every single email, we will process even bulk emails one by one;
     * But for the sake of not reducing the queue performance, we will put all of them in the queue by just one call
     * @param SendMultipleEmailRequest $request
     * @return JsonResponse
     */
    public function __invoke(SendMultipleEmailRequest $request): JsonResponse
    {
        throw new \Exception;
        $this->push($request->all());

        return APIResponse::success(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array $data
     */
    private function push(array $data)
    {
        $jobs = [];
        foreach ($data['to'] as $email) {
            $data['to'] = $email;
            $jobs[] = new SendSingleEmailJob(Email::fromArray($data));
        }

        $this->queueFactory->bulk($jobs, [], QueueManager::BULK_EMAIL_QUEUE);
    }
}
