<?php

namespace App\Http\Requests\Email;

use App\Rules\Base64Validator;
use App\ValueObjects\Email;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SendSingleEmailRequest
 * @package App\Http\Requests\Email
 */
class SendSingleEmailRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'to' => 'required|array|min:1|max:100',
            'to.*' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'bodyType' => ['required', Rule::in(Email::getValidBodyTypes())],
            'attachFileCode' => ['nullable', new Base64Validator()],
            'attachFileName' => 'required_with:attachFileCode|max:100',
            'cc' => 'nullable|array',
            'cc.*' => 'nullable|email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'nullable|email',
            'fromAddress' => 'required|email',
            'fromName' => 'required|string|max:255',

        ];
    }
}
