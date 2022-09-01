<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => app()->getLocale() === 'ar' ? $this->ar_title : $this->en_title,
            'description' => app()->getLocale() === 'ar' ? $this->ar_description : $this->en_description,
            'image' => $this->image,
            'date' => $this->date
        ];
        // parent::toArray($request);
    }
}