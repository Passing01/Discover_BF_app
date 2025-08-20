<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Geocoding\NominatimGeocoder;

class TaxiController extends Controller
{
    public function index()
    {
        $taxis = Taxi::query()->where('available', true)->paginate(12);
        return view('transport.taxi.index', compact('taxis'));
    }

    public function createRide(Taxi $taxi)
    {
        abort_unless($taxi->available, 404);
        return view('transport.taxi.create_ride', compact('taxi'));
    }

    public function storeRide(Request $request, Taxi $taxi)
    {
        abort_unless($taxi->available, 404);

        $data = $request->validate([
            'ride_date' => ['required','date'],
            'pickup_location' => ['required','string','max:255'],
            'dropoff_location' => ['required','string','max:255'],
            // Optional: we will try to compute it server-side
            'distance_km' => ['nullable','numeric','min:0.1'],
        ]);

        // Try server-side geocoding to compute a reliable distance
        $computedDistance = null;
        try {
            $geocoder = new NominatimGeocoder();
            $a = $geocoder->geocode($data['pickup_location']);
            $b = $geocoder->geocode($data['dropoff_location']);
            if ($a && $b) {
                $computedDistance = $this->haversineKm($a[0], $a[1], $b[0], $b[1]);
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        $provided = $request->input('distance_km');
        $finalDistance = null;
        if ($computedDistance !== null && $computedDistance > 0) {
            $finalDistance = round($computedDistance, 1);
            // override request distance silently or with a small flash message
            session()->flash('info', __('Distance recalculée automatiquement: :km km', ['km' => number_format($finalDistance, 1)]));
        } elseif (!is_null($provided) && is_numeric($provided) && (float)$provided > 0) {
            $finalDistance = (float) $provided;
        } else {
            // Neither computed nor provided: return with a helpful error
            return back()
                ->withInput()
                ->withErrors(['distance_km' => __('Impossible d\'estimer la distance. Veuillez préciser les adresses ou saisir la distance.')]);
        }

        $price = round($finalDistance * (float) $taxi->price_per_km, 2);

        $ride = Ride::create([
            'user_id' => Auth::id(),
            'taxi_id' => $taxi->id,
            'ride_date' => $data['ride_date'],
            'pickup_location' => $data['pickup_location'],
            'dropoff_location' => $data['dropoff_location'],
            'distance_km' => $finalDistance,
            'price' => $price,
            'status' => 'requested',
        ]);

        return redirect()->route('transport.taxi.ride.show', $ride);
    }

    /**
     * Compute Haversine distance in kilometers between two lat/lng.
     */
    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $la1 = deg2rad($lat1);
        $la2 = deg2rad($lat2);
        $a = sin($dLat/2) ** 2 + sin($dLng/2) ** 2 * cos($la1) * cos($la2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    public function showRide(Ride $ride)
    {
        if (!\Auth::check() || $ride->user_id !== \Auth::id()) {
            abort(403);
        }
        return view('transport.taxi.ride_show', compact('ride'));
    }
}
