<?php

namespace App\Console\Commands;

use App\Jobs\SendSingleEmailJob;
use App\Rules\Base64Validator;
use App\ValueObjects\Email;
use App\ValueObjects\QueueManager;
use Illuminate\Console\Command;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Rule;

/**
 * Class SendEmailCommand
 * @package App\Console\Commands
 */
class SendEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send email';
    /**
     * @var Factory
     */
    private $validationFactory;

    /**
     * Create a new command instance.
     *
     * @param Factory $validationFactory
     */
    public function __construct(Factory $validationFactory)
    {
        parent::__construct();

        $this->validationFactory = $validationFactory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $to = $this->getRecipient();
        $subject = $this->getSubject();
        $body = $this->getBody();
        $bodyType = $this->getBodyType();
        $fromName = $this->getFromName();
        $fromAddress = $this->getFromAddress();
        $attachFileCode = $this->getAttachFile();
        $attachFileName = $this->getAttachFileName($attachFileCode);
        $cc = $this->getCc();
        $bcc = $this->getBcc();

        $email = Email::fromArray(
            compact('to', 'subject', 'body', 'bodyType', 'fromName', 'fromAddress', 'attachFileCode', 'attachFileName', 'cc', 'bcc')
        );

        dispatch(new SendSingleEmailJob($email))->onQueue(QueueManager::SINGLE_EMAIL_QUEUE);

        $this->info('The email was pushed into the application queue');
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getRecipient(): string
    {
        $email = $this->ask('Enter the recipient email address');
        $validator = $this->validationFactory->make(['email' => $email], ['email' => 'required|email']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getRecipient();
        }

        return $email;
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getSubject(): string
    {
        $subject = $this->ask('Enter the email subject');
        $validator = $this->validationFactory->make(['subject' => $subject], ['subject' => 'required|string|max:255']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getSubject();
        }

        return $subject;
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getBody(): string
    {
        $body = $this->ask('Enter the email body');
        $validator = $this->validationFactory->make(['body' => $body], ['body' => 'required|string']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getBody();
        }

        return $body;
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getBodyType(): string
    {
        $bodyType = $this->ask('Enter the email body type, one of the following properties: text/html, text/plain, text/markdown');
        $validator = $this->validationFactory->make(['bodyType' => $bodyType],
            ['bodyType' => ['required', Rule::in(Email::getValidBodyTypes())]]);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getBodyType();
        }

        return $bodyType;
    }


    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getFromName(): string
    {
        $fromName = $this->ask('Enter the email fromName property');
        $validator = $this->validationFactory->make(['fromName' => $fromName],
            ['fromName' => 'required|string']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getFromName();
        }

        return $fromName;
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getFromAddress(): string
    {
        $fromAddress = $this->ask('Enter the email fromAddress property');
        $validator = $this->validationFactory->make(['fromAddress' => $fromAddress],
            ['fromAddress' => 'required|email']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getFromAddress();
        }

        return $fromAddress;
    }

    /**
     * Recursive function for getting email input certainly
     * @return string
     */
    private function getAttachFile(): ?string
    {
        $attachFileCode = $this->ask('Enter the base64 encoded of file (this field is optional), press enter to skip');
        $validator = $this->validationFactory->make(['attachFileCode' => $attachFileCode],
            ['attachFileCode' => ['nullable', new Base64Validator()] ]);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getAttachFile();
        }

        return $attachFileCode;
    }

    /**
     * Recursive function for getting email input certainly
     * @param null $attachFileCode
     * @return string|null
     */
    private function getAttachFileName($attachFileCode = null): ?string
    {
        if (!empty($attachFileCode)) {
            $attachFileName = $this->ask('Enter the attached file name');
            $validator = $this->validationFactory->make(['attachFileName' => $attachFileName],
                ['attachFileName' => 'required|string|max:255']);

            if ($validator->fails()) {
                $this->warn($validator->errors()->first());
                return $this->getAttachFileName(true);
            }

            return $attachFileName;
        }

        return null;
    }


    /**
     * Recursive function for getting email input certainly
     * @return array
     */
    private function getCc(): array
    {
        $cc = $this->ask('Enter email cc (this field is optional), press enter to skip');
        $validator = $this->validationFactory->make(['cc' => $cc],
            ['cc' => 'nullable|email']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getCc();
        }

        return !empty($cc) ? [$cc] : [];
    }

    /**
     * Recursive function for getting email input certainly
     * @return array
     */
    private function getBcc(): ?array
    {
        $bcc = $this->ask('Enter email bcc (this field is optional), press enter to skip');
        $validator = $this->validationFactory->make(['bcc' => $bcc],
            ['bcc' => 'nullable|email']);

        if ($validator->fails()) {
            $this->warn($validator->errors()->first());
            return $this->getBcc();
        }

        return !empty($bcc) ? [$bcc] : [];
    }
}
