<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'auth est gérée par le middleware Sanctum sur la route
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_at' => ['required', 'date', 'after:now'],
            'party_size' => ['required', 'integer', 'min:1', 'max:20'],
            'special_requests' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array'],
            // Les plats utilisent des UUID string
            'items.*.dish_id' => ['required_with:items', 'uuid', 'exists:dishes,id'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'reservation_at.after' => 'La date de réservation doit être dans le futur.',
            'items.*.dish_id.uuid' => 'Le plat sélectionné est invalide (UUID requis).',
            'items.*.dish_id.exists' => 'Le plat sélectionné est introuvable.',
        ];
    }
}
