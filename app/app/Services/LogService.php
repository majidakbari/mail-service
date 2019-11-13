<?php

namespace App\Services;

use App\Entities\Log;
use App\Interfaces\LogRepositoryInterface;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LogService
 * @package App\Services
 */
class LogService
{
    /**
     * @var LogRepositoryInterface
     */
    private $logRepository;

    /**
     * LogService constructor.
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param Email $email
     * @param int|string $providerId
     * @return Log|Model
     */
    public function success(Email $email, $providerId)
    {
        return $this->writeLogs($email, $providerId);
    }

    /**
     * @param Email $email
     * @param int|string $providerId
     * @return Log|Model
     */
    public function fail(Email $email, $providerId)
    {
        return $this->writeLogs($email, $providerId, false);
    }

    /**
     * @param Email $email
     * @param int|string $providerId
     * @param bool $success
     * @return Log|Model
     */
    public function writeLogs(Email $email, $providerId, $success = true)
    {
        $log = (new Log())->fill([
            'to' => $email->getTo(),
            'body' => $email->getBody(),
            'email_metadata' => $email->getMetaData(),
            'provider' => $providerId,
            'sent_at' => $success ? now() : null,
            'failed_at' => $success ? null : now()
        ]);

        return $this->logRepository->save($log);
    }
}
