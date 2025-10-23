<?php

namespace App\Http\Controllers;

use App\Models\Province;

class ProvinceController extends Controller
{
    /**
     * Display the Netherlands province map with pest control services
     */
    public function index()
    {
        $provinces = Province::with('activePestControlServices')
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

        return view('welcome-map', compact('provinces'));
    }

}
