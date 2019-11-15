<?php

namespace App\Http\Requests\Email;

/**
 * Class SendMultipleEmailRequest
 * @package App\Http\Requests\Email
 */
class SendMultipleEmailRequest extends SendSingleEmailRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['to'] = 'required|array';
        $rules['to.*'] = 'required|email';

        return $rules;
    }
}
