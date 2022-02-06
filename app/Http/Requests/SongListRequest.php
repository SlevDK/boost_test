<?php

namespace App\Http\Requests;

class SongListRequest extends ApiRequest
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
            'order_by' => 'string|in:created_at,email,duration',
            'order_direction' => 'string|in:asc,ASC,desc,DESC',
        ];
    }
}