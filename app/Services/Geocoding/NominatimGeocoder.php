<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Http;

class NominatimGeocoder
{
    /**
     * Geocode a free-form address using Nominatim (OpenStreetMap).
     * Returns [lat, lon] as floats on success, or null on failure.
     */
    public function geocode(string $query): ?array
    {
        try {
            $resp = Http::withHeaders([
                'User-Agent' => 'DiscoverBF/1.0 (+https://discoverbf.test)'
            ])->timeout(8)
              ->get('https://nominatim.openstreetmap.org/search', [
                  'q' => $query,
                  'format' => 'json',
                  'limit' => 1,
              ]);

            if (!$resp->ok()) {
                return null;
            }
            $data = $resp->json();
            if (!is_array($data) || empty($data)) {
                return null;
            }
            $first = $data[0] ?? null;
            if (!$first || !isset($first['lat'], $first['lon'])) {
                return null;
            }
            return [
                (float) $first['lat'],
                (float) $first['lon'],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
