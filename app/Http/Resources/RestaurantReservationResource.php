<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantReservationResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'user_id' => $this->user_id,
            'reservation_at' => $this->reservation_at?->toIso8601String(),
            'party_size' => $this->party_size,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'special_requests' => $this->special_requests,
            'order_items' => $this->order_items,
            // Optionally include restaurant summary if loaded
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
        ];
    }
}
