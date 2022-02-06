<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongResourceCollection extends ResourceCollection
{
    public $collects = SongResource::class;
    protected $preserveAllQueryParameters = true;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Preserve pagination data
        return $this->resource;
    }
}
