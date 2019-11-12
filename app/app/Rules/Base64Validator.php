<?php

namespace App\Rules;

use App\Tools\FileHelper;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Base64Validator
 * @package App\Rules
 */
class Base64Validator implements Rule
{
    /**
     * @var array
     */
    private $mimes;

    /**
     * @param array $mimes
     */
    public function __construct(array $mimes = [])
    {
        $this->mimes = $mimes;
    }

    /**
     * @return array
     */
    public function getMimes(): array
    {
        return !empty($this->mimes) ? $this->mimes : FileHelper::getAllValidMimeTypes();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_string($value)) {
            $fileHelper = new FileHelper($value);

            return in_array($fileHelper->getMimeType(), $this->getMimes());
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.base64');
    }
}
