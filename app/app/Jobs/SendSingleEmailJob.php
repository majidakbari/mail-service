<?php

namespace App\Jobs;

use App\Services\MailService;
use App\ValueObjects\Email;
use App\ValueObjects\MailProvider;
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
     * @var int
     */
    private $providerIndex = 1;

    /**
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
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
    public function getProviderIndex(): int
    {
        return $this->providerIndex;
    }

    /**
     * @param int $providerIndex
     */
    public function setProviderIndex(int $providerIndex): void
    {
        $this->providerIndex = $providerIndex;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailProvider = $this->getProvider();

        (new MailService($mailProvider))->send($this->getEmail());
    }

    /**
     * @return MailProvider|null
     */
    private function getProvider()
    {
        foreach (config('mail.providers') as $key => $mailProvider) {
            if ($key == 'sandgrid') {
                return MailProvider::fromArray($mailProvider);
            }
        }
        //Means that non of the providers could send the email
        return null;
    }
}
