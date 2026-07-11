<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePjokRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:60'],
            'code' => ['nullable', 'string', 'max:120'],
            'name' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ];
    }
}
