<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'price_per_night' => $this->price_per_night,
            'description' => $this->description,
            'photo' => $this->photo,
            'capacity' => $this->capacity,
            'available' => $this->available,
        ];
    }
}