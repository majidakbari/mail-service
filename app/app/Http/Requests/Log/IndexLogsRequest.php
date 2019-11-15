<?php

namespace App\Http\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class IndexLogsRequest
 * @package App\Http\Requests\Log
 */
class IndexLogsRequest extends FormRequest
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
            'email' => 'required|email',
            'fromDate' => 'nullable|date:Y-m-d',
            'toDate' => 'nullable|date:Y-m-d',
        ];
    }
}
