<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Province;
use App\Models\Town;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * Display the active country's province map with pest control services
     * Currently shows Netherlands only, future: IP-based country detection
     */
    public function index()
    {
        // Get the active country (currently only Netherlands is active)
        // TODO: In the future, use IP geolocation to automatically determine user's country
        $activeCountry = Country::where('is_active', true)->first();

        if (!$activeCountry) {
            abort(404, 'No active country found. Please activate a country first.');
        }

        // Load ONLY the provinces for this active country
        $provinces = $activeCountry->provinces()
            ->with('activePestControlServices')
            ->get()
            ->mapWithKeys(function ($province) {
                return [$province->name => [
                    'name' => $province->name,
                    'population' => $province->population,
                    'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'address' => $service->address,
                            'phone' => $service->phone,
                            'email' => $service->email,
                            'specialty' => $service->specialty,
                            'rating' => (float) $service->rating,
                            'review_count' => $service->review_count,
                        ];
                    })
                ]];
            });

        return view('welcome-map', compact('provinces', 'activeCountry'));
    }

    /**
     * Search for towns/cities and return with province info
     */
    public function searchTowns(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $towns = Town::with('province')
            ->where('name', 'like', $query . '%')
            ->orderBy('population', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($town) {
                return [
                    'id' => $town->id,
                    'name' => $town->name,
                    'population' => $town->population,
                    'province_name' => $town->province->name,
                ];
            });

        return response()->json($towns);
    }

}
