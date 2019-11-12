<?php

namespace App\Http\Requests;

use App\Rules\Base64Validator;
use App\ValueObjects\Email;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SendSingleEmailRequest
 * @package App\Http\Requests
 */
class SendSingleEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
            'bodyType' => ['required', Rule::in(Email::getValidBodyTypes())],
            'attachFileCode' => ['nullable', new Base64Validator()],
            'attachFileName' => 'required_if:attachFileCode,1|string|max:100',
            'cc' => 'nullable|array',
            'cc.*' => 'nullable|email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'nullable|email'
        ];
    }
}
