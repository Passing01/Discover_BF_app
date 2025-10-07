<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'address' => $this->address,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'stars' => $this->stars,
            'photo' => $this->photo,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
        ];
    }
}