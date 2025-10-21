<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\PestControlService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvinceController extends Controller
{
    /**
     * Display the welcome page with provinces data
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

        return view('welcome', compact('provinces'));
    }

    /**
     * Get all provinces with their pest control services as JSON
     */
    public function getProvincesData(): JsonResponse
    {
        $provinces = Province::with('activePestControlServices')
            ->get()
            ->mapWithKeys(function ($province) {
                return [$province->name => [
                    'name' => $province->name,
                    'population' => $province->population,
                    'latitude' => (float) $province->latitude,
                    'longitude' => (float) $province->longitude,
                    'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'address' => $service->address,
                            'phone' => $service->phone,
                            'email' => $service->email,
                            'website' => $service->website,
                            'specialty' => $service->specialty,
                            'rating' => (float) $service->rating,
                            'review_count' => $service->review_count,
                            'latitude' => $service->latitude ? (float) $service->latitude : null,
                            'longitude' => $service->longitude ? (float) $service->longitude : null,
                        ];
                    })
                ]];
            });

        return response()->json($provinces);
    }

    /**
     * Get pest control services for a specific province
     */
    public function getProvinceServices(Request $request, $provinceName): JsonResponse
    {
        $province = Province::where('name', $provinceName)
            ->with('activePestControlServices')
            ->first();

        if (!$province) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        return response()->json([
            'name' => $province->name,
            'population' => $province->population,
            'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'address' => $service->address,
                    'phone' => $service->phone,
                    'email' => $service->email,
                    'website' => $service->website,
                    'specialty' => $service->specialty,
                    'rating' => (float) $service->rating,
                    'review_count' => $service->review_count,
                ];
            })
        ]);
    }

    /**
     * Search pest control services across all provinces
     */
    public function searchServices(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $specialty = $request->get('specialty', '');
        
        $servicesQuery = PestControlService::query()
            ->with('province')
            ->where('is_active', true);

        if ($query) {
            $servicesQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('specialty', 'like', "%{$query}%");
            });
        }

        if ($specialty) {
            $servicesQuery->where('specialty', 'like', "%{$specialty}%");
        }

        $services = $servicesQuery->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'address' => $service->address,
                'phone' => $service->phone,
                'email' => $service->email,
                'website' => $service->website,
                'specialty' => $service->specialty,
                'rating' => (float) $service->rating,
                'review_count' => $service->review_count,
                'province' => $service->province->name,
            ];
        });

        return response()->json($services);
    }
}
