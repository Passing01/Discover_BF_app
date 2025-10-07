<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'address' => $this->address,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'map_url' => $this->map_url,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'avg_price' => $this->avg_price,
            'rating' => $this->rating,
            'is_active' => (bool) $this->is_active,
            'cover_image' => $this->cover_image,
            'gallery' => $this->gallery,
            'video_urls' => $this->video_urls,
            // Include available dishes if relationship is loaded
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
        ];
    }
}
