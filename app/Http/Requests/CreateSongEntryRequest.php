<?php

namespace App\Http\Requests;

class CreateSongEntryRequest extends ApiRequest
{
    // Possibly can be replaced with custom rule
    const INT24_MAX = 2 ** 24; // 16777216

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
            'email' => 'required|email|exists:songs,email',
            'duration' => ['required', 'integer', 'min:1', 'max:'.self::INT24_MAX],
        ];
    }
}
