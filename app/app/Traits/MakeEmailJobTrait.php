<?php

namespace App\Traits;

use App\Jobs\SendSingleEmailJob;
use App\ValueObjects\Email;

/**
 * Trait MakeEmailJobTrait
 * @package App\Traits
 */
trait MakeEmailJobTrait
{
    /**
     * @param array $data
     * @return array
     */
    private function makeJobFromArray(array $data): array
    {
        $jobs = [];

        foreach ($data['to'] as $email) {
            $data['to'] = $email;
            $jobs[] = new SendSingleEmailJob(Email::fromArray($data));
        }

        return $jobs;
    }
}
