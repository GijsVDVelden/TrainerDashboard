<?php

namespace App\Http\Requests;

use App\Enums\PlayerPositions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'position'   => [ 'required', Rule::enum(PlayerPositions::class) ],
            'birth_date' => ['required', 'date'],
        ];
    }
}
