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
            'total_duration'    => 'integer|min:0',
            'total_duration_condition'  => 'string|in:<,>,=,<=,>=',

            'order_by'          => 'string|in:id,name,email,duration,created_at',
            'order_direction'   => 'string|in:asc,ASC,desc,DESC',
        ];
    }
}
