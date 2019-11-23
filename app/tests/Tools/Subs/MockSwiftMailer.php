<?php

namespace Tests\Tools\Subs;

use Swift_Mailer;
use Swift_Mime_SimpleMessage;

/**
 * Class MockSwiftMailer
 * @package Tests\Tools\Subs
 */
class MockSwiftMailer extends Swift_Mailer
{
    /**
     * @inheritDoc
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        return 0;
    }
}
