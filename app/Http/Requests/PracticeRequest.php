<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PracticeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'starts_at' => ['required','date'],
            'ends_at'   => ['nullable','date','after:starts_at'],
            'location'  => ['nullable','string','max:255'],
            'notes'     => ['nullable','string'],
        ];
    }
}
