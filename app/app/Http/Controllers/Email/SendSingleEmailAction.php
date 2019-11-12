<?php

namespace App\Http\Controllers\Email;

use App\Http\Requests\SendSingleEmailRequest;
use App\Jobs\SendSingleEmailJob;
use App\Tools\APIResponse;
use App\ValueObjects\Email;
use App\ValueObjects\QueueManager;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SendSingleEmailAction
 * @package App\Http\Controllers\Email
 */
class SendSingleEmailAction
{
    /**
     * @param SendSingleEmailRequest $request
     * @return JsonResponse
     */
    public function __invoke(SendSingleEmailRequest $request): JsonResponse
    {
        dispatch(new SendSingleEmailJob(Email::fromArray($request->all())))->onQueue(QueueManager::SINGLE_EMAIL_QUEUE);

        return APIResponse::success(null, Response::HTTP_NO_CONTENT);
    }
}
