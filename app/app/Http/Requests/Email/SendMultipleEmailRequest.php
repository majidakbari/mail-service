<?php

namespace App\Http\Requests\Email;

use App\Rules\Base64Validator;
use App\ValueObjects\Email;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SendMultipleEmailRequest
 * @package App\Http\Requests\Email
 */
class SendMultipleEmailRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array|min:1|max:100',
            'data.*.to' => 'required|array',
            'data.*.to.*' => 'required|email',
            'data.*.subject' => 'required|string|max:255',
            'data.*.body' => 'required|string',
            'data.*.bodyType' => ['required', Rule::in(Email::getValidBodyTypes())],
            'data.*.attachFileCode' => ['nullable', new Base64Validator()],
            'data.*.attachFileName' => 'required_with:attachFileCode|max:100',
            'data.*.cc' => 'nullable|array',
            'data.*.cc.*' => 'nullable|email',
            'data.*.bcc' => 'nullable|array',
            'data.*.bcc.*' => 'nullable|email',
            'data.*.fromAddress' => 'required|email',
            'data.*.fromName' => 'required|string|max:255',

        ];
    }
}
