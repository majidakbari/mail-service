<?php

namespace App\Http\Requests;

/**
 * Class SendMultipleEmailRequest
 * @package App\Http\Requests
 */
class SendMultipleEmailRequest extends SendSingleEmailRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['to'] = 'required|array';
        $rules['to.*'] = 'required|email';

        return $rules;
    }
}
