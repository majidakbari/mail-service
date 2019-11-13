<?php

namespace App\Jobs;

use App\Entities\Log;
use App\Services\LogService;
use App\Services\MailService;
use App\ValueObjects\Email;
use App\ValueObjects\MailProvider;
use App\ValueObjects\QueueManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class SendSingleEmailJob
 * @package App\Jobs]
 */
class SendSingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var Email
     */
    private $email;

    /**
     * Specifies which provider (from config/mail.php) has been chosen as the SMTP relay.
     * The default value equals to 0 that shows the first provider is our default SMTP provider.
     * If any task fails, this value will be increased to use the other providers
     * @var int
     */
    private $providerKey;

    /**
     *
     * @param Email $email
     * @param int $providerKey
     */
    public function __construct(Email $email, $providerKey = 0)
    {
        $this->email = $email;
        $this->providerKey = $providerKey;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getProviderKey(): int
    {
        return $this->providerKey;
    }

    /**
     * Execute the job.
     * @param LogService $logService
     * @return void
     * @throws Exception
     */
    public function handle(LogService $logService): void
    {
        if (is_null($mailProvider = $this->getProvider())) {
            $logService->fail($this->getEmail(), Log::NO_PROVIDERS);

            return;
        }

        (new MailService($mailProvider))->send($this->getEmail());
        $logService->success($this->getEmail(), $mailProvider->getId());
    }

    /**
     * @return MailProvider|null
     */
    private function getProvider()
    {
        foreach (config('mail.providers') as $key => $mailProvider) {
            if ($key == $this->getProviderKey()) {
                return MailProvider::fromArray($mailProvider);
            }
        }
        //Means that non of the providers could send the email
        return null;
    }

    /**
     * @param LogService $logService
     * @param Exception|null $exception
     */
    public function failed(LogService $logService, Exception $exception = null)
    {
        $logService->fail($this->getEmail(),$this->getProvider()->getId(), $exception->getMessage());

        dispatch(new static($this->getEmail(),
            $this->getProviderKey() + 1))->onQueue(QueueManager::FAILED_EMAIL_QUEUE);
    }
}
